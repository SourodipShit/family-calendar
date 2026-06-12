<?php

class File
{
    private static function compressImageGD($source, $destination, $quality = 75)
    {
        $info = @getimagesize($source);

        if ($info === false) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];

        if ($info['mime'] == 'image/jpeg') {
            $image = @imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = @imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = @imagecreatefrompng($source);
            @imagepalettetotruecolor($image);
        } else {
            return false;
        }

        if (!$image) {
            return false;
        }

        // Set maximum dimensions to ensure file size drops significantly
        $maxWidth = 1200;
        $maxHeight = 1200;

        // Resize if the image is larger than the maximum dimensions
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Fill with white background for transparent PNGs/GIFs
            $white = imagecolorallocate($newImage, 255, 255, 255);
            imagefill($newImage, 0, 0, $white);

            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            @imagedestroy($image);
            $image = $newImage;
        }

        // Save image with compression as JPEG
        $success = @imagejpeg($image, $destination, $quality);

        // Free up memory
        @imagedestroy($image);

        return $success;
    }
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
            $fileName = uniqid(time() . '_') . '_' . basename($file['name']);
        }

        $filePath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if the uploaded file is an image and compress it
            $info = @getimagesize($filePath);
            if ($info !== false && in_array($info['mime'], ['image/jpeg', 'image/gif', 'image/png'])) {
                // Determine new filename with .jpg extension since compressImageGD saves as JPEG
                $newFilePath = preg_replace('/\.[^.]+$/', '.jpg', $filePath);

                // Compress the image
                if (self::compressImageGD($filePath, $newFilePath, 75)) {
                    // Remove the original file if the extension changed
                    if ($filePath !== $newFilePath && file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $filePath = $newFilePath;
                }
            }

            $width = null;
            $height = null;
            $finalInfo = @getimagesize($filePath);
            if ($finalInfo !== false) {
                $width = $finalInfo[0];
                $height = $finalInfo[1];
            }

            return [
                "status" => "success",
                "message" => "File uploaded successfully.",
                "filePath" => $filePath,
                "fileSize" => filesize($filePath),
                "width" => $width,
                "height" => $height,
                "originalName" => basename($file['name'])
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
