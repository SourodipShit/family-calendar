<?php
require_once __DIR__ . '/../../classes/CoachCategory.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin authentication (assuming basic admin check here, adapt as needed)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => false, "message" => "Unauthorized access"]);
    exit;
}

$action = $_GET['action'] ?? 'list';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        $result = CoachCategory::getAll();
        echo json_encode($result);
        break;

    case 'create':
        $result = CoachCategory::add($data);
        echo json_encode($result);
        break;

    case 'update':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID is required"]);
            break;
        }
        unset($data['id']); // Remove id from data payload if it exists
        $result = CoachCategory::edit($data, $id);
        echo json_encode($result);
        break;

    case 'delete':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => false, "message" => "ID is required"]);
            break;
        }
        $result = CoachCategory::delete($id);
        echo json_encode($result);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Invalid action"]);
        break;
}
