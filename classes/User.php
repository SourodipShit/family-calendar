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
            $result = Database::runPrepared("INSERT INTO users(name, email, phone, role, image, password) VALUES(?, ?, ?, ?, ?, ?)", [
                $data['name'],
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

    public static function deleteUser($user_id, $image_path = null)
    {
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }

        try {
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
}
