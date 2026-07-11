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

// For any remaining UI components trying to fetch these
if ($action == 'getStorageDetails') {
    echo json_encode(["status" => "success", "data" => ["total_storage" => 0, "allocated_storage" => 500]]);
    exit;
}

if ($action == 'getPending') {
    echo json_encode(["status" => "success", "data" => []]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action or action not allowed in family view"]);
exit;
