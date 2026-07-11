<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Family.php';
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

$token = $_COOKIE['family_view_token'] ?? null;
$family_id = false;
if ($token) {
    $family_id = FamilyViewDevice::verifyTokenAndGetFamilyId($token);
}

if (!$family_id) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized."]);
    exit;
}

echo json_encode(Family::getMembersByFamilyId($family_id));
exit;
