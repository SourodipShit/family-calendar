<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/Reward.php';
require_once __DIR__ . '/../../classes/Points.php';
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

$token = $_COOKIE['family_view_token'] ?? null;
$family_id = false;
if ($token) {
    $family_id = FamilyViewDevice::verifyTokenAndGetFamilyId($token);
}

if (!$family_id) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        echo json_encode(Reward::getByFamilyId($family_id));
        break;

    case 'family_vault':
        echo json_encode(Reward::getFamilyVault($family_id));
        break;
        
    case 'my_vault':
        // Family view has no 'my' vault
        echo json_encode(['status' => 'success', 'data' => []]);
        break;

    case 'get_points':
        // Family view has no specific user, so points are 0
        echo json_encode(['status' => 'success', 'data' => ['balance' => 0]]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action for family view']);
        break;
}
