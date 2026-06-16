<?php
require_once __DIR__ . '/../config/Database.php';

class FamilyRequest
{
    // Create a new request (e.g. inviting a user to a family)
    public static function create($data)
    {
        $requester_id = $data['requester_id'];
        $family_id = $data['family_id'];
        $email = $data['email'];
        
        // Find if user already exists
        $user = Database::runPrepared("SELECT id FROM users WHERE email = ?", [$email])->fetch(PDO::FETCH_ASSOC);
        $receiver_id = $user ? $user['id'] : null;
        
        // Check if request already exists
        $existing = Database::runPrepared("SELECT id FROM family_requests WHERE family_id = ? AND email = ? AND status = 'pending'", [$family_id, $email])->fetch();
        if ($existing) {
            return ["status" => "error", "message" => "A pending request for this email already exists in this family."];
        }

        try {
            Database::runPrepared(
                "INSERT INTO family_requests (requester_id, family_id, email, receiver_id, status) VALUES (?, ?, ?, ?, 'pending')",
                [$requester_id, $family_id, $email, $receiver_id]
            );
            return ["status" => "success", "message" => "Request created successfully."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to create request: " . $e->getMessage()];
        }
    }

    // Get requests sent by/for a specific family
    public static function getByFamily($family_id)
    {
        return Database::runPrepared(
            "SELECT fr.*, u.name as requester_name 
             FROM family_requests fr 
             LEFT JOIN users u ON fr.requester_id = u.id 
             WHERE fr.family_id = ?",
            [$family_id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get requests addressed to a specific user's email
    public static function getByUserEmail($email)
    {
        return Database::runPrepared(
            "SELECT fr.*, f.name as family_name, u.name as requester_name 
             FROM family_requests fr 
             JOIN families f ON fr.family_id = f.id 
             LEFT JOIN users u ON fr.requester_id = u.id 
             WHERE fr.email = ?",
            [$email]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update the request status and act accordingly (e.g., adding user to family if approved)
    public static function updateStatus($id, $status)
    {
        try {
            $pdo = Database::getInstance();
            $pdo->beginTransaction();
            
            Database::runPrepared(
                "UPDATE family_requests SET status = ? WHERE id = ?",
                [$status, $id]
            );
            
            if ($status === 'approved') {
                // The requester and receiver are now linked.
                // We no longer insert the requester into user_family.
                // This ensures they only see shared events, not the full family calendar.
            }
            
            $pdo->commit();
            return ["status" => "success", "message" => "Request status updated successfully."];
        } catch (PDOException $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => "Database error: " . $e->getMessage()];
        }
    }

    // Delink a previously approved request
    public static function delink($id, $receiver_id)
    {
        try {
            $pdo = Database::getInstance();
            
            $req = Database::runPrepared("SELECT * FROM family_requests WHERE id = ? AND receiver_id = ?", [$id, $receiver_id])->fetch(PDO::FETCH_ASSOC);
            if (!$req) {
                return ["status" => "error", "message" => "Request not found or unauthorized."];
            }
            
            $pdo->beginTransaction();
            
            // Remove user from the family
            Database::runPrepared("DELETE FROM user_family WHERE user_id = ? AND family_id = ?", [$req['requester_id'], $req['family_id']]);
            
            // Delete the request
            Database::runPrepared("DELETE FROM family_requests WHERE id = ?", [$id]);
            
            $pdo->commit();
            return ["status" => "success", "message" => "User successfully de-linked from your family."];
        } catch (PDOException $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => "Database error: " . $e->getMessage()];
        }
    }

    // Delete a request
    public static function delete($id)
    {
        try {
            Database::runPrepared(
                "DELETE FROM family_requests WHERE id = ?",
                [$id]
            );
            return ["status" => "success", "message" => "Request deleted successfully."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to delete request: " . $e->getMessage()];
        }
    }
}
