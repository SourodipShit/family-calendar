<?php
require_once __DIR__ . '/../classes/Reward.php';
require_once __DIR__ . '/../classes/Points.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['active_family_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or no active family']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$family_id = $_SESSION['user']['active_family_id'];

switch ($action) {
    case 'list':
        echo json_encode(Reward::getByFamilyId($family_id));
        break;

    case 'create':
        if (empty($data['title']) || !isset($data['price'])) {
            echo json_encode(['status' => 'error', 'message' => 'Title and price are required']);
            exit;
        }
        $data['family_id'] = $family_id;
        
        $countResult = Reward::getRewardCountByFamilyId($family_id);
        if ($countResult['status'] === 'success' && $countResult['data'] >= 10) {
            echo json_encode(['status' => 'error', 'message' => 'You can only have a maximum of 10 rewards. Please delete an existing reward first.']);
            exit;
        }
        
        $file = isset($_FILES['image']) ? $_FILES['image'] : null;
        echo json_encode(Reward::add($data, $file));
        break;

    case 'update':
        if (empty($data['id']) || empty($data['title']) || !isset($data['price'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID, title, and price are required']);
            exit;
        }
        $data['family_id'] = $family_id;
        
        $file = isset($_FILES['image']) ? $_FILES['image'] : null;
        echo json_encode(Reward::edit($data, $file));
        break;

    case 'delete':
        $reward_id = $data['id'] ?? $_GET['id'] ?? null;
        if (empty($reward_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Reward ID is required']);
            exit;
        }
        echo json_encode(Reward::delete($reward_id, $family_id));
        break;

    case 'redeem':
        $reward_id = $data['reward_id'] ?? $_GET['reward_id'] ?? null;
        if (empty($reward_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Reward ID is required']);
            exit;
        }
        $user_id = $_SESSION['user']['id'];
        echo json_encode(Reward::redeem($reward_id, $user_id, $family_id));
        break;

    case 'my_vault':
        $user_id = $_SESSION['user']['id'];
        echo json_encode(Reward::getMyVault($user_id, $family_id));
        break;

    case 'family_vault':
        echo json_encode(Reward::getFamilyVault($family_id));
        break;

    case 'get_points':
        $user_id = $_SESSION['user']['id'];
        echo json_encode(Points::getPoints($user_id));
        break;

    case 'complete_redemption':
        $redeem_id = $data['redeem_id'] ?? $_GET['redeem_id'] ?? null;
        if (empty($redeem_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Redeem ID is required']);
            exit;
        }
        echo json_encode(Reward::completeRedemption($redeem_id, $family_id));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
