<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Meals.php";
require_once __DIR__ . "/../classes/File.php";
require_once __DIR__ . "/../config/Database.php";

$action = $_GET['action'] ?? '';

if ($action == 'add') {
    $imagePath = $_POST['image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = File::upload($_FILES['image'], 'meals');
        if ($uploadResult['status'] === 'success') {
            $imagePath = $uploadResult['filePath'];
        }
    }
    $_POST['image'] = $imagePath;
    
    // Inject user info for approval flow
    $_POST['user_id'] = $_SESSION['user']['id'] ?? null;
    $role = $_SESSION['user']['role'] ?? '';
    $_POST['status'] = ($role === 'family-head') ? 'approved' : 'pending';
    
    $result = Meals::addMeal($_POST);
    echo json_encode($result);
    exit;
}

if ($action == 'update') {
    $imagePath = $_POST['image'] ?? ''; // Keep existing if no new upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Fetch old image to delete it
        $oldMeal = Database::runPrepared("SELECT image FROM meals WHERE id = ?", [$_POST['id']])->fetch(PDO::FETCH_ASSOC);
        if ($oldMeal && !empty($oldMeal['image'])) {
            File::deleteFile($oldMeal['image']);
        }

        $uploadResult = File::upload($_FILES['image'], 'meals');
        if ($uploadResult['status'] === 'success') {
            $imagePath = $uploadResult['filePath'];
        }
    }
    $_POST['image'] = $imagePath;
    $result = Meals::updateMeal($_POST);
    echo json_encode($result);
    exit;
}

if ($action == 'delete') {
    // Fetch image to delete it
    $oldMeal = Database::runPrepared("SELECT image FROM meals WHERE id = ?", [$_POST['id']])->fetch(PDO::FETCH_ASSOC);
    if ($oldMeal && !empty($oldMeal['image'])) {
        File::deleteFile($oldMeal['image']);
    }
    
    $result = Meals::deleteMeal($_POST['id']);
    echo json_encode($result);
    exit;
}

if ($action == 'getByDateRange') {
    $family_id = $_POST['family_id'] ?? $_SESSION['user']['active_family_id'] ?? 1;
    $user_id = $_SESSION['user']['id'] ?? null;
    $result = Meals::getByDateRange($_POST['startDate'], $_POST['endDate'], $family_id, $user_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getPendingUser') {
    $family_id = $_SESSION['user']['active_family_id'] ?? 1;
    $user_id = $_SESSION['user']['id'] ?? null;
    $result = Meals::getPendingMealsUser($user_id, $family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getPendingFamily') {
    if (($_SESSION['user']['role'] ?? '') !== 'family-head') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }
    $family_id = $_SESSION['user']['active_family_id'] ?? 1;
    $result = Meals::getPendingMealsFamily($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'approve') {
    if (($_SESSION['user']['role'] ?? '') !== 'family-head') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }
    $family_id = $_SESSION['user']['active_family_id'] ?? 1;
    $result = Meals::approveMeal($_POST['id'], $family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'reject') {
    if (($_SESSION['user']['role'] ?? '') !== 'family-head') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }
    $family_id = $_SESSION['user']['active_family_id'] ?? 1;
    $result = Meals::rejectMeal($_POST['id'], $family_id);
    echo json_encode($result);
    exit;
}
