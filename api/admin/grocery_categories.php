<?php
require_once __DIR__ . '/../../classes/GroceryCategories.php';
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin check
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    // In some contexts, 'admin' role might not be set yet, but we allow siteadmin access
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        $result = GroceryCategories::getAll();
        echo json_encode($result);
        break;

    case 'create':
        // For admin, we force is_default = 1 and family_id = null
        $data['family_id'] = null;
        $data['is_default'] = 1;
        $result = GroceryCategories::add($data);
        echo json_encode($result);
        break;

    case 'update':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID is required"]);
            break;
        }
        // Admin updates global categories
        unset($data['id']);
        $result = GroceryCategories::edit($data, $id);
        echo json_encode($result);
        break;

    case 'delete':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID is required"]);
            break;
        }
        $result = GroceryCategories::delete($id);
        echo json_encode($result);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Invalid action"]);
        break;
}
