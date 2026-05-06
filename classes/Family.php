<?php
require_once __DIR__ . '/../config/Database.php';

class Family
{
    public static function getMembers()
    {
        $members = Database::runPrepared(
            "SELECT users.* 
         FROM users 
         INNER JOIN user_family 
            ON users.id = user_family.user_id 
         WHERE user_family.family_id = ?
         AND users.role IN ('member','family-head')",
            [$_SESSION['user']['families'][0]['id']]
        )->fetchAll(PDO::FETCH_ASSOC);

        return $members;
    }

    public static function getMembersByFamilyId($family_id)
    {
        $members = Database::runPrepared(
            "SELECT users.* 
         FROM users 
         INNER JOIN user_family 
            ON users.id = user_family.user_id 
         WHERE user_family.family_id = ?
         AND users.role IN ('member','family-head')",
            [$family_id]
        )->fetchAll(PDO::FETCH_ASSOC);

        return $members;
    }

    public static function getFamily($family_id)
    {
        $stmt = Database::runPrepared("SELECT * FROM families WHERE id = ?", [$family_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllFamilies()
    {
        $sql = "SELECT f.*, COUNT(uf.user_id) as member_count 
                FROM families f 
                LEFT JOIN user_family uf ON f.id = uf.family_id 
                GROUP BY f.id 
                ORDER BY f.name ASC";
        $stmt = Database::run($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function add($data)
    {
        $check = Database::runPrepared("SELECT id FROM families WHERE email = ?", [$data['email']]);
        if ($check->fetch()) {
            return ['status' => 'error', 'message' => 'Email already exists'];
        }

        try {
            $result = Database::runPrepared("INSERT INTO families(name, email, location, timezone) VALUES(?, ?, ?, ?)", [
                $data['name'],
                $data['email'],
                $data['location'],
                $data['timezone']
            ]);

            return ['status' => 'success', 'message' => 'Family added successfully', 'id' => Database::getInstance()->lastInsertId()];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to add family: ' . $e->getMessage()];
        }
    }

    public static function delete($family_id, $image_path = null)
    {
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }

        try {
            // First delete links in user_family
            Database::runPrepared("DELETE FROM user_family WHERE family_id = ?", [$family_id]);
            
            // Then delete the family
            $result = Database::runPrepared("DELETE FROM families WHERE id = ?", [$family_id]);
            if ($result) {
                return ['status' => 'success', 'message' => 'Family deleted successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to delete family: ' . $e->getMessage()];
        }
    }

    public static function update($data)
    {
        $check = Database::runPrepared("SELECT id FROM families WHERE email = ? AND id != ?", [$data['email'], $data['id']]);
        if ($check->fetch()) {
            return ['status' => 'error', 'message' => 'Email already exists'];
        }

        try {
            $result = Database::runPrepared("UPDATE families SET name = ?, email = ?, location = ?, timezone = ? WHERE id = ?", [
                $data['name'],
                $data['email'],
                $data['location'],
                $data['timezone'],
                $data['id']
            ]);

            return ['status' => 'success', 'message' => 'Family updated successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update family: ' . $e->getMessage()];
        }
    }
}
