<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Rating.php";

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;

if (!$userId && !isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated."]);
    exit;
}

$userId = $userId ?? $_POST['user_id'];

if ($action == 'save') {
    $mealId = $_POST['meal_id'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if (!$mealId || !$rating) {
        echo json_encode(["status" => "error", "message" => "Missing parameters."]);
        exit;
    }

    $result = Rating::save($mealId, $userId, $rating);
    echo json_encode($result);
    exit;
}

if ($action == 'get') {
    $mealId = $_POST['meal_id'] ?? $_GET['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Rating::getRating($mealId, $userId);
    echo json_encode($result);
    exit;
}

if ($action == 'getAvg') {
    $mealId = $_POST['meal_id'] ?? $_GET['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Rating::getAverageRating($mealId);
    echo json_encode($result);
    exit;
}

if ($action == 'delete') {
    $mealId = $_POST['meal_id'] ?? null;
    if (!$mealId) {
        echo json_encode(["status" => "error", "message" => "Missing meal ID."]);
        exit;
    }

    $result = Rating::delete($mealId, $userId);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action."]);
