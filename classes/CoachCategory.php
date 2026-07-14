<?php
require_once __DIR__ . '/../config/Database.php';

class CoachCategory
{
    public static function getAll()
    {
        try {
            $categories = Database::runPrepared("SELECT * FROM coach_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => true, "data" => $categories];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function getById($id)
    {
        try {
            $category = Database::runPrepared("SELECT * FROM coach_categories WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if ($category) {
                return ["status" => true, "data" => $category];
            } else {
                return ["status" => false, "message" => "Category not found"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function add($data)
    {
        try {
            if (empty($data['name'])) {
                return ["status" => false, "message" => "Category name is required"];
            }
            
            $query = "INSERT INTO coach_categories (name) VALUES (?)";
            $result = Database::runPrepared($query, [$data['name']]);
            
            if ($result->rowCount() > 0) {
                return ["status" => true, "message" => "Coach category added successfully"];
            } else {
                return ["status" => false, "message" => "Failed to add coach category"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function edit($data, $id)
    {
        try {
            if (empty($data['name'])) {
                return ["status" => false, "message" => "Category name is required"];
            }

            $query = "UPDATE coach_categories SET name = ? WHERE id = ?";
            $result = Database::runPrepared($query, [$data['name'], $id]);
            
            if ($result->rowCount() > 0 || $result->errorCode() === '00000') {
                return ["status" => true, "message" => "Coach category updated successfully"];
            } else {
                return ["status" => false, "message" => "Failed to update coach category"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function delete($id)
    {
        try {
            $result = Database::runPrepared("DELETE FROM coach_categories WHERE id = ?", [$id]);
            if ($result->rowCount() > 0) {
                return ["status" => true, "message" => "Coach category deleted successfully"];
            } else {
                return ["status" => false, "message" => "Failed to delete coach category"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }
}
