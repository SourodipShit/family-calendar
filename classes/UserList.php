<?php
require_once __DIR__ . "/../config/Database.php";

class UserList
{
    public static function addList($data)
    {
        try {
            $stmt = Database::runPrepared("INSERT INTO user_lists (user_id, name) VALUES (?, ?)", [
                $data['user_id'],
                $data['name']
            ]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'List created successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to create list: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to create list'];
    }

    public static function editList($data)
    {
        try {
            $stmt = Database::runPrepared("UPDATE user_lists SET name = ? WHERE id = ?", [
                $data['name'],
                $data['id']
            ]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'List updated successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update list: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to update list'];
    }

    public static function delete($id)
    {
        try {
            $stmt = Database::runPrepared("DELETE FROM user_lists WHERE id = ?", [$id]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'List deleted successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to delete list: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to delete list'];
    }

    public static function GetByUserId($id)
    {
        try {
            // Get all lists
            $stmt = Database::runPrepared("SELECT * FROM user_lists WHERE user_id = ? ORDER BY created_at DESC", [$id]);
            $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get items for each list
            foreach ($lists as &$list) {
                $list['items'] = self::getItemsByListId($list['id']);
            }
            return $lists;
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function addItem($data)
    {
        try {
            $stmt = Database::runPrepared("INSERT INTO user_list_items (list_id, content) VALUES (?, ?)", [
                $data['list_id'],
                $data['content']
            ]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'Item added successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to add item: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to add item'];
    }

    public static function editItem($data)
    {
        try {
            $stmt = Database::runPrepared("UPDATE user_list_items SET content = ? WHERE id = ?", [
                $data['content'],
                $data['id']
            ]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'Item updated successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to update item: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to update item'];
    }

    public static function DeleteItem($id)
    {
        // Renamed from Delete($id) to avoid conflict with list deletion in the same class
        try {
            $stmt = Database::runPrepared("DELETE FROM user_list_items WHERE id = ?", [$id]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'Item deleted successfully'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to delete item: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to delete item'];
    }

    public static function getItemsByListId($id)
    {
        try {
            $stmt = Database::runPrepared("SELECT * FROM user_list_items WHERE list_id = ? ORDER BY created_at ASC", [$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function CheckItem($id)
    {
        try {
            $stmt = Database::runPrepared("UPDATE user_list_items SET is_checked = 1 WHERE id = ?", [$id]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'Item checked'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to check item: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to check item'];
    }

    public static function uncheck($id)
    {
        try {
            $stmt = Database::runPrepared("UPDATE user_list_items SET is_checked = 0 WHERE id = ?", [$id]);
            if ($stmt) {
                return ['status' => 'success', 'message' => 'Item unchecked'];
            }
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to uncheck item: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Failed to uncheck item'];
    }
}
