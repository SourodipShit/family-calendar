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
        
    case 'redeem':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $reward_id = $data['reward_id'] ?? $_GET['reward_id'] ?? null;
        $user_id = $data['user_id'] ?? $_GET['user_id'] ?? null;
        if (empty($reward_id) || empty($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Reward ID and User ID are required']);
            exit;
        }
        echo json_encode(Reward::redeem($reward_id, $user_id, $family_id));
        break;

    case 'my_vault':
        $user_id = $_GET['user_id'] ?? null;
        if (empty($user_id)) {
            echo json_encode(['status' => 'success', 'data' => []]);
        } else {
            echo json_encode(Reward::getMyVault($user_id, $family_id));
        }
        break;

    case 'get_points':
        $user_id = $_GET['user_id'] ?? null;
        if (empty($user_id)) {
            echo json_encode(['status' => 'success', 'data' => ['balance' => 0]]);
        } else {
            echo json_encode(Points::getPoints($user_id));
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action for family view']);
        break;
}
