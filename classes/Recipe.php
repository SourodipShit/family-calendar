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

    public static function getRecipies($count, $offset = 0, $filter = [], $userFamilyId = 0)
    {
        try {
            $params = [$userFamilyId, $userFamilyId];
            $sql = "SELECT r.*, u.name as user_name, u.image as user_image, ri.image_path as image_url, rn.calories,
                    CASE 
                        WHEN r.family_id = ? THEN 'approved'
                        WHEN r.visibility = 'public' THEN 'approved'
                        ELSE rar.status 
                    END as request_status
                FROM recipes r
                INNER JOIN users u ON u.id = r.user_id
                LEFT JOIN recipe_images ri ON ri.recipe_id = r.id AND ri.is_main = 1
                LEFT JOIN recipe_nutrition rn ON rn.recipe_id = r.id
                LEFT JOIN recipe_access_requests rar ON rar.recipe_id = r.id AND rar.requester_family_id = ?
                WHERE r.status = 'approved'";

            if (!empty($filter['category'])) {
                $sql .= " AND r.category = ?";
                $params[] = $filter['category'];
            }

            if (!empty($filter['difficulty'])) {
                $sql .= " AND r.difficulty = ?";
                $params[] = $filter['difficulty'];
            }


            if (!empty($filter['search'])) {
                $sql .= " AND r.name LIKE ?";
                $params[] = "%" . $filter['search'] . "%";
            }

            // Add limit and offset safely
            $sql .= " ORDER BY r.created_at DESC LIMIT " . (int)$count . " OFFSET " . (int)$offset;

            $stmt = Database::runPrepared($sql, $params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($results) == 0) {
                return ["status" => "success", "message" => "No recipes found."];
            }
            return ["status" => "success", "data" => $results];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getById($id)
    {
        try {
            // 1. Fetch Core Recipe Data with Main Image and User info
            $sql = "SELECT r.*, u.name as user_name, u.image as user_image, ri.image_path as image_url
                FROM recipes r
                INNER JOIN users u ON u.id = r.user_id
                LEFT JOIN recipe_images ri ON ri.recipe_id = r.id AND ri.is_main = 1
                WHERE r.id = ?";
            $stmt = Database::runPrepared($sql, [$id]);
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$recipe) {
                return ["status" => "error", "message" => "Recipe not found."];
            }

            // 2. Fetch Ingredients
            $sql = "SELECT * FROM recipe_ingredients WHERE recipe_id = ?";
            $stmt = Database::runPrepared($sql, [$id]);
            $recipe['ingredients'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Fetch Steps
            $sql = "SELECT * FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number ASC";
            $stmt = Database::runPrepared($sql, [$id]);
            $recipe['steps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Fetch All Images
            $sql = "SELECT * FROM recipe_images WHERE recipe_id = ? ORDER BY sort_order ASC";
            $stmt = Database::runPrepared($sql, [$id]);
            $recipe['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Fetch Nutrition
            $sql = "SELECT * FROM recipe_nutrition WHERE recipe_id = ?";
            $stmt = Database::runPrepared($sql, [$id]);
            $recipe['nutrition'] = $stmt->fetch(PDO::FETCH_ASSOC);

            return ["status" => "success", "data" => $recipe];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function requestAccess($recipeId, $userFamilyId)
    {
        try {
            $sql = "INSERT INTO recipe_access_requests (recipe_id, requester_family_id) VALUES (?, ?)";
            Database::runPrepared($sql, [$recipeId, $userFamilyId]);
            return ["status" => "success", "message" => "Request sent successfully."];
        }
        catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function approveAccess($requestId)
    {
        try {
            $sql = "UPDATE recipe_access_requests SET status = 'approved' WHERE id = ?";
            Database::runPrepared($sql, [$requestId]);
            return ["status" => "success", "message" => "Request approved successfully."];
        }
        catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function rejectAccess($requestId)
    {
        try {
            $sql = "UPDATE recipe_access_requests SET status = 'denied' WHERE id = ?";
            Database::runPrepared($sql, [$requestId]);
            return ["status" => "success", "message" => "Request denied successfully."];
        }
        catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getAccessRequests($recipeId)
    {
        try {
            $sql = "SELECT * FROM recipe_access_requests WHERE recipe_id = ?";
            $stmt = Database::runPrepared($sql, [$recipeId]);
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $requests];
        }
        catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public static function getRequestsForFamily($userId)
    {
        try {
            $sql = "SELECT rar.*, r.name as recipe_name, f.name as requester_family_name 
                    FROM recipe_access_requests rar
                    INNER JOIN recipes r ON r.id = rar.recipe_id
                    INNER JOIN families f ON f.id = rar.requester_family_id
                    WHERE r.user_id = ?
                    ORDER BY rar.requested_at DESC";
            $stmt = Database::runPrepared($sql, [$userId]);
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $requests];
        }
        catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
