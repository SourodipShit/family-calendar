<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/File.php";

class ThemeReward
{
    public static function getAll()
    {
        try {
            $sql = "SELECT tr.*, f.name as family_name 
                    FROM theme_rewards tr 
                    LEFT JOIN families f ON tr.family_id = f.id 
                    ORDER BY tr.is_global DESC, tr.name ASC, tr.points ASC";
            $stmt = Database::runPrepared($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $grouped = [];
            foreach ($rows as $row) {
                $name = $row['name'];
                $family_id = $row['family_id'] ?? 0;
                $is_global = $row['is_global'];
                $key = $name . '_' . $family_id . '_' . $is_global;
                
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'name' => $name,
                        'is_global' => $is_global,
                        'family_id' => $row['family_id'],
                        'family_name' => $row['family_name'],
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
            $family_id = !empty($data['family_id']) ? $data['family_id'] : null;
            $is_global = $data['is_global'] ?? ($family_id ? 0 : 1);
            $name = $data['name'];
            $levels = is_string($data['levels']) ? json_decode($data['levels'], true) : $data['levels'];

            Database::runPrepared("BEGIN");

            $sql = "INSERT INTO theme_rewards (family_id, is_global, name, image, level, points) VALUES (?, ?, ?, ?, ?, ?)";

            foreach ($levels as $index => $levelData) {
                $level = $levelData['level'];
                $points = $levelData['points'];
                $frontend_id = $levelData['frontend_id'] ?? $index;

                $imagePath = null;
                if (isset($files['levels_image_' . $frontend_id]) && $files['levels_image_' . $frontend_id]['error'] == UPLOAD_ERR_OK) {
                    $uploadResult = File::upload($files['levels_image_' . $frontend_id], 'theme_rewards');
                    if ($uploadResult['status'] === 'success') {
                        // File::upload returns something like "../public/uploads/theme_rewards/name.jpg"
                        // Strip the leading "../"
                        $imagePath = preg_replace('/^\.\.\//', '', $uploadResult['filePath']);
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
            $family_id = !empty($data['family_id']) ? $data['family_id'] : null;
            $is_global = $data['is_global'] ?? ($family_id ? 0 : 1);
            $old_name = $data['old_name'];
            $name = $data['name'];
            $levels = is_string($data['levels']) ? json_decode($data['levels'], true) : $data['levels'];

            Database::runPrepared("BEGIN");

            // Fetch old images before deleting
            if ($is_global) {
                $stmt = Database::runPrepared("SELECT image FROM theme_rewards WHERE name = ? AND is_global = 1", [$old_name]);
            } else {
                $stmt = Database::runPrepared("SELECT image FROM theme_rewards WHERE name = ? AND family_id = ?", [$old_name, $family_id]);
            }
            $oldImagesRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($is_global) {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND is_global = 1", [$old_name]);
            } else {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND family_id = ?", [$old_name, $family_id]);
            }

            $sql = "INSERT INTO theme_rewards (family_id, is_global, name, image, level, points) VALUES (?, ?, ?, ?, ?, ?)";
            $keptImages = [];

            foreach ($levels as $index => $levelData) {
                $level = $levelData['level'];
                $points = $levelData['points'];
                $imagePath = $levelData['existing_image'] ?? null;
                $frontend_id = $levelData['frontend_id'] ?? $index;

                if (isset($files['levels_image_' . $frontend_id]) && $files['levels_image_' . $frontend_id]['error'] == UPLOAD_ERR_OK) {
                    $uploadResult = File::upload($files['levels_image_' . $frontend_id], 'theme_rewards');
                    if ($uploadResult['status'] === 'success') {
                        $imagePath = preg_replace('/^\.\.\//', '', $uploadResult['filePath']);
                    }
                }

                if ($imagePath) {
                    $keptImages[] = $imagePath;
                }

                Database::runPrepared($sql, [$family_id, $is_global, $name, $imagePath, $level, $points]);
            }

            Database::runPrepared("COMMIT");

            // Delete unused old images
            foreach ($oldImagesRows as $row) {
                if (!empty($row['image']) && !in_array($row['image'], $keptImages)) {
                    $filePath = __DIR__ . '/../' . $row['image'];
                    File::deleteFile($filePath);
                }
            }

            return json_encode(["status" => "success", "message" => "Theme updated successfully"]);
        } catch (Exception $e) {
            Database::runPrepared("ROLLBACK");
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public static function delete($theme_name, $family_id = null, $is_global = false)
    {
        try {
            // Fetch images before deleting
            if ($is_global) {
                $stmt = Database::runPrepared("SELECT image FROM theme_rewards WHERE name = ? AND is_global = 1", [$theme_name]);
            } else {
                $stmt = Database::runPrepared("SELECT image FROM theme_rewards WHERE name = ? AND family_id = ?", [$theme_name, $family_id]);
            }
            $imagesRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($is_global) {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND is_global = 1", [$theme_name]);
            } else {
                Database::runPrepared("DELETE FROM theme_rewards WHERE name = ? AND family_id = ?", [$theme_name, $family_id]);
            }

            // Delete images from filesystem
            foreach ($imagesRows as $row) {
                if (!empty($row['image'])) {
                    $filePath = __DIR__ . '/../' . $row['image'];
                    File::deleteFile($filePath);
                }
            }

            return json_encode(["status" => "success", "message" => "Theme deleted successfully"]);
        } catch (PDOException $e) {
            return json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
