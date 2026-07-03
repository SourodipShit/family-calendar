<?php
session_start();

// Make sure to include dependencies
require_once __DIR__ . '/../config/Database.php'; 
require_once __DIR__ . '/../classes/Chore.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getChores':
        getChoresByDateRange();
        break;
    case 'addChore':
        addChore();
        break;
    case 'editChore':
        editChore();
        break;
    case 'deleteChore':
        deleteChore();
        break;
    case 'requestComplete':
        requestComplete();
        break;
    case 'approveChore':
        approveChore();
        break;
    case 'skipChore':
        skipChore();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getChoresByDateRange()
{
    try {
        $familyId = $_SESSION['user']['active_family_id'];
        $userId = $_SESSION['user']['id'];
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');

        $chores = Chore::getByDateRange($start, $end, $familyId, $userId);
        echo json_encode($chores);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function addChore()
{
    try {
        $data = $_POST;
        $data['family_id'] = $_SESSION['user']['active_family_id'];
        $data['created_by'] = $_SESSION['user']['id'];

        $result = Chore::add($data);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}

function editChore()
{
    try {
        $data = $_POST;
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'Missing chore ID', 'status' => 'error']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $result = Chore::edit($id, $data, $userId);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}

function deleteChore()
{
    try {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'Missing chore ID', 'status' => 'error']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $result = Chore::delete($id, $userId);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}

function requestComplete()
{
    try {
        $instanceId = $_POST['instance_id'] ?? null;
        if (!$instanceId) {
            echo json_encode(['error' => 'Missing instance ID', 'status' => 'error']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $result = Chore::requestComplete($instanceId, $userId);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}

function approveChore()
{
    try {
        $instanceId = $_POST['instance_id'] ?? null;
        if (!$instanceId) {
            echo json_encode(['error' => 'Missing instance ID', 'status' => 'error']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $userRole = $_SESSION['user']['role'] ?? 'member';
        
        $result = Chore::approve($instanceId, $userId, $userRole);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}

function skipChore()
{
    try {
        $instanceId = $_POST['instance_id'] ?? null;
        if (!$instanceId) {
            echo json_encode(['error' => 'Missing instance ID', 'status' => 'error']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $userRole = $_SESSION['user']['role'] ?? 'member';

        $result = Chore::skip($instanceId, $userId, $userRole);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}
