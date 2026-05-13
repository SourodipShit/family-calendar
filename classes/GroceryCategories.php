<?php
require_once __DIR__ . '/../config/Database.php';

class GroceryCategories
{
    public static function getAll()
    {
        try {
            $category = Database::runPrepared("SELECT * FROM grocery_categories")->fetchAll(PDO::FETCH_ASSOC);
            if (count($category) > 0) {
                return ["status" => true, "data" => $category];
            } else {
                return ["status" => false, "message" => "No categories found"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function getByFamily($familyId)
    {
        try {
            $category = Database::runPrepared("SELECT * FROM grocery_categories WHERE family_id = ? OR (family_id IS NULL AND is_default = 1)", [$familyId])->fetchAll(PDO::FETCH_ASSOC);
            if (count($category) > 0) {
                return ["status" => true, "data" => $category];
            } else {
                return ["status" => false, "message" => "No categories found"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function add($data){
        try{
            $keys = [];
            $values = [];
            foreach($data as $key => $value){
                if($value !== null){
                    $keys[] = $key;
                    $values[] = $value;
                }
            }
            $query = "INSERT INTO grocery_categories (". implode(", ", $keys) . ") VALUES (" . implode(", ", array_fill(0, count($keys), "?")) . ")";
            $result = Database::runPrepared($query, $values);
            if($result->rowCount() > 0){
                return ["status" => true, "message" => "Category added successfully"];
            }else{
                return ["status" => false, "message" => "Failed to add category"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function edit($data, $id){
        try{
            $keys = [];
            $values = [];
            foreach($data as $key => $value){
                if($value !== null){
                    $keys[] = $key;
                    $values[] = $value;
                }
            }
            $values[] = $id;
            $query = "UPDATE grocery_categories SET " . implode(", ", $keys) . " WHERE id = ?";
            $result = Database::runPrepared($query, $values);
            if($result->rowCount() > 0){
                return ["status" => true, "message" => "Category updated successfully"];
            }else{
                return ["status" => false, "message" => "Failed to update category"];
            }
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public static function delete($id){
        try{
            Database::runPrepared("DELETE FROM grocery_categories WHERE id = ?", [$id]);
            return ["status" => true, "message" => "Category deleted successfully"];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }
}
