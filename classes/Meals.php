<?php
require_once __DIR__ . "/../config/Database.php";

class Meals
{
    public static function getByDateRange($startDate, $endDate, $familyId, $userId = null)
    {
        try {
            $sql = "SELECT m.*, 
                           (SELECT COALESCE(AVG(rating), 0) FROM meal_ratings WHERE meal_id = m.id) as average_rating, 
                           (SELECT COUNT(*) FROM meal_ratings WHERE meal_id = m.id) as total_ratings,
                           (SELECT COUNT(*) FROM meal_favorites f WHERE f.meal_id = m.id AND f.user_id = ?) as is_favorite
                    FROM meals m 
                    WHERE m.date BETWEEN ? AND ? AND m.family_id = ? AND m.status = 'approved'
                    ORDER BY m.date";
            
            $meals = Database::runPrepared($sql, [$userId, $startDate, $endDate, $familyId])->fetchAll(PDO::FETCH_ASSOC);
            
            // Format average_rating to 1 decimal place
            foreach ($meals as &$meal) {
                $meal['average_rating'] = round($meal['average_rating'], 1);
            }

            return ["status" => "success", "meals" => $meals];
        } catch (PDOException $e) {
            error_log("Meal Fetch Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getPendingMealsUser($userId, $familyId)
    {
        try {
            $sql = "SELECT m.* FROM meals m WHERE m.user_id = ? AND m.family_id = ? AND m.status = 'pending' ORDER BY m.date ASC";
            $meals = Database::runPrepared($sql, [$userId, $familyId])->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "meals" => $meals];
        } catch (PDOException $e) {
            error_log("Meal Fetch Pending User Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getPendingMealsFamily($familyId)
    {
        try {
            // Join with users table to get the creator's name
            $sql = "SELECT m.*, u.name as creator_name 
                    FROM meals m 
                    LEFT JOIN users u ON m.user_id = u.id 
                    WHERE m.family_id = ? AND m.status = 'pending' 
                    ORDER BY m.date ASC";
            $meals = Database::runPrepared($sql, [$familyId])->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "meals" => $meals];
        } catch (PDOException $e) {
            error_log("Meal Fetch Pending Family Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function approveMeal($id, $familyId)
    {
        try {
            // First get the meal details to know its date and type
            $meal = Database::runPrepared("SELECT date, type FROM meals WHERE id = ? AND family_id = ?", [$id, $familyId])->fetch(PDO::FETCH_ASSOC);
            if (!$meal) {
                return ["status" => "error", "message" => "Meal not found."];
            }

            Database::getInstance()->beginTransaction();

            // Approve the target meal
            Database::runPrepared("UPDATE meals SET status = 'approved' WHERE id = ?", [$id]);

            // Reject all other pending meals for the same date and type in this family
            Database::runPrepared(
                "UPDATE meals SET status = 'rejected' WHERE family_id = ? AND date = ? AND type = ? AND id != ? AND status = 'pending'",
                [$familyId, $meal['date'], $meal['type'], $id]
            );

            Database::getInstance()->commit();
            return ["status" => "success", "message" => "Meal approved and conflicts rejected."];
        } catch (PDOException $e) {
            Database::getInstance()->rollBack();
            error_log("Meal Approve Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function rejectMeal($id, $familyId)
    {
        try {
            Database::runPrepared("UPDATE meals SET status = 'rejected' WHERE id = ? AND family_id = ?", [$id, $familyId]);
            return ["status" => "success", "message" => "Meal rejected."];
        } catch (PDOException $e) {
            error_log("Meal Reject Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function addMeal($data)
    {
        try {
            // Safety Check: Only check for existing APPROVED meals for this date and type
            $check = Database::runPrepared("SELECT id FROM meals WHERE family_id = ? AND type = ? AND date = ? AND status = 'approved'", [$data['family_id'], $data['type'], $data['date']])->fetch();
            if ($check) {
                return ["status" => "error", "message" => "An approved meal already exists for this slot."];
            }

            $recipe_id = !empty($data['recipe_id']) ? $data['recipe_id'] : null;
            $user_id = !empty($data['user_id']) ? $data['user_id'] : null;
            $status = !empty($data['status']) ? $data['status'] : 'pending';

            Database::runPrepared(
                "INSERT INTO meals (type, name, image, date, family_id, recipe_id, user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
                [$data['type'], $data['name'], $data['image'], $data['date'], $data['family_id'], $recipe_id, $user_id, $status]
            );
            return ["status" => "success", "id" => Database::getInstance()->lastInsertId(), "message" => "Meal added successfully."];
        } catch (PDOException $e) {
            error_log("Meal Add Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function updateMeal($data)
    {
        try {
            // Safety Check: Unique combination of family_id, type, and date (excluding current record) that are approved
            $check = Database::runPrepared("SELECT id FROM meals WHERE family_id = ? AND type = ? AND date = ? AND id != ? AND status = 'approved'", [$data['family_id'], $data['type'], $data['date'], $data['id']])->fetch();
            if ($check) {
                return ["status" => "error", "message" => "An approved meal already exists for this slot."];
            }

            $recipe_id = !empty($data['recipe_id']) ? $data['recipe_id'] : null;
            Database::runPrepared("UPDATE meals SET name = ?, date = ?, type = ?, image = ?, family_id = ?, recipe_id = ? WHERE id = ?", [$data['name'], $data['date'], $data['type'], $data['image'], $data['family_id'], $recipe_id, $data['id']]);
            return ["status" => "success", "message" => "Meal updated successfully."];
        } catch (PDOException $e) {
            error_log("Meal Update Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function deleteMeal($id)
    {
        try {
            Database::runPrepared("DELETE FROM meals WHERE id = ?", [$id]);
            return ["status" => "success", "message" => "Meal removed successfully."];
        } catch (PDOException $e) {
            error_log("Meal Delete Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
