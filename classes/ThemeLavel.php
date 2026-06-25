<?php
require_once __DIR__ . "/../config/Database.php";

class ThemeLavel
{
    public static function getAll()
    {
        try {
            $sql = "SELECT * FROM theme_level";
            $result = Database::run($sql)->fetchAll(PDO::FETCH_ASSOC);
            return json_encode([
                "status" => "success",
                "data" => $result
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function add($data)
    {
        try {
            $sql = "INSERT INTO theme_level (name)
                    VALUES (?)";
            $result = Database::runPrepared($sql, $data);
            return json_encode([
                "status" => "success",
                "message" => "Theme level added successfully"
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function update($data)
    {
        try {
            $sql = "UPDATE theme_level SET name = ? WHERE id = ?";
            $result = Database::runPrepared($sql, $data);
            return json_encode([
                "status" => "success",
                "message" => "Theme level updated successfully"
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function delete($id)
    {
        try {
            $sql = "DELETE FROM theme_level WHERE id = ?";
            $result = Database::runPrepared($sql, [$id]);
            return json_encode([
                "status" => "success",
                "message" => "Theme level deleted successfully"
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
}
