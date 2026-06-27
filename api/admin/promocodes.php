<?php
require_once __DIR__ . '/../../classes/PromoCode.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic auth check - you may want to enhance this to ensure the user is specifically a SITE ADMIN
// For example: if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'site_admin') { ... }
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'list':
        // Site Admin Action: Get all promo codes
        echo json_encode(PromoCode::fetchAll());
        break;

    case 'create':
        // Site Admin Action: Create a new promo code
        if (empty($data['code'])) {
            echo json_encode(['status' => 'error', 'message' => 'Promo code is required']);
            exit;
        }
        // Force max codes rule
        echo json_encode(PromoCode::add($data));
        break;

    case 'delete':
        // Site Admin Action: Delete a promo code
        if (empty($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID is required for deleting']);
            exit;
        }
        echo json_encode(PromoCode::delete($data['id']));
        break;

    case 'update':
        // Site Admin Action: Update an existing promo code (e.g., deactivate it)
        if (empty($data['id']) || empty($data['code'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID and Code are required for updating']);
            exit;
        }
        echo json_encode(PromoCode::edit($data));
        break;

    case 'verify':
        // Public/Signup Action: Verify if a code is valid
        // (Often this might live in a public API, but placing it here works too)
        $code = $data['code'] ?? $_GET['code'] ?? null;
        if (empty($code)) {
            echo json_encode(['status' => 'error', 'message' => 'Promo code parameter is required']);
            exit;
        }
        echo json_encode(PromoCode::verify($code));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid API action']);
        break;
}
