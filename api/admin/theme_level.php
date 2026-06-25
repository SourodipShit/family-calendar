<?php
require_once __DIR__ . '/../../classes/ThemeLavel.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is an admin or has the right permissions (basic check placeholder based on other files)
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        echo ThemeLavel::getAll();
        break;

    case 'create':
        // Expects 'name' in $data
        if (empty($data['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            exit;
        }
        echo ThemeLavel::add([$data['name']]);
        break;

    case 'update':
        // Expects 'id' and 'name' in $data
        if (empty($data['id']) || empty($data['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID and Name are required']);
            exit;
        }
        echo ThemeLavel::update([$data['name'], $data['id']]);
        break;

    case 'delete':
        // Expects 'id' in $data or GET
        $id = $data['id'] ?? $_GET['id'] ?? null;
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            exit;
        }
        echo ThemeLavel::delete($id);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
