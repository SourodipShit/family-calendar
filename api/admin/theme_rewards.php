<?php
require_once __DIR__ . '/../../classes/ThemeReward.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic admin check placeholder based on other files
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        echo ThemeReward::getAll();
        break;

    case 'create':
        if (empty($data['name']) || empty($data['levels'])) {
            echo json_encode(['status' => 'error', 'message' => 'Theme name and levels are required']);
            exit;
        }
        echo ThemeReward::add($data, $_FILES);
        break;

    case 'update':
        if (empty($data['old_name']) || empty($data['name']) || empty($data['levels'])) {
            echo json_encode(['status' => 'error', 'message' => 'Old name, new name, and levels are required']);
            exit;
        }
        echo ThemeReward::edit($data, $_FILES);
        break;

    case 'delete':
        $theme_name = $data['name'] ?? $_GET['name'] ?? null;
        if (empty($theme_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Theme name is required']);
            exit;
        }
        echo ThemeReward::delete($theme_name, null, true); // Admin deletes are global
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
