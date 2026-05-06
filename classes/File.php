<?php

class File
{
    public static function upload($file, $uploadDir = null, $fileName = null)
    {
        $default_dir = "../public/uploads";

        if ($uploadDir && strpos($uploadDir, '/') === 0) {
            $uploadDir = substr($uploadDir, 1);
        }

        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ["status" => "error", "message" => "No file uploaded."];
        }

        $uploadDir = $uploadDir ? $default_dir . "/" . $uploadDir : $default_dir;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($fileName === null) {
            $fileName = time() . '_' . basename($file['name']);
        }

        $filePath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                "status" => "success",
                "message" => "File uploaded successfully.",
                "filePath" => $filePath
            ];
        } else {
            return ["status" => "error", "message" => "Failed to upload file."];
        }
    }

    public static function deleteFile($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
            return ["status" => "success", "message" => "File deleted successfully."];
        } else {
            return ["status" => "error", "message" => "File not found."];
        }
    }
}
