<?php
require_once __DIR__ . "/../config/Database.php";

class PromoCode
{
    /**
     * Add a new promo code
     */
    public static function add($data)
    {
        try {
            // Maximum of 5 codes allowed (enforcing business logic)
            $countRes = self::getCount();
            if ($countRes['status'] === 'success' && $countRes['data'] >= 5) {
                return ["status" => "error", "message" => "Maximum of 5 promo codes allowed."];
            }

            $sql = "INSERT INTO promo_codes (code, description, months_free, max_uses, is_active) VALUES (?, ?, ?, ?, ?)";
            $params = [
                $data['code'],
                $data['description'],
                $data['months_free'] ?? 1,
                $data['max_uses'] ?? 1,
                $data['is_active'] ?? 1
            ];
            
            $result = Database::runPrepared($sql, $params);
            return ["status" => "success", "message" => "Promo code added successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to add promo code", "data" => $e->getMessage()];
        }
    }

    /**
     * Delete an existing promo code
     */
    public static function delete($id)
    {
        try {
            $sql = "DELETE FROM promo_codes WHERE id = ?";
            $result = Database::runPrepared($sql, [$id]);
            return ["status" => "success", "message" => "Promo code deleted successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to delete promo code", "data" => $e->getMessage()];
        }
    }

    /**
     * Edit an existing promo code
     */
    public static function edit($data)
    {
        try {
            $sql = "UPDATE promo_codes SET code = ?, description = ?, months_free = ?, max_uses = ?, is_active = ? WHERE id = ?";
            $params = [
                $data['code'],
                $data['description'],
                $data['months_free'],
                $data['max_uses'],
                $data['is_active'],
                $data['id']
            ];
            
            $result = Database::runPrepared($sql, $params);
            return ["status" => "success", "message" => "Promo code updated successfully", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to update promo code", "data" => $e->getMessage()];
        }
    }

    /**
     * Fetch all promo codes
     */
    public static function fetchAll()
    {
        try {
            $sql = "SELECT * FROM promo_codes ORDER BY created_at DESC";
            $stmt = Database::runPrepared($sql, []);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch promo codes", "data" => $e->getMessage()];
        }
    }

    /**
     * Get a promo code by its text code (e.g., '1MFREE')
     */
    public static function getByCode($code)
    {
        try {
            $sql = "SELECT * FROM promo_codes WHERE code = ?";
            $stmt = Database::runPrepared($sql, [$code]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return ["status" => "success", "data" => $result];
            } else {
                return ["status" => "error", "message" => "Promo code not found"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch promo code", "data" => $e->getMessage()];
        }
    }

    /**
     * Verify if a promo code is valid for use
     */
    public static function verify($code)
    {
        $res = self::getByCode($code);
        
        if ($res['status'] !== 'success') {
            return ["status" => "error", "message" => "Invalid promo code"];
        }

        $promo = $res['data'];
        
        if ($promo['is_active'] != 1) {
            return ["status" => "error", "message" => "This promo code is no longer active"];
        }

        if ($promo['times_used'] >= $promo['max_uses']) {
            return ["status" => "error", "message" => "This promo code has reached its usage limit"];
        }

        return ["status" => "success", "message" => "Promo code is valid", "data" => $promo];
    }
    
    /**
     * Increment the usage counter for a promo code (called when a user signs up)
     */
    public static function incrementUsage($id)
    {
        try {
            $sql = "UPDATE promo_codes SET times_used = times_used + 1 WHERE id = ?";
            $result = Database::runPrepared($sql, [$id]);
            return ["status" => "success", "message" => "Promo code usage incremented", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to increment usage", "data" => $e->getMessage()];
        }
    }

    /**
     * Helper to get total number of promo codes to enforce the limit of 5
     */
    public static function getCount()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM promo_codes";
            $stmt = Database::runPrepared($sql, []);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => (int)$result['total']];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to count promo codes", "data" => $e->getMessage()];
        }
    }
}
