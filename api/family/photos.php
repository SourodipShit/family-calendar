<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../../classes/Photo.php";
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

$token = $_COOKIE['family_view_token'] ?? null;
$family_id = false;
if ($token) {
    $family_id = FamilyViewDevice::verifyTokenAndGetFamilyId($token);
}

if (!$family_id) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action == 'getByFamily') {
    $result = Photo::getByFamily($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getStorageDetails') {
    $result = Photo::getPhotoStorageDetails($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getPending') {
    $result = Photo::getPendingPhotos($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'upload') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $data = [
            'file' => $_FILES['file'],
            'family_id' => $family_id,
            'size' => $_FILES['file']['size'],
            'metadata' => $_POST['metadata'] ?? null,
            'uploaded_by' => null // Family view might not have a specific user logged in, or we might need to handle this.
        ];
        
        $result = Photo::uploadPhoto($data);
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded or upload error"]);
    }
    exit;
}

if ($action == 'approve') {
    $photoId = $_POST['id'] ?? null;
    if ($photoId) {
        $result = Photo::approvePhoto($photoId);
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "Missing photo ID"]);
    }
    exit;
}

if ($action == 'delete') {
    $photoId = $_POST['id'] ?? null;
    if ($photoId) {
        $result = Photo::deletePhoto($photoId);
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "Missing photo ID"]);
    }
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action or action not allowed in family view"]);
exit;
