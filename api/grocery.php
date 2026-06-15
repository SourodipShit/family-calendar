<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Grocery.php";
require_once __DIR__ . "/../config/Database.php";

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = $_GET['action'] ?? $data['action'] ?? '';

if (!$action) {
    echo json_encode(["status" => "error", "message" => "Action is required"]);
    exit;
}

switch ($action) {
    case 'add':
        // Expects ['list' => [...], 'items' => [...]]
        $result = Grocery::addNewList($data);
        echo json_encode($result);
        break;

    case 'update':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "List ID is required"]);
            break;
        }
        // Remove keys that are not columns in the database
        unset($data['id']);
        unset($data['action']);
        
        $result = Grocery::editList($id, $data);
        echo json_encode($result);
        break;

    case 'delete':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "List ID is required"]);
            break;
        }
        $result = Grocery::deleteList($id);
        echo json_encode($result);
        break;

    case 'get':
        $id = $_GET['id'] ?? $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "List ID is required"]);
            break;
        }
        $result = Grocery::getGroceryList($id);
        echo json_encode($result);
        break;

    case 'list':
        $family_id = $_GET['family_id'] ?? $data['family_id'] ?? $_SESSION['user']['active_family_id'] ?? null;
        if (!$family_id) {
            echo json_encode(["status" => "error", "message" => "Family ID is required"]);
            break;
        }
        $result = Grocery::getFamilyLists($family_id);
        echo json_encode($result);
        break;

    case 'getByDate':
        $family_id = $_GET['family_id'] ?? $data['family_id'] ?? $_SESSION['user']['active_family_id'] ?? null;
        $start = $_GET['startDate'] ?? $data['startDate'] ?? null;
        $end = $_GET['endDate'] ?? $data['endDate'] ?? null;
        if (!$family_id || !$start || !$end) {
            echo json_encode(["status" => "error", "message" => "Missing required parameters"]);
            break;
        }
        $result = Grocery::getByDateRange($family_id, $start, $end);
        echo json_encode($result);
        break;


    case 'addItem':
        $result = Grocery::addItems($data);
        echo json_encode($result);
        break;

    case 'updateItem':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Item ID is required"]);
            break;
        }
        $result = Grocery::editItems($id, $data);
        echo json_encode($result);
        break;

    case 'deleteItem':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Item ID is required"]);
            break;
        }
        $result = Grocery::deleteItem($id);
        echo json_encode($result);
        break;

    case 'toggleItem':
        $id = $data['id'] ?? null;
        $is_complete = $data['is_complete'] ?? false;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Item ID is required"]);
            break;
        }
        $result = Grocery::toggleItemStatus($id, $is_complete);
        echo json_encode($result);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

