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
                    WHERE m.date BETWEEN ? AND ? AND m.family_id = ? 
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

    public static function addMeal($data)
    {
        try {
            // Safety Check: Unique combination of family_id, type, and date
            $check = Database::runPrepared("SELECT id FROM meals WHERE family_id = ? AND type = ? AND date = ?", [$data['family_id'], $data['type'], $data['date']])->fetch();
            if ($check) {
                return ["status" => "error", "message" => "A meal of this type already exists for this date."];
            }

            Database::runPrepared("INSERT INTO meals (type, name, image, date, family_id) VALUES (?, ?, ?, ?, ?)", [$data['type'], $data['name'], $data['image'], $data['date'], $data['family_id']]);
            return ["status" => "success", "id" => Database::getInstance()->lastInsertId(), "message" => "Meal added successfully."];
        } catch (PDOException $e) {
            error_log("Meal Add Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function updateMeal($data)
    {
        try {
            // Safety Check: Unique combination of family_id, type, and date (excluding current record)
            $check = Database::runPrepared("SELECT id FROM meals WHERE family_id = ? AND type = ? AND date = ? AND id != ?", [$data['family_id'], $data['type'], $data['date'], $data['id']])->fetch();
            if ($check) {
                return ["status" => "error", "message" => "A meal of this type already exists for this date."];
            }

            Database::runPrepared("UPDATE meals SET name = ?, date = ?, type = ?, image = ?, family_id = ? WHERE id = ?", [$data['name'], $data['date'], $data['type'], $data['image'], $data['family_id'], $data['id']]);
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
