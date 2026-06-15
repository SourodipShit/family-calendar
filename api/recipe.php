<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Recipe.php";

$action = $_GET['action'] ?? '';
switch ($action) {
    case "getRecipies":
        $count = isset($_GET['count']) ? (int) $_GET['count'] : 0;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
        $filter = isset($_GET['filter']) ? $_GET['filter'] : [];
        $userFamilyId = $_SESSION['user']['active_family_id'] ?? 0;
        $result = Recipe::getRecipies($count, $offset, $filter, $userFamilyId);
        echo json_encode($result);
        exit;

    case "requestAccess":
        $recipeId = $_POST['recipeId'];
        $userFamilyId = $_POST['userFamilyId'];
        $result = Recipe::requestAccess($recipeId, $userFamilyId);
        echo json_encode($result);
        exit;

    case "approveAccess":
        $requestId = $_POST['requestId'];
        $result = Recipe::approveAccess($requestId);
        echo json_encode($result);
        exit;

    case "rejectAccess":
        $requestId = $_POST['requestId'];
        $result = Recipe::rejectAccess($requestId);
        echo json_encode($result);
        exit;

    case "getRequestsForFamily":
        $userId = $_GET['userId'];
        $result = Recipe::getRequestsForFamily($userId);
        echo json_encode($result);
        exit;

    case "deleteRecipe":
        $recipeId = $_POST['recipeId'];
        $result = Recipe::deleteRecipe($recipeId);
        echo json_encode($result);
        exit;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        exit;
}

