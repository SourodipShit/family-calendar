<?php
session_start();
require_once __DIR__ . '/../classes/UserList.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'addList':
        $data = [
            'user_id' => $userId,
            'name' => $_POST['name'] ?? ''
        ];
        if (empty($data['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'List name is required']);
            break;
        }
        echo json_encode(UserList::addList($data));
        break;

    case 'editList':
        $data = [
            'id' => $_POST['id'] ?? 0,
            'name' => $_POST['name'] ?? ''
        ];
        if (empty($data['name']) || empty($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'List ID and name are required']);
            break;
        }
        echo json_encode(UserList::editList($data));
        break;

    case 'deleteList':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'List ID is required']);
            break;
        }
        echo json_encode(UserList::delete($id));
        break;

    case 'GetByUserId':
        echo json_encode(UserList::GetByUserId($userId));
        break;

    case 'addItem':
        $data = [
            'list_id' => $_POST['list_id'] ?? 0,
            'content' => $_POST['content'] ?? ''
        ];
        if (empty($data['list_id']) || empty($data['content'])) {
            echo json_encode(['status' => 'error', 'message' => 'List ID and content are required']);
            break;
        }
        echo json_encode(UserList::addItem($data));
        break;

    case 'editItem':
        $data = [
            'id' => $_POST['id'] ?? 0,
            'content' => $_POST['content'] ?? ''
        ];
        if (empty($data['id']) || empty($data['content'])) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID and content are required']);
            break;
        }
        echo json_encode(UserList::editItem($data));
        break;

    case 'deleteItem':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID is required']);
            break;
        }
        echo json_encode(UserList::DeleteItem($id));
        break;

    case 'getItemsByListId':
        $list_id = $_GET['list_id'] ?? $_POST['list_id'] ?? 0;
        if (empty($list_id)) {
            echo json_encode(['status' => 'error', 'message' => 'List ID is required']);
            break;
        }
        echo json_encode(UserList::getItemsByListId($list_id));
        break;

    case 'CheckItem':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID is required']);
            break;
        }
        echo json_encode(UserList::CheckItem($id));
        break;

    case 'uncheckItem':
        $id = $_POST['id'] ?? 0;
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID is required']);
            break;
        }
        echo json_encode(UserList::uncheck($id));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
