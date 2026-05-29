<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Recipe.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'approve' || $action === 'reject') {
    $recipe_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$recipe_id) {
        echo json_encode(["status" => "error", "message" => "Recipe ID is required."]);
        exit;
    }

    $status = ($action === 'approve') ? 'approved' : 'rejected';
    $result = Recipe::updateStatus($recipe_id, $status);
    
    echo json_encode($result);
} elseif ($action === 'get') {
    $recipe_id = $_GET['id'] ?? null;
    if (!$recipe_id) {
        echo json_encode(["status" => "error", "message" => "Recipe ID is required."]);
        exit;
    }
    
    $result = Recipe::getById($recipe_id);
    echo json_encode($result);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
