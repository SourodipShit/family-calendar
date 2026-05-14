<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Favorite.php";

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;

if (!$userId && !isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit;
}

$userId = $userId ?? $_POST['user_id'];

if ($action == 'add') {
    $mealId = $_POST['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Favorite::add($mealId, $userId);
    echo json_encode($result);
    exit;
}

if ($action == 'remove') {
    $mealId = $_POST['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Favorite::remove($mealId, $userId);
    echo json_encode($result);
    exit;
}

if ($action == 'toggle') {
    $mealId = $_POST['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Favorite::toggle($mealId, $userId);
    echo json_encode($result);
    exit;
}

if ($action == 'check') {
    $mealId = $_POST['meal_id'] ?? $_GET['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Favorite::isFavorite($mealId, $userId);
    echo json_encode($result);
    exit;
}

if ($action == 'list') {
    $result = Favorite::getUserFavorites($userId);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action."]);
