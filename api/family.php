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
$family_id = $_SESSION['user']['families'][0]['family_id'] ?? null;

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
        echo json_encode($result);
        exit;
    
    case 'updateSettings':
        $result = Family::updateFamilySettings($data['settings'], $family_id);
        if ($result['status'] === 'success') {
            $_SESSION['user']['families'][0]['settings'] = json_encode($data['settings']);
        }
        echo json_encode($result);
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}
