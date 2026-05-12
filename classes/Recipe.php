<?php
require_once __DIR__ . '/../config/Database.php';

class Recipe
{
    public static function add($data)
    {
        try {
            Database::getInstance()->beginTransaction();

            $recipeData = $data['recipe'];
            $fields = array_keys($recipeData);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($recipeData);

            $sql = "INSERT INTO recipes (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);

            $recipeId = Database::getInstance()->lastInsertId();

            if (!empty($data['ingredients'])) self::addIngredients($recipeId, $data['ingredients']);
            if (!empty($data['steps'])) self::addSteps($recipeId, $data['steps']);
            if (!empty($data['images'])) self::addImages($recipeId, $data['images']);
            if (!empty($data['stats'])) self::addStats($recipeId, $data['stats']);

            Database::getInstance()->commit();
            return $recipeId;

        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            throw $e;
        }
    }

    private static function addIngredients($RecipeId, $ingredients)
    {
        foreach ($ingredients as $ingredient) {
            $ingredient['recipe_id'] = $RecipeId;
            $fields = array_keys($ingredient);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($ingredient);

            $sql = "INSERT INTO recipe_ingredients (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
        }
    }

    private static function addSteps($RecipeId, $steps)
    {
        foreach ($steps as $index => $step) {
            $step['recipe_id'] = $RecipeId;
            if (!isset($step['step_number'])) $step['step_number'] = $index + 1;
            $fields = array_keys($step);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($step);

            $sql = "INSERT INTO recipe_steps (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
        }
    }

    private static function addImages($RecipeId, $images)
    {
        foreach ($images as $index => $image) {
            $image['recipe_id'] = $RecipeId;
            if (!isset($image['is_main'])) $image['is_main'] = ($index === 0 ? 1 : 0);
            if (!isset($image['sort_order'])) $image['sort_order'] = $index;
            $fields = array_keys($image);
            $placeholders = array_fill(0, count($fields), '?');
            $params = array_values($image);

            $sql = "INSERT INTO recipe_images (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
            Database::runPrepared($sql, $params);
        }
    }

    private static function addStats($RecipeId, $stats)
    {
        $stats['recipe_id'] = $RecipeId;
        $fields = array_keys($stats);
        $placeholders = array_fill(0, count($fields), '?');
        $params = array_values($stats);

        $sql = "INSERT INTO recipe_nutrition (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        Database::runPrepared($sql, $params);
    }
}
