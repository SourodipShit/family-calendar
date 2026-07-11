<?php
require_once __DIR__ . '/../../classes/EventTypes.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

header('Content-Type: application/json');

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
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        $types = EventTypes::getEventTypesByFamilyId(['family_id' => $family_id]);
        echo json_encode(['status' => 'success', 'data' => $types]);
        break;

    case 'get':
        $type = EventTypes::getEventTypeById(['id' => $_GET['id']]);
        echo json_encode(['status' => 'success', 'data' => $type]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action for family view']);
        break;
}
