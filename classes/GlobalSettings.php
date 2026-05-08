<?php
require_once __DIR__ . "/../config/Database.php";

class GlobalSettings
{
    public static function getAllSettings()
    {
        try {
            $settings = Database::runPrepared("SELECT * FROM global_settings")->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $settings];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function updateSetting($key, $value)
    {
        try {
            $stmt = Database::runPrepared("UPDATE global_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "message" => "Settings updated successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function getSetting($key)
    {
        try {
            $stmt = Database::runPrepared("SELECT setting_value FROM global_settings WHERE setting_key = ?", [$key])->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $stmt ?: []];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
