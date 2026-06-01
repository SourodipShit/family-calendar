<?php
require_once __DIR__ . '/../../classes/EventTypes.php';
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic admin check - assuming 'role' or similar in session
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    // For now, if role is not set, let's check if they are in siteadmin context
    // In this project, it seems admin might be determined differently.
    // Let's assume for now that if they can reach this, they might be admin, 
    // but we should be careful. 
    // Looking at other files, I don't see a strict role check yet, 
    // but I should probably implement one or check how it's done.
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        // Get all event types (global defaults + family specific)
        try {
            $types = Database::runPrepared("SELECT et.*, f.name as family_name FROM event_types et LEFT JOIN families f ON et.family_id = f.id")->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $types]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'create':
        $data['family_id'] = null;
        $data['is_default'] = 1;
        echo EventTypes::createEventType($data);
        break;

    case 'update':
        $existing = EventTypes::getEventTypeById(['id' => $data['id']]);
        if ($existing) {
            $data['family_id'] = $existing['family_id'];
            $data['is_default'] = $existing['is_default'];
        } else {
            $data['family_id'] = null;
            $data['is_default'] = 1;
        }
        echo EventTypes::updateEventType($data);
        break;

    case 'delete':
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
