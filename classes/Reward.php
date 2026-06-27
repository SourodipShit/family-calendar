<?php
require_once __DIR__ . "/../config/Database.php"; // Adjust path if Database.php is in /classes
require_once __DIR__ . "/File.php";
require_once __DIR__ . "/Points.php";

class Reward
{
    public static function add($data, $file = null)
    {
        try {
            $imagePath = null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadResult = File::upload($file, 'rewards');
                if ($uploadResult['status'] === 'success') {
                    $imagePath = $uploadResult['filePath'];
                } else {
                    return $uploadResult; // Return error from upload
                }
            }

            $sql = "INSERT INTO rewards (family_id, title, price, image) VALUES(?, ?, ?, ?)";
            $params = [
                $data['family_id'],
                $data['title'],
                $data['price'],
                $imagePath
            ];
            
            $result = Database::runPrepared($sql, $params);
            return ["status" => "success", "message" => "Reward added successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to add reward", "data" => $e->getMessage()];
        }
    }

    public static function edit($data, $file = null)
    {
        try {
            $imagePath = $data['existing_image'] ?? null;
            
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadResult = File::upload($file, 'rewards');
                if ($uploadResult['status'] === 'success') {
                    // Delete old image if a new one is uploaded
                    if ($imagePath && file_exists($imagePath)) {
                        File::deleteFile($imagePath);
                    }
                    $imagePath = $uploadResult['filePath'];
                } else {
                    return $uploadResult; // Return error from upload
                }
            }

            $sql = "UPDATE rewards SET title = ?, price = ?, image = ? WHERE id = ? AND family_id = ?";
            $params = [
                $data['title'],
                $data['price'],
                $imagePath,
                $data['id'],
                $data['family_id']
            ];

            $result = Database::runPrepared($sql, $params);
            return ["status" => "success", "message" => "Reward updated successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to update reward", "data" => $e->getMessage()];
        }
    }

    public static function delete($id, $familyId)
    {
        try {
            // Fetch reward to delete image if exists
            $sqlSelect = "SELECT image FROM rewards WHERE id = ? AND family_id = ?";
            $reward = Database::runPrepared($sqlSelect, [$id, $familyId]);

            if (!empty($reward) && !empty($reward[0]['image'])) {
                if (file_exists($reward[0]['image'])) {
                    File::deleteFile($reward[0]['image']);
                }
            }

            $sql = "DELETE FROM rewards WHERE id = ? AND family_id = ?";
            $result = Database::runPrepared($sql, [$id, $familyId]);
            return ["status" => "success", "message" => "Reward deleted successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to delete reward", "data" => $e->getMessage()];
        }
    }

    public static function getByFamilyId($familyId)
    {
        try {
            $sql = "SELECT id, title, price, image, created_at FROM rewards WHERE family_id = ? ORDER BY created_at DESC";
            $stmt = Database::runPrepared($sql, [$familyId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch rewards", "data" => $e->getMessage()];
        }
    }

    public static function getRewardCountByFamilyId($familyId)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM rewards WHERE family_id = ?";
            $stmt = Database::runPrepared($sql, [$familyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result['total']];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to count rewards", "data" => $e->getMessage()];
        }
    }

    public static function getById($id, $familyId)
    {
        try {
            $sql = "SELECT * FROM rewards WHERE id = ? AND family_id = ?";
            $stmt = Database::runPrepared($sql, [$id, $familyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return ["status" => "success", "data" => $result];
            } else {
                return ["status" => "error", "message" => "Reward not found"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch reward", "data" => $e->getMessage()];
        }
    }

    public static function redeem($rewardId, $userId, $familyId)
    {
        try {
            $db = Database::getInstance();
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) {
                $db->beginTransaction();
            }

            // Get reward details
            $rewardRes = self::getById($rewardId, $familyId);
            if ($rewardRes['status'] !== 'success') {
                throw new Exception("Reward not found");
            }
            $reward = $rewardRes['data'];
            $price = $reward['price'];

            // Check if user has enough points
            $pointsRes = Points::getPoints($userId);
            if ($pointsRes['status'] !== 'success') {
                throw new Exception("Failed to fetch user points");
            }
            $balance = isset($pointsRes['data']['balance']) ? $pointsRes['data']['balance'] : 0;
            
            if ($balance < $price) {
                throw new Exception("Not enough points to redeem this reward");
            }

            // Debit points
            $debitRes = Points::debitPoints($userId, $price, 'Redeemed reward: ' . $reward['title']);
            if ($debitRes['status'] !== 'success') {
                throw new Exception("Failed to deduct points");
            }

            // Insert into redeemed_rewards
            $sql = "INSERT INTO redeemed_rewards (reward_id, user_id, family_id, status) VALUES (?, ?, ?, 'pending')";
            Database::runPrepared($sql, [$rewardId, $userId, $familyId]);

            if (!$inTransaction) {
                $db->commit();
            }
            
            $newBalance = $balance - $price;
            return ["status" => "success", "message" => "Reward redeemed successfully", "new_balance" => $newBalance];
        } catch (Exception $e) {
            if (isset($db) && isset($inTransaction) && !$inTransaction && $db->inTransaction()) {
                $db->rollBack();
            }
            return ["status" => "error", "message" => "Failed to redeem reward", "data" => $e->getMessage()];
        }
    }

    public static function getMyVault($userId, $familyId)
    {
        try {
            $sql = "SELECT rr.*, r.title, r.price, r.image 
                    FROM redeemed_rewards rr 
                    JOIN rewards r ON rr.reward_id = r.id 
                    WHERE rr.user_id = ? AND rr.family_id = ? 
                    ORDER BY rr.redeemed_at DESC";
            $stmt = Database::runPrepared($sql, [$userId, $familyId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch vault", "data" => $e->getMessage()];
        }
    }

    public static function getFamilyVault($familyId)
    {
        try {
            $sql = "SELECT rr.*, r.title, r.price, r.image, u.name, u.image AS user_image 
                    FROM redeemed_rewards rr 
                    JOIN rewards r ON rr.reward_id = r.id 
                    JOIN users u ON rr.user_id = u.id
                    WHERE rr.family_id = ? 
                    ORDER BY rr.redeemed_at DESC";
            $stmt = Database::runPrepared($sql, [$familyId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch family vault", "data" => $e->getMessage()];
        }
    }

    public static function completeRedemption($redeemId, $familyId)
    {
        try {
            $sql = "UPDATE redeemed_rewards SET status = 'completed', completed_at = CURRENT_TIMESTAMP WHERE id = ? AND family_id = ?";
            $result = Database::runPrepared($sql, [$redeemId, $familyId]);
            return ["status" => "success", "message" => "Reward redemption completed", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to complete redemption", "data" => $e->getMessage()];
        }
    }
}
