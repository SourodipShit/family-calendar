<?php
header('Content-Type: application/json');
require_once '../classes/Family.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $family_id = $_POST['id'] ?? null;

    if (!$family_id) {
        echo json_encode(['status' => 'error', 'message' => 'Family ID is required']);
        exit;
    }

    $result = Family::delete($family_id);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
