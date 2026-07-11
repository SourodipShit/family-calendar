<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/Database.php'; 
require_once __DIR__ . '/../../classes/Chore.php';
require_once __DIR__ . '/../../classes/FamilyViewDevice.php';

$token = $_COOKIE['family_view_token'] ?? null;
$family_id = false;
if ($token) {
    $family_id = FamilyViewDevice::verifyTokenAndGetFamilyId($token);
}

if (!$family_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getChores':
        getChoresByDateRange($family_id);
        break;
    case 'requestComplete':
        requestComplete($family_id);
        break;
    default:
        echo json_encode(['error' => 'Invalid action for family view']);
        break;
}

function getChoresByDateRange($familyId)
{
    try {
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');

        // null user id to get all chores for the family
        $chores = Chore::getByDateRange($start, $end, $familyId, null);
        echo json_encode($chores);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function requestComplete($familyId)
{
    try {
        $instanceId = $_POST['instance_id'] ?? null;
        if (!$instanceId) {
            echo json_encode(['error' => 'Missing instance ID', 'status' => 'error']);
            return;
        }

        // Family view doesn't have a user ID tied to it
        // The Chore class uses user id 0 or null for "system/family view"
        $result = Chore::requestComplete($instanceId, 0);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'status' => 'error']);
    }
}
