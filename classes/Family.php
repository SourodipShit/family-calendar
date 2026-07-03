<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/SharedEmails.php';

class Family
{
    public static function getMembers()
    {
        return self::getMembersByFamilyId($_SESSION['user']['active_family_id']);
    }

    public static function getMembersByFamilyId($family_id, $user_id = null)
    {
        if (!$user_id && isset($_SESSION['user']['id'])) {
            $user_id = $_SESSION['user']['id'];
        }

        $members = Database::runPrepared(
            "SELECT users.* 
         FROM users 
         INNER JOIN user_family 
            ON users.id = user_family.user_id 
         WHERE user_family.family_id = ?
         AND users.role IN ('member','family-head')",
            [$family_id]
        )->fetchAll(PDO::FETCH_ASSOC);

        $external_users = [];
        if ($user_id) {
            $external_requesters = Database::runPrepared(
                "SELECT u.* 
                 FROM users u
                 INNER JOIN family_requests fr ON u.id = fr.requester_id
                 WHERE fr.family_id = ? AND fr.receiver_id = ? AND fr.status = 'approved'",
                [$family_id, $user_id]
            )->fetchAll(PDO::FETCH_ASSOC);

            $external_receivers = Database::runPrepared(
                "SELECT u.* 
                 FROM users u
                 INNER JOIN family_requests fr ON u.id = fr.receiver_id
                 INNER JOIN user_family uf ON fr.requester_id = uf.user_id
                 WHERE uf.family_id = ? AND fr.requester_id = ? AND fr.status = 'approved'",
                [$family_id, $user_id]
            )->fetchAll(PDO::FETCH_ASSOC);

            $existing_ids = array_map(function($m) { return $m['id']; }, $members);
            
            foreach (array_merge($external_requesters, $external_receivers) as $eu) {
                if (!in_array($eu['id'], $existing_ids)) {
                    $eu['is_external'] = true;
                    $external_users[] = $eu;
                    $existing_ids[] = $eu['id'];
                }
            }
        }

        return array_merge($members, $external_users);
    }

    public static function getFamily($family_id)
    {
        $stmt = Database::runPrepared("SELECT * FROM families WHERE id = ?", [$family_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllFamilies()
    {
        $sql = "SELECT f.*, COUNT(uf.user_id) as member_count, a.account_number,
                       (SELECT promo_code FROM used_promocodes up WHERE up.family_id = f.id ORDER BY up.entered_at DESC LIMIT 1) as promo_code
                FROM families f 
                LEFT JOIN user_family uf ON f.id = uf.family_id 
                LEFT JOIN accounts a ON f.id = a.family_id
                GROUP BY f.id, a.account_number 
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
            $result = Database::runPrepared("INSERT INTO families(name, email, location, timezone, storage_allocated) VALUES(?, ?, ?, ?, ?)", [
                $data['name'],
                $data['email'],
                $data['location'],
                $data['timezone'],
                $data['storage_allocated'] ?? 500
            ]);

            $familyId = Database::getLastInsertId();
            
            // Allocate shared email if available
            SharedEmails::allocateFamily($familyId);

            return ['status' => 'success', 'message' => 'Family added successfully', 'id' => $familyId];
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
            if (isset($data['storage_allocated'])) {
                $result = Database::runPrepared("UPDATE families SET name = ?, email = ?, location = ?, timezone = ?, storage_allocated = ? WHERE id = ?", [
                    $data['name'],
                    $data['email'],
                    $data['location'],
                    $data['timezone'],
                    $data['storage_allocated'],
                    $data['id']
                ]);
            } else {
                $result = Database::runPrepared("UPDATE families SET name = ?, email = ?, location = ?, timezone = ? WHERE id = ?", [
                    $data['name'],
                    $data['email'],
                    $data['location'],
                    $data['timezone'],
                    $data['id']
                ]);
            }

            return ['status' => 'success', 'message' => 'Family updated successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update family: ' . $e->getMessage()];
        }
    }

    public static function updateFamilySettings($settings, $familyId)
    {
        try {
            $result = Database::runPrepared("UPDATE families SET settings = ? WHERE id = ?", [
                json_encode($settings),
                $familyId
            ]);

            return ['status' => 'success', 'message' => 'Family settings updated successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update family settings: ' . $e->getMessage()];
        }
    }

    public static function updateMonthlyAmount($familyId, $amount)
    {
        try {
            Database::runPrepared("UPDATE families SET monthly_amount = ? WHERE id = ?", [
                $amount,
                $familyId
            ]);
            return ['status' => 'success', 'message' => 'Monthly amount updated successfully'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update monthly amount: ' . $e->getMessage()];
        }
    }
}

