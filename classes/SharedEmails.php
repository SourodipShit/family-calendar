<?php
require_once __DIR__ . '/../config/Database.php';

class SharedEmails
{
    // Create
    public static function create($emailAddress, $password)
    {
        try {
            $result = Database::runPrepared("INSERT INTO family_shared_emails (email_address, password) VALUES (?, ?)", [
                $emailAddress,
                $password
            ]);
            return ['status' => 'success', 'message' => 'Shared email created successfully', 'id' => Database::getLastInsertId()];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to create shared email: ' . $e->getMessage()];
        }
    }

    // Read single
    public static function getById($id)
    {
        $stmt = Database::runPrepared("SELECT * FROM family_shared_emails WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read all
    public static function getAll()
    {
        $stmt = Database::run("SELECT * FROM family_shared_emails ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update
    public static function update($id, $emailAddress, $password, $familyId = null)
    {
        try {
            if ($familyId === '') $familyId = null;

            // Get current state to handle family email syncing
            $current = self::getById($id);
            $oldFamilyId = $current ? $current['family_id'] : null;
            
            if ($familyId === null) {
                Database::runPrepared("UPDATE family_shared_emails SET email_address = ?, password = ?, family_id = NULL, allocated_at = NULL WHERE id = ?", [
                    $emailAddress,
                    $password,
                    $id
                ]);
            } else {
                Database::runPrepared("UPDATE family_shared_emails SET email_address = ?, password = ?, family_id = ?, allocated_at = NOW() WHERE id = ?", [
                    $emailAddress,
                    $password,
                    $familyId,
                    $id
                ]);
            }

            // Sync email with the families table
            if ($oldFamilyId && $oldFamilyId != $familyId) {
                // If it was allocated to a family and is now unallocated or changed to another family, clear the old family's email
                Database::runPrepared("UPDATE families SET email = NULL WHERE id = ?", [$oldFamilyId]);
            }
            if ($familyId) {
                // Update the newly allocated family's email to this shared email address
                Database::runPrepared("UPDATE families SET email = ? WHERE id = ?", [$emailAddress, $familyId]);
            }

            return ['status' => 'success', 'message' => 'Shared email updated successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update shared email: ' . $e->getMessage()];
        }
    }

    // Delete
    public static function delete($id)
    {
        try {
            Database::runPrepared("DELETE FROM family_shared_emails WHERE id = ?", [$id]);
            return ['status' => 'success', 'message' => 'Shared email deleted successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to delete shared email: ' . $e->getMessage()];
        }
    }

    // Allocate an available email to a family
    public static function allocateFamily($familyId)
    {
        try {
            $freeEmail = Database::runPrepared("SELECT id FROM family_shared_emails WHERE family_id IS NULL LIMIT 1")->fetch(PDO::FETCH_ASSOC);

            if ($freeEmail) {
                Database::runPrepared("UPDATE family_shared_emails SET family_id = ?, allocated_at = NOW() WHERE id = ?", [
                    $familyId,
                    $freeEmail['id']
                ]);
                return ['status' => 'success', 'message' => 'Email allocated successfully', 'email_id' => $freeEmail['id']];
            }

            return ['status' => 'warning', 'message' => 'No shared emails available'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to allocate email: ' . $e->getMessage()];
        }
    }

    public static function getFamilyEmail($familyId)
    {
        try {
            $stmt = Database::runPrepared("SELECT email_address FROM family_shared_emails WHERE family_id = ?", [$familyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return ['status' => 'success', 'email' => $result['email_address']];
            }
            
            return ['status' => 'error', 'message' => 'No shared email found for this family'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to get family email: ' . $e->getMessage()];
        }
    }
}
