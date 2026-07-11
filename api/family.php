<?php
require_once __DIR__ . '/../classes/Family.php';
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// Current family ID
$family_id = $_SESSION['user']['active_family_id'] ?? null;

if (!$family_id) {
    echo json_encode(['status' => 'error', 'message' => 'Family context not found']);
    exit;
}

switch ($action) {
    case 'get':
        $family = Family::getFamily($family_id);
        echo json_encode(['status' => 'success', 'data' => $family]);
        exit;

    case 'update':
        $data['id'] = $family_id;
        $result = Family::update($data);
        if ($result['status'] === 'success') {
            $isEnabled = (isset($data['family_view_enabled']) && $data['family_view_enabled'] == '1') ? 1 : 0;
            if (isset($_SESSION['user']['active_family'])) {
                $_SESSION['user']['active_family']['family_view_enabled'] = $isEnabled;
            }
            if (isset($_SESSION['active_account_id']) && isset($_SESSION['accounts'][$_SESSION['active_account_id']]['active_family'])) {
                $_SESSION['accounts'][$_SESSION['active_account_id']]['active_family']['family_view_enabled'] = $isEnabled;
            }
        }
        echo json_encode($result);
        exit;
    
    case 'verify_family_view':
        $pin = $data['pin'] ?? '';
        require_once __DIR__ . '/../classes/Auth.php';
        $result = Auth::verifyFamilyView($family_id, $pin);
        if ($result['status'] === 'success') {
            $_SESSION['family_view_authenticated'] = true;
        }
        echo json_encode($result);
        exit;

    case 'updateSettings':
        $result = Family::updateFamilySettings($data['settings'], $family_id);
        if ($result['status'] === 'success') {
            $_SESSION['user']['active_family']['settings'] = json_encode($data['settings']);
        }
        echo json_encode($result);
        exit;

    case 'leave':
        $leave_family_id = $data['family_id'] ?? null;
        $user_id = $_SESSION['user']['id'];
        if (!$leave_family_id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing family ID']);
            exit;
        }
        try {
            $count = Database::runPrepared("SELECT COUNT(*) as c FROM user_family WHERE user_id = ?", [$user_id])->fetch()['c'];
            if ($count <= 1) {
                echo json_encode(['status' => 'error', 'message' => 'You cannot leave your only family.']);
                exit;
            }
            Database::runPrepared("DELETE FROM user_family WHERE user_id = ? AND family_id = ?", [$user_id, $leave_family_id]);
            
            if ($leave_family_id == $family_id) {
                // If they leave the active family, clear session families to force re-evaluation on reload
                unset($_SESSION['user']['families']);
                echo json_encode(['status' => 'success', 'redirect' => true, 'message' => 'Left family successfully.']);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Left family successfully.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to leave family.']);
        }
        exit;

    case 'getMembers':
        $members = Family::getMembersByFamilyId($family_id);
        echo json_encode(['status' => 'success', 'data' => $members]);
        exit;

    case 'removeMember':
        $remove_user_id = $data['user_id'] ?? null;
        if (!$remove_user_id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
            exit;
        }
        if ($remove_user_id == $_SESSION['user']['id']) {
            echo json_encode(['status' => 'error', 'message' => 'You cannot remove yourself. Use the leave family option.']);
            exit;
        }
        try {
            Database::runPrepared("DELETE FROM user_family WHERE user_id = ? AND family_id = ?", [$remove_user_id, $family_id]);
            echo json_encode(['status' => 'success', 'message' => 'Member removed successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove member.']);
        }
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

