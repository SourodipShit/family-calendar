<?php
require_once __DIR__ . "/../config/Database.php";

class Favorite
{
    public static function add($meal_id, $user_id)
    {
        try {
            // Check if already a favorite
            $check = Database::runPrepared("SELECT id FROM meal_favorites WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id])->fetch();
            if ($check) {
                return ["status" => "success", "message" => "Already in favorites."];
            }

            Database::runPrepared("INSERT INTO meal_favorites (meal_id, user_id) VALUES (?, ?)", [$meal_id, $user_id]);
            return ["status" => "success", "message" => "Added to favorites successfully."];
        } catch (PDOException $e) {
            error_log("Favorite Add Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function remove($meal_id, $user_id)
    {
        try {
            Database::runPrepared("DELETE FROM meal_favorites WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id]);
            return ["status" => "success", "message" => "Removed from favorites successfully."];
        } catch (PDOException $e) {
            error_log("Favorite Remove Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function isFavorite($meal_id, $user_id)
    {
        try {
            $check = Database::runPrepared("SELECT id FROM meal_favorites WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id])->fetch();
            return ["status" => "success", "is_favorite" => (bool)$check];
        } catch (PDOException $e) {
            error_log("Favorite Check Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function toggle($meal_id, $user_id)
    {
        try {
            $check = Database::runPrepared("SELECT id FROM meal_favorites WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id])->fetch();
            if ($check) {
                return self::remove($meal_id, $user_id);
            } else {
                return self::add($meal_id, $user_id);
            }
        } catch (PDOException $e) {
            error_log("Favorite Toggle Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getUserFavorites($user_id)
    {
        try {
            $sql = "SELECT m.*, 
                           COALESCE(AVG(r.rating), 0) as average_rating, 
                           COUNT(r.id) as total_ratings,
                           1 as is_favorite
                    FROM meals m 
                    JOIN meal_favorites f ON m.id = f.meal_id 
                    LEFT JOIN meal_ratings r ON m.id = r.meal_id
                    WHERE f.user_id = ? 
                    GROUP BY m.id
                    ORDER BY f.created_at DESC";
            
            $favorites = Database::runPrepared($sql, [$user_id])->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($favorites as &$meal) {
                $meal['average_rating'] = round($meal['average_rating'], 1);
            }
            
            return ["status" => "success", "favorites" => $favorites];
        } catch (PDOException $e) {
            error_log("User Favorites Fetch Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
