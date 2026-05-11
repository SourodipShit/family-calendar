<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'approve') {
    $family_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$family_id) {
        echo json_encode(["status" => "error", "message" => "Family ID is required."]);
        exit;
    }

    try {
        // Ensure the approved column exists in the families table
        Database::runPrepared("UPDATE families SET approved = 1 WHERE id = ?", [$family_id]);
        echo json_encode(["status" => "success", "message" => "Family approved successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to approve family: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
