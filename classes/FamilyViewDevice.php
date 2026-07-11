<?php
require_once __DIR__ . '/../config/Database.php';

class FamilyViewDevice
{
    /**
     * Create a new device token for a family
     * 
     * @param int $family_id
     * @return array
     */
    public static function create($family_id)
    {
        try {
            $token = bin2hex(random_bytes(32)); // Generates a 64-character hex string

            $sql = "INSERT INTO family_view_devices (family_id, token, created_at, active) VALUES (?, ?, CURRENT_TIMESTAMP, 1)";
            $stmt = Database::runPrepared($sql, [$family_id, $token]);

            if ($stmt) {
                return [
                    'status' => 'success',
                    'token' => $token,
                    'device_id' => Database::getInstance()->lastInsertId()
                ];
            }
            
            return ['status' => 'error', 'message' => 'Failed to create device.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Verify a token and update its last_used timestamp
     * 
     * @param string $token
     * @return array
     */
    public static function verify($token)
    {
        try {
            $sql = "SELECT * FROM family_view_devices WHERE token = ? AND active = 1";
            $device = Database::runPrepared($sql, [$token])->fetch(PDO::FETCH_ASSOC);

            if ($device) {
                // Update last_used
                self::updateLastUsed($device['id']);
                return ['status' => 'success', 'data' => $device];
            }

            return ['status' => 'error', 'message' => 'Invalid or inactive token.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Update the last_used timestamp for a specific device
     * 
     * @param int $id
     */
    private static function updateLastUsed($id)
    {
        $sql = "UPDATE family_view_devices SET last_used = CURRENT_TIMESTAMP WHERE id = ?";
        Database::runPrepared($sql, [$id]);
    }

    /**
     * Revoke (deactivate) a specific token
     * 
     * @param string $token
     * @return array
     */
    public static function revoke($token)
    {
        try {
            $sql = "UPDATE family_view_devices SET active = 0 WHERE token = ?";
            $stmt = Database::runPrepared($sql, [$token]);
            
            if ($stmt->rowCount() > 0) {
                return ['status' => 'success', 'message' => 'Device revoked successfully.'];
            }
            return ['status' => 'error', 'message' => 'Device not found or already inactive.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Revoke (deactivate) all devices for a specific family
     * 
     * @param int $family_id
     * @return array
     */
    public static function revokeAll($family_id)
    {
        try {
            $sql = "UPDATE family_view_devices SET active = 0 WHERE family_id = ?";
            $stmt = Database::runPrepared($sql, [$family_id]);
            
            return ['status' => 'success', 'message' => 'All devices revoked for the family.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Verify token and return family ID directly
     * 
     * @param string $token
     * @return int|false
     */
    public static function verifyTokenAndGetFamilyId($token)
    {
        $result = self::verify($token);
        if ($result['status'] === 'success' && isset($result['data']['family_id'])) {
            return $result['data']['family_id'];
        }
        return false;
    }
}
