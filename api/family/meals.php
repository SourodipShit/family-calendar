<?php
require_once __DIR__ . "/../../classes/Meals.php";
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

header('Content-Type: application/json');

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

if ($action == 'getByDateRange') {
    // We pass null for user_id so it fetches all meals for the family
    $user_id = null;
    $startDate = $_POST['startDate'] ?? $_GET['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? $_GET['endDate'] ?? '';
    
    $result = Meals::getByDateRange($startDate, $endDate, $family_id, $user_id);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action for family view"]);
exit;
