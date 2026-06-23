<?php
require_once __DIR__ . "/../config/Database.php";

class User
{
    public static function addUser($data)
    {
        // Check if email already exists
        $check = Database::runPrepared("SELECT id FROM users WHERE email = ?", [$data['email']]);
        if ($check->fetch()) {
            return ['status' => 'error', 'message' => 'Email already exists'];
        }

        try {
            $result = Database::runPrepared("INSERT INTO users(name, nickname, email, phone, role, image, password) VALUES(?, ?, ?, ?, ?, ?, ?)", [
                $data['name'],
                $data['nickname'] ?? null,
                $data['email'],
                $data['phone'],
                $data['role'],
                $data['image'],
                $data['password']
            ]);

            $user_id = Database::runPrepared("SELECT id FROM users WHERE email = ?", [$data['email']]);
            $user_id = $user_id->fetchColumn();
            self::linkUserToFamily($user_id, $data['family_id']);

            if ($result) {
                return ['status' => 'success', 'message' => 'User added successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to add user: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Failed to add user'];
    }

    public static function getAllUsers()
    {
        $stmt = Database::run("SELECT * FROM users ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllUsersWithFamily()
    {
        $sql = "SELECT u.*, f.name as family_name 
                FROM users u 
                LEFT JOIN user_family uf ON u.id = uf.user_id 
                LEFT JOIN families f ON uf.family_id = f.id 
                ORDER BY u.name ASC";
        $stmt = Database::run($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserById($user_id)
    {
        $stmt = Database::runPrepared("SELECT * FROM users WHERE id = ?", [$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateUser($user_id, $data)
    {
        // Check if email belongs to another user
        if (isset($data['email'])) {
            $check = Database::runPrepared("SELECT id FROM users WHERE email = ? AND id != ?", [$data['email'], $user_id]);
            if ($check->fetch()) {
                return ['status' => 'error', 'message' => 'Email already exists for another user'];
            }
        }

        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $user_id;

        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";

        try {
            $result = Database::runPrepared($sql, $params);
            if ($result) {
                return ['status' => 'success', 'message' => 'User updated successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update user: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Failed to update user'];
    }

    public static function getUserSettings($user_id)
    {
        $stmt = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$user_id]);
        $result = $stmt->fetchColumn();
        return $result ? json_decode($result, true) : [];
    }

    public static function updateUserSettings($settings, $user_id)
    {
        try {
            // Ensure settings is a valid JSON string
            $settings_json = is_string($settings) ? $settings : json_encode($settings);
            
            $result = Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [$settings_json, $user_id]);
            
            if ($result) {
                return ['status' => 'success', 'message' => 'User settings updated successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update user settings: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Failed to update user settings'];
    }

    public static function deleteUser($user_id, $image_path = null)
    {
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }

        try {
            Database::runPrepared("DELETE FROM user_family WHERE user_id = ?", [$user_id]);
            $result = Database::runPrepared("DELETE FROM users WHERE id = ?", [$user_id]);
            if ($result) {
                return ['status' => 'success', 'message' => 'User deleted successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to delete user: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Failed to delete user'];
    }

    public static function linkUserToFamily($user_id, $family_id)
    {
        $result = Database::runPrepared("INSERT INTO user_family(user_id, family_id) VALUES(?, ?)", [
            $user_id,
            $family_id
        ]);
    }

    public static function findByEmail($email)
    {
        $stmt = Database::runPrepared("SELECT * FROM users WHERE email = ?", [$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getUserFamilies($user_id)
    {
        // Get native families from user_family
        $nativeFamilies = Database::runPrepared("
            SELECT f.id, f.name, 'Native' as connection_type 
            FROM families f 
            INNER JOIN user_family uf ON f.id = uf.family_id 
            WHERE uf.user_id = ?
        ", [$user_id])->fetchAll(PDO::FETCH_ASSOC);

        // Get linked families from family_requests
        $linkedFamilies = Database::runPrepared("
            SELECT f.id, f.name, 'Shared Connection' as connection_type 
            FROM families f 
            INNER JOIN family_requests fr ON f.id = fr.family_id 
            WHERE (fr.requester_id = ? OR fr.receiver_id = ?) AND fr.status = 'approved'
        ", [$user_id, $user_id])->fetchAll(PDO::FETCH_ASSOC);

        // Combine and remove duplicates by family ID
        $families = [];
        $seen = [];
        
        foreach (array_merge($nativeFamilies, $linkedFamilies) as $family) {
            if (!isset($seen[$family['id']])) {
                $families[] = $family;
                $seen[$family['id']] = true;
            }
        }
        
        return $families;
    }
}
