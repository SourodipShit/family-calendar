<?php
require_once __DIR__ . "/../config/Database.php";

class Rating
{
    public static function save($meal_id, $user_id, $rating)
    {
        try {
            // Check if rating already exists
            $existing = Database::runPrepared("SELECT id FROM meal_ratings WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id])->fetch();

            if ($existing) {
                // Update existing rating
                Database::runPrepared("UPDATE meal_ratings SET rating = ? WHERE id = ?", [$rating, $existing['id']]);
                return ["status" => "success", "message" => "Rating updated successfully."];
            } else {
                // Insert new rating
                Database::runPrepared("INSERT INTO meal_ratings (meal_id, user_id, rating) VALUES (?, ?, ?)", [$meal_id, $user_id, $rating]);
                return ["status" => "success", "message" => "Rating saved successfully."];
            }
        } catch (PDOException $e) {
            error_log("Rating Save Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function delete($meal_id, $user_id)
    {
        try {
            Database::runPrepared("DELETE FROM meal_ratings WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id]);
            return ["status" => "success", "message" => "Rating removed successfully."];
        } catch (PDOException $e) {
            error_log("Rating Delete Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getRating($meal_id, $user_id)
    {
        try {
            $rating = Database::runPrepared("SELECT rating FROM meal_ratings WHERE meal_id = ? AND user_id = ?", [$meal_id, $user_id])->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "rating" => $rating ? $rating['rating'] : 0];
        } catch (PDOException $e) {
            error_log("Rating Get Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getAverageRating($meal_id)
    {
        try {
            $data = Database::runPrepared("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM meal_ratings WHERE meal_id = ?", [$meal_id])->fetch(PDO::FETCH_ASSOC);
            return [
                "status" => "success",
                "average" => $data['avg_rating'] ? round($data['avg_rating'], 1) : 0,
                "count" => $data['total_ratings']
            ];
        } catch (PDOException $e) {
            error_log("Average Rating Get Error: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
