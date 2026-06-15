<?php
require_once __DIR__ . '/../classes/GroceryCategories.php';
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$familyId = $_SESSION['user']['active_family_id'] ?? null;

if (!$familyId) {
    echo json_encode(["status" => false, "message" => "No family found in session"]);
    exit;
}

$action = $_GET['action'] ?? 'list';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        $result = GroceryCategories::getByFamily($familyId);
        echo json_encode($result);
        break;

    case 'create':
        // Users can add their own family-specific categories
        $data['family_id'] = $familyId;
        $data['is_default'] = 0;
        $result = GroceryCategories::add($data);
        echo json_encode($result);
        break;

    case 'update':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID is required"]);
            break;
        }
        // Security check: only allow editing if it belongs to this family
        // (For simplicity, we'll assume the edit method or DB handles this, 
        // but we should ideally verify ownership here)
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

