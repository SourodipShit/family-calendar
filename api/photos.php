<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Photo.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';
$family_id = $_POST['family_id'] ?? $_SESSION['user']['families'][0]['family_id'] ?? null;

if (!$family_id) {
    echo json_encode(["status" => "error", "message" => "No family selected"]);
    exit;
}

if ($action == 'upload') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $data = [
            'file' => $_FILES['file'],
            'family_id' => $family_id,
            'size' => $_FILES['file']['size'], // The class logic will override this with the compressed size
            'metadata' => $_POST['metadata'] ?? null,
            'uploaded_by' => $_SESSION['user']['id']
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

if ($action == 'getPending') {
    $result = Photo::getPendingPhotos($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getByFamily') {
    $result = Photo::getByFamily($family_id);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
