<?php
require_once __DIR__ . '/../classes/EventTypes.php';
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// Current family ID - assuming the first one for now or from session if set
$family_id = $_SESSION['user']['families'][0]['family_id'] ?? null;

if (!$family_id) {
    echo json_encode(['status' => 'error', 'message' => 'Family context not found']);
    exit;
}

switch ($action) {
    case 'list':
        $types = EventTypes::getEventTypesByFamilyId(['family_id' => $family_id]);
        echo json_encode(['status' => 'success', 'data' => $types]);
        break;

    case 'create':
        $data['family_id'] = $family_id;
        $data['is_default'] = 0; // User created types are not default
        echo EventTypes::createEventType($data);
        break;

    case 'update':
        // Safety check: Don't allow updating system default types via this API if needed
        // But the class updateEventType allows it. We should check is_default in DB first.
        $existing = EventTypes::getEventTypeById(['id' => $data['id']]);
        if ($existing && $existing['is_default']) {
            echo json_encode(['status' => 'error', 'message' => 'System defined types cannot be modified']);
            exit;
        }
        $data['family_id'] = $family_id;
        $data['is_default'] = 0;
        echo EventTypes::updateEventType($data);
        break;

    case 'delete':
        $existing = EventTypes::getEventTypeById(['id' => $data['id']]);
        if ($existing && $existing['is_default']) {
            echo json_encode(['status' => 'error', 'message' => 'System defined types cannot be deleted']);
            exit;
        }
        echo EventTypes::deleteEventType($data);
        break;

    case 'get':
        $type = EventTypes::getEventTypeById(['id' => $_GET['id']]);
        echo json_encode(['status' => 'success', 'data' => $type]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
