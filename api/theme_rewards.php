<?php
require_once __DIR__ . '/../classes/ThemeReward.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['active_family_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or no active family']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$family_id = $_SESSION['user']['active_family_id'];

switch ($action) {
    case 'list':
        echo ThemeReward::getByFamily($family_id);
        break;

    case 'create':
        if (empty($data['name']) || empty($data['levels'])) {
            echo json_encode(['status' => 'error', 'message' => 'Theme name and levels are required']);
            exit;
        }
        
        $response = json_decode(ThemeReward::getByFamily($family_id), true);
        $custom_count = 0;
        if (isset($response['data'])) {
            foreach ($response['data'] as $r) {
                if ($r['is_global'] == 0 && $r['family_id'] == $family_id) {
                    $custom_count++;
                }
            }
        }
        if ($custom_count >= 1) {
            echo json_encode(['status' => 'error', 'message' => 'Your family can only create 1 custom theme reward. Please edit the existing one.']);
            exit;
        }

        $data['family_id'] = $family_id;
        $data['is_global'] = 0;
        echo ThemeReward::add($data, $_FILES);
        break;

    case 'update':
        if (empty($data['old_name']) || empty($data['name']) || empty($data['levels'])) {
            echo json_encode(['status' => 'error', 'message' => 'Old name, new name, and levels are required']);
            exit;
        }
        $data['family_id'] = $family_id;
        $data['is_global'] = 0;
        echo ThemeReward::edit($data, $_FILES);
        break;

    case 'delete':
        $theme_name = $data['name'] ?? $_GET['name'] ?? null;
        if (empty($theme_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Theme name is required']);
            exit;
        }
        echo ThemeReward::delete($theme_name, $family_id, false);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
