<?php

require_once __DIR__ . "/../config/Database.php";

class ThemeReward
{
    public static function getAll()
    {
        try {
            $sql = "SELECT * FROM theme_rewards WHERE is_global = 1 ORDER BY name ASC, points ASC";
            $stmt = Database::runPrepared($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grouped = [];
            foreach ($rows as $row) {
                $name = $row['name'];
                if (!isset($grouped[$name])) {
                    $grouped[$name] = [
                        'name' => $name,
                        'is_global' => 1,
                        'family_id' => $row['family_id'],
                        'levels' => []
                    ];
                }
                $grouped[$name]['levels'][] = [
                    'id' => $row['id'],
                    'level' => $row['level'],
                    'points' => $row['points'],
                    'image' => $row['image']
                ];
            }
            return json_encode([
                "status" => "success",
                "data" => array_values($grouped)
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function getByFamily($family_id)
    {
        try {
            $sql = "SELECT * FROM theme_rewards WHERE is_global = 1 OR family_id = ? ORDER BY is_global DESC, name ASC, points ASC";
            $stmt = Database::runPrepared($sql, [$family_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grouped = [];
            foreach ($rows as $row) {
                $name = $row['name'];
                $key = $name . '_' . $row['is_global'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'name' => $name,
                        'is_global' => $row['is_global'],
                        'family_id' => $row['family_id'],
                        'levels' => []
                    ];
                }
                $grouped[$key]['levels'][] = [
                    'id' => $row['id'],
                    'level' => $row['level'],
                    'points' => $row['points'],
                    'image' => $row['image']
                ];
            }
            return json_encode([
                "status" => "success",
                "data" => array_values($grouped)
            ]);
        } catch (PDOException $e) {
            return json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function add($data, $files = null)
    {
        try {
            $family_id = $data['family_id'] ?? null;
            $is_global = $data['is_global'] ?? ($family_id ? 0 : 1);
            $name = $data['name'];
            $levels = is_string($data['levels']) ? json_decode($data['levels'], true) : $data['levels'];

            Database::runPrepared("BEGIN");

            $sql = "INSERT INTO theme_rewards (family_id, is_global, name, image, level, points) VALUES (?, ?, ?, ?, ?, ?)";

            foreach ($levels as $index => $levelData) {
                $level = $levelData['level'];
                $points = $levelData['points'];

                $imagePath = null;
                if (isset($files['levels_image_' . $index]) && $files['levels_image_' . $index]['error'] == UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../public/uploads/theme_rewards/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = time() . '_' . basename($files['levels_image_' . $index]['name']);
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($files['levels_image_' . $index]['tmp_name'], $targetPath)) {
                        $imagePath = 'public/uploads/theme_rewards/' . $fileName;
                    }
                }

                Database::runPrepared($sql, [$family_id, $is_global, $name, $imagePath, $level, $points]);
            }

            Database::runPrepared("COMMIT");
            return json_encode(["status" => "success", "message" => "Theme added successfully"]);
        } catch (Exception $e) {
            Database::runPrepared("ROLLBACK");
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function edit($data, $files = null)
    {
        try {
            $family_id = $data['family_id'] ?? null;
            $is_global = $data['is_global'] ?? ($family_id ? 0 : 1);
            $old_name = $data['old_name'];
            $name = $data['name'];
            $levels = is_string($data['levels']) ? json_decode($data['levels'], true) : $data['levels'];

            Database::runPrepared("BEGIN");

            if ($is_global) {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND is_global = 1", [$old_name]);
            } else {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND family_id = ?", [$old_name, $family_id]);
            }

            $sql = "INSERT INTO theme_rewards (family_id, is_global, name, image, level, points) VALUES (?, ?, ?, ?, ?, ?)";

            foreach ($levels as $index => $levelData) {
                $level = $levelData['level'];
                $points = $levelData['points'];
                $imagePath = $levelData['existing_image'] ?? null;

                if (isset($files['levels_image_' . $index]) && $files['levels_image_' . $index]['error'] == UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../public/uploads/theme_rewards/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = time() . '_' . basename($files['levels_image_' . $index]['name']);
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($files['levels_image_' . $index]['tmp_name'], $targetPath)) {
                        $imagePath = 'public/uploads/theme_rewards/' . $fileName;
                    }
                }

                Database::runPrepared($sql, [$family_id, $is_global, $name, $imagePath, $level, $points]);
            }

            Database::runPrepared("COMMIT");
            return json_encode(["status" => "success", "message" => "Theme updated successfully"]);
        } catch (Exception $e) {
            Database::runPrepared("ROLLBACK");
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function delete($theme_name, $family_id = null, $is_global = false)
    {
        try {
            if ($is_global) {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND is_global = 1", [$theme_name]);
            } else {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND family_id = ?", [$theme_name, $family_id]);
            }
            return json_encode(["status" => "success", "message" => "Theme deleted successfully"]);
        } catch (PDOException $e) {
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
