<?php
require_once __DIR__ . '/../classes/User.php';
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
$user_id = $_SESSION['user']['id'];

switch ($action) {
    case 'get':
        $settings = User::getUserSettings($user_id);
        echo json_encode(['status' => 'success', 'data' => $settings]);
        exit;

    case 'update':
        $new_settings = $data['settings'] ?? [];
        
        // Merge with existing settings
        $existing_settings = json_decode($_SESSION['user']['settings'] ?? '{}', true);
        if (!is_array($existing_settings)) {
            $existing_settings = [];
        }
        $settings = array_merge($existing_settings, $new_settings);

        $result = User::updateUserSettings($settings, $user_id);
        
        if ($result['status'] === 'success') {
            // Update session settings
            $_SESSION['user']['settings'] = json_encode($settings);
            
            // Also update the multi-session store if it exists
            if (isset($_SESSION['active_account_id']) && isset($_SESSION['accounts'][$_SESSION['active_account_id']])) {
                $_SESSION['accounts'][$_SESSION['active_account_id']]['settings'] = json_encode($settings);
            }
        }
        
        echo json_encode($result);
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}
