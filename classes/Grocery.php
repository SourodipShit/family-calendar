<?php
require_once __DIR__ . "/../config/Database.php";

class Grocery
{
    // --- List Functions ---

    public static function addNewList($data)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);
        unset($data['action']);
        $list = $data;

        if (empty($list)) {
            return ["status" => "error", "message" => "List data is required"];
        }

        $keys = [];
        $values = [];

        foreach ($list as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }

        $sql = "INSERT INTO grocery_lists (" . implode(",", $keys) . ") VALUES (" . implode(",", array_fill(0, count($keys), "?")) . ")";

        if (Database::runPrepared($sql, $values)) {
            $list_id = Database::getLastInsertId();
            foreach ($items as $item) {
                $item['grocery_list_id'] = $list_id;
                self::addItems($item);
            }
            return ["status" => "success", "message" => "List added successfully", "id" => $list_id];
        } else {
            return ["status" => "error", "message" => "List could not be added"];
        }
    }

    public static function editList($id, $data)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $keys = [];
        $values = [];

        foreach ($data as $key => $value) {
            $keys[] = "$key = ?";
            $values[] = $value;
        }

        if (empty($keys)) {
            return ["status" => "error", "message" => "No data to update"];
        }

        $sql = "UPDATE grocery_lists SET " . implode(", ", $keys) . " WHERE id = ?";
        $values[] = $id;

        if (Database::runPrepared($sql, $values)) {
            // Delete old items and add new ones
            Database::runPrepared("DELETE FROM grocery_items WHERE grocery_list_id = ?", [$id]);
            foreach ($items as $item) {
                $item['grocery_list_id'] = $id;
                self::addItems($item);
            }
            return ["status" => "success", "message" => "List updated successfully"];
        } else {
            return ["status" => "error", "message" => "List could not be updated"];
        }
    }

    public static function deleteList($id)
    {
        // Delete items first (due to FK-like relationship, though not explicitly defined as FK in export, it's good practice)
        Database::runPrepared("DELETE FROM grocery_items WHERE grocery_list_id = ?", [$id]);
        
        $sql = "DELETE FROM grocery_lists WHERE id = ?";
        if (Database::runPrepared($sql, [$id])) {
            return ["status" => "success", "message" => "List deleted successfully"];
        } else {
            return ["status" => "error", "message" => "List could not be deleted"];
        }
    }


    public static function getFamilyLists($family_id)
    {
        $lists = Database::runPrepared("SELECT * FROM grocery_lists WHERE family_id = ? ORDER BY created_at DESC", [$family_id])->fetchAll(PDO::FETCH_ASSOC);
        return ["status" => "success", "data" => $lists];
    }

    public static function getByDateRange($family_id, $startDate, $endDate)
    {
        $sql = "SELECT * FROM grocery_lists WHERE family_id = ? AND week_start_date = ? AND week_end_date = ?";
        $list = Database::runPrepared($sql, [$family_id, $startDate, $endDate])->fetch(PDO::FETCH_ASSOC);
        if ($list) {
            $list['items'] = self::getListItems($list['id']);
            return ["status" => "success", "data" => $list];
        }
        return ["status" => "error", "message" => "No list found for this range"];
    }

    // --- Item Functions ---

    public static function addItems($data)
    {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }

        $sql = "INSERT INTO grocery_items (" . implode(",", $keys) . ") VALUES (" . implode(",", array_fill(0, count($keys), "?")) . ")";

        if (Database::runPrepared($sql, $values)) {
            return ["status" => "success", "message" => "Item added successfully", "id" => Database::getLastInsertId()];
        } else {
            return ["status" => "error", "message" => "Item could not be added"];
        }
    }

    public static function editItems($id, $data)
    {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = "$key = ?";
            $values[] = $value;
        }

        if (empty($keys)) {
            return ["status" => "error", "message" => "No data to update"];
        }

        $sql = "UPDATE grocery_items SET " . implode(", ", $keys) . " WHERE id = ?";
        $values[] = $id;

        if (Database::runPrepared($sql, $values)) {
            return ["status" => "success", "message" => "Item edited successfully"];
        } else {
            return ["status" => "error", "message" => "Item could not be edited"];
        }
    }

    public static function deleteItem($id)
    {
        $sql = "DELETE FROM grocery_items WHERE id = ?";
        if (Database::runPrepared($sql, [$id])) {
            return ["status" => "success", "message" => "Item deleted successfully"];
        } else {
            return ["status" => "error", "message" => "Item could not be deleted"];
        }
    }

    public static function toggleItemStatus($id, $is_complete)
    {
        $sql = "UPDATE grocery_items SET is_complete = ? WHERE id = ?";
        if (Database::runPrepared($sql, [$is_complete ? 1 : 0, $id])) {
            return ["status" => "success", "message" => "Item status updated"];
        } else {
            return ["status" => "error", "message" => "Failed to update item status"];
        }
    }

    public static function getGroceryList($list_id)
    {
        $sql = "SELECT * FROM grocery_lists WHERE id = ?";
        $list = Database::runPrepared($sql, [$list_id])->fetch(PDO::FETCH_ASSOC);
        if ($list) {
            $list['items'] = self::getListItems($list_id);
            return ["status" => "success", "data" => $list];
        }
        return ["status" => "error", "message" => "List not found"];
    }

    private static function getListItems($list_id)
    {
        $sql = "SELECT i.*, c.name as category_name FROM grocery_items i JOIN grocery_categories c ON i.grocery_category_id = c.id WHERE grocery_list_id = ?";
        return Database::runPrepared($sql, [$list_id])->fetchAll(PDO::FETCH_ASSOC);
    }
}
