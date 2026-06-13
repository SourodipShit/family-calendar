<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/User.php";
require_once __DIR__ . "/File.php";


class Photo
{
    public static function getByFamily($familyId)
    {
        $sql = "SELECT * FROM photos WHERE family_id = ? AND status = 'approved' ORDER BY created_at DESC";

        $photos = Database::runPrepared($sql, [$familyId])->fetchAll(PDO::FETCH_ASSOC);

        foreach ($photos as &$photo) {
            $user = User::getUserById($photo['uploaded_by']);
            $photo['user'] = $user;
        }

        if (empty($photos)) {
            return ["status" => "success", "message" => "No photos found", "data" => []];
        }

        return ["status" => "success", "message" => "Photos fetched successfully", "data" => $photos];
    }

    public static function getPendingPhotos($familyId)
    {
        $sql = "SELECT * FROM photos WHERE family_id = ? AND status = 'pending' ORDER BY created_at DESC";

        $photos = Database::runPrepared($sql, [$familyId])->fetchAll(PDO::FETCH_ASSOC);

        foreach ($photos as &$photo) {
            $user = User::getUserById($photo['uploaded_by']);
            $photo['user'] = $user;
        }

        if (empty($photos)) {
            return ["status" => "success", "message" => "No photos found", "data" => []];
        }

        return ["status" => "success", "message" => "Photos fetched successfully", "data" => $photos];
    }

    public static function uploadPhoto($data)
    {
        $file = $data['file'];
        $result = File::upload($file, 'photos');
        if ($result['status'] === 'error') {
            return $result;
        }

        $existingMeta = isset($data['metadata']) ? json_decode($data['metadata'], true) : [];
        if (!is_array($existingMeta)) $existingMeta = [];

        $existingMeta['original_name'] = $result['originalName'] ?? basename($file['name']);
        if (isset($result['width'])) $existingMeta['width'] = $result['width'];
        if (isset($result['height'])) $existingMeta['height'] = $result['height'];

        $metadataJson = json_encode($existingMeta);

        Database::runPrepared("INSERT INTO photos(family_id, photo, file_size, metadata, uploaded_by) VALUES(?, ?, ?, ?, ?)", [
            $data['family_id'],
            $result['filePath'],
            $result['fileSize'],
            $metadataJson,
            $data['uploaded_by']
        ]);

        return ["status" => "success", "message" => "Photo uploaded successfully"];
    }

    public static function approvePhoto($photoId)
    {
        $result = Database::runPrepared("UPDATE photos SET status = 'approved' WHERE id = ?", [$photoId]);
        return ["status" => "success", "message" => "Photo approved successfully"];
    }

    public static function deletePhoto($photoId)
    {
        $result = Database::runPrepared("SELECT photo FROM photos WHERE id = ?", [$photoId])->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return ["status" => "error", "message" => "Photo not found"];
        }
        File::deleteFile($result['photo']);
        $result = Database::runPrepared("UPDATE photos SET status = 'deleted' WHERE id = ?", [$photoId]);
        return ["status" => "success", "message" => "Photo deleted successfully"];
    }

    public static function getPhotoStorageDetails($familyId)
    {
        $family = Database::runPrepared("SELECT storage_allocated FROM families WHERE id = ?", [$familyId])->fetch(PDO::FETCH_ASSOC);
        if (!$family) {
            return ["status" => "error", "message" => "Family not found"];
        }

        $allocatedStorage = (float)$family['storage_allocated'];

        $sql = "SELECT 
                    SUM(CASE WHEN status = 'approved' THEN file_size ELSE 0 END) as approved_bytes,
                    SUM(CASE WHEN status = 'pending' THEN file_size ELSE 0 END) as pending_bytes
                FROM photos 
                WHERE family_id = ?";
                
        $stats = Database::runPrepared($sql, [$familyId])->fetch(PDO::FETCH_ASSOC);

        $approvedBytes = $stats['approved_bytes'] ? (float)$stats['approved_bytes'] : 0;
        $pendingBytes = $stats['pending_bytes'] ? (float)$stats['pending_bytes'] : 0;

        $approvedMb = round($approvedBytes / (1024 * 1024), 2);
        $notApprovedMb = round($pendingBytes / (1024 * 1024), 2);
        $totalMb = round(($approvedBytes + $pendingBytes) / (1024 * 1024), 2);

        return [
            "status" => "success",
            "data" => [
                "allocated_storage" => $allocatedStorage,
                "approved_storage" => $approvedMb,
                "not_approved_storage" => $notApprovedMb,
                "total_storage" => $totalMb
            ]
        ];
    }
}
