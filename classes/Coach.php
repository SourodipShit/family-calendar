<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/File.php';

class Coach
{
    /**
     * Add a new coach profile along with certifications and plans.
     */
    public static function add($data)
    {
        try {
            Database::getInstance()->beginTransaction();

            // Insert into coach_profiles
            $profileData = $data['profile'];
            $fields = array_keys($profileData);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($profileData);

            $sql = "INSERT INTO coach_profiles (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
            
            $profileId = Database::getInstance()->lastInsertId();
            
            // user_id is the foreign key reference for coach_id in child tables
            $userId = $profileData['user_id'];

            if (!empty($data['certifications'])) {
                self::addCertifications($userId, $data['certifications']);
            }
            
            if (!empty($data['plans'])) {
                self::addPlans($userId, $data['plans']);
            }

            Database::getInstance()->commit();
            return ["status" => "success", "id" => $profileId, "user_id" => $userId];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update an existing coach profile and their child records.
     */
    public static function update($id, $data)
    {
        try {
            Database::getInstance()->beginTransaction();

            $profileData = $data['profile'];
            $fields = [];
            $params = [];
            foreach ($profileData as $key => $value) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;

            $sql = "UPDATE coach_profiles SET " . implode(", ", $fields) . " WHERE id = ?";
            Database::runPrepared($sql, $params);
            
            // Fetch user_id to update child tables
            $stmt = Database::runPrepared("SELECT user_id FROM coach_profiles WHERE id = ?", [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$profile) throw new Exception("Coach profile not found");
            
            $userId = $profile['user_id'];

            // Replace certifications if provided in the update payload
            if (isset($data['certifications'])) {
                Database::runPrepared("DELETE FROM coach_certifications WHERE coach_id = ?", [$userId]);
                self::addCertifications($userId, $data['certifications']);
            }
            
            // Replace plans if provided in the update payload
            if (isset($data['plans'])) {
                Database::runPrepared("DELETE FROM coach_plans WHERE coach_id = ?", [$userId]);
                self::addPlans($userId, $data['plans']);
            }

            Database::getInstance()->commit();
            return ["status" => "success", "message" => "Updated successfully"];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Helper to add certifications. Handles file uploads using File.php.
     */
    private static function addCertifications($coachId, $certifications)
    {
        foreach ($certifications as $cert) {
            $imagePath = $cert['image'] ?? '';
            
            // Automatically handle file upload if 'file' array (like $_FILES format) is passed
            if (isset($cert['file']) && is_array($cert['file']) && $cert['file']['error'] === UPLOAD_ERR_OK) {
                $upload = File::upload($cert['file'], 'certifications');
                if ($upload['status'] === 'success') {
                    $imagePath = $upload['filePath'];
                }
            }

            $params = [
                $coachId, 
                $cert['name'] ?? '', 
                $cert['description'] ?? '', 
                $imagePath
            ];
            $sql = "INSERT INTO coach_certifications (coach_id, name, description, image) VALUES (?, ?, ?, ?)";
            Database::runPrepared($sql, $params);
        }
    }
    
    /**
     * Helper to add coach plans.
     */
    private static function addPlans($coachId, $plans)
    {
        foreach ($plans as $plan) {
            $plan['coach_id'] = $coachId;
            $fields = array_keys($plan);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($plan);

            $sql = "INSERT INTO coach_plans (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
        }
    }

    /**
     * Fetch a coach profile by ID, including user details, certifications, and plans.
     */
    public static function getById($id)
    {
        try {
            $sql = "SELECT cp.*, u.name as user_name, u.email, u.image as profile_image, cc.name as category_name
                    FROM coach_profiles cp
                    INNER JOIN users u ON cp.user_id = u.id
                    LEFT JOIN coach_categories cc ON cp.category_id = cc.id
                    WHERE cp.id = ?";
            
            $stmt = Database::runPrepared($sql, [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$profile) return ["status" => "error", "message" => "Coach profile not found."];
            
            $userId = $profile['user_id'];
            
            // Certifications
            $certStmt = Database::runPrepared("SELECT * FROM coach_certifications WHERE coach_id = ?", [$userId]);
            $certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Plans
            $planStmt = Database::runPrepared("SELECT * FROM coach_plans WHERE coach_id = ?", [$userId]);
            $plans = $planStmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                "status" => "success", 
                "data" => [
                    "profile" => $profile,
                    "certifications" => $certifications,
                    "plans" => $plans
                ]
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    /**
     * Fetch all coaches based on optional filters.
     */
    public static function getAll($filter = [])
    {
        try {
            $sql = "SELECT cp.*, u.name as user_name, u.email, u.image as profile_image, cc.name as category_name
                    FROM coach_profiles cp
                    INNER JOIN users u ON cp.user_id = u.id
                    LEFT JOIN coach_categories cc ON cp.category_id = cc.id
                    WHERE 1=1";
            $params = [];
            
            if (!empty($filter['category_id'])) {
                $sql .= " AND cp.category_id = ?";
                $params[] = $filter['category_id'];
            }
            
            if (!empty($filter['approval_status'])) {
                $sql .= " AND cp.approval_status = ?";
                $params[] = $filter['approval_status'];
            }
            
            $sql .= " ORDER BY cp.created_at DESC";
            
            $stmt = Database::runPrepared($sql, $params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    /**
     * Delete a coach profile and associated sub-records.
     */
    public static function delete($id)
    {
        try {
            Database::getInstance()->beginTransaction();
            
            $stmt = Database::runPrepared("SELECT user_id FROM coach_profiles WHERE id = ?", [$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profile) {
                $userId = $profile['user_id'];
                Database::runPrepared("DELETE FROM coach_certifications WHERE coach_id = ?", [$userId]);
                Database::runPrepared("DELETE FROM coach_plans WHERE coach_id = ?", [$userId]);
            }
            
            Database::runPrepared("DELETE FROM coach_profiles WHERE id = ?", [$id]);
            
            Database::getInstance()->commit();
            return ["status" => "success", "message" => "Coach profile deleted successfully"];
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update the approval status of a coach profile.
     */
    public static function updateStatus($id, $status)
    {
        try {
            $allowedStatuses = ['pending', 'approved', 'rejected', 'pending_edits'];
            if (!in_array($status, $allowedStatuses)) {
                return ["status" => "error", "message" => "Invalid status provided."];
            }

            Database::runPrepared("UPDATE coach_profiles SET approval_status = ? WHERE id = ?", [$status, $id]);
            return ["status" => "success", "message" => "Coach status updated successfully."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Fetch the count of pending coach profiles.
     */
    public static function getPendingCoachesCount()
    {
        try {
            $stmt = Database::runPrepared("SELECT COUNT(id) as count FROM coach_profiles WHERE approval_status = 'pending'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result['count']];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
