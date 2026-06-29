<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';
$password = $input['password'] ?? '';

if (empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password is required']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($password, $hash)) {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password. Action aborted.']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}

if ($action === 'resetAssets') {
    resetAssets();
} elseif ($action === 'factoryReset') {
    factoryReset();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function resetAssets() {
    try {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        // Wipe data
        $tablesToEmpty = [
            'event_reminders', 'event_members', 'events',
            'meal_favorites', 'meal_ratings', 'meals',
            'recipe_images', 'recipe_ingredients', 'recipe_nutrition', 'recipe_steps', 'recipe_access_requests', 'recipes',
            'grocery_items', 'grocery_lists', 'photos',
            'chore_instances', 'chores',
            'point_transactions', 'redeemed_rewards', 'rewards'
        ];
        
        foreach ($tablesToEmpty as $table) {
            $pdo->exec("DELETE FROM `$table`");
        }

        // Reset user point balances instead of deleting rows
        $pdo->exec("UPDATE user_points SET balance = 0");

        // Delete family specific theme rewards
        $pdo->exec("DELETE FROM theme_rewards WHERE family_id IS NOT NULL");
        
        // Wipe physical files
        deleteFilesInDir(__DIR__ . '/../public/uploads/meals');
        deleteFilesInDir(__DIR__ . '/../public/uploads/recipes');
        deleteFilesInDir(__DIR__ . '/../public/uploads/photos');
        deleteFilesInDir(__DIR__ . '/../public/uploads/rewards');

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'All family assets have been successfully deleted.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function factoryReset() {
    try {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        // Find non-admin users to delete their physical profiles
        $stmt = $pdo->prepare("SELECT image FROM users WHERE role != 'siteadmin' AND image IS NOT NULL");
        $stmt->execute();
        $userImages = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Delete the images from disk
        foreach ($userImages as $image) {
            $imagePath = str_replace('../', '', $image);
            $file = __DIR__ . '/../' . $imagePath;
            if (file_exists($file) && is_file($file)) {
                unlink($file);
            }
        }
        
        // Delete assets
        $tablesToEmpty = [
            'event_reminders', 'event_members', 'events',
            'meal_favorites', 'meal_ratings', 'meals',
            'recipe_images', 'recipe_ingredients', 'recipe_nutrition', 'recipe_steps', 'recipe_access_requests', 'recipes',
            'grocery_items', 'grocery_lists', 'photos',
            'chore_instances', 'chores',
            'point_transactions', 'redeemed_rewards', 'rewards', 'user_points',
            'promo_codes',
            'payments', 'accounts'
        ];
        foreach ($tablesToEmpty as $table) {
            $pdo->exec("DELETE FROM `$table`");
        }
        
        // Delete non-default categories/types and family rewards
        $pdo->exec("DELETE FROM event_types WHERE family_id IS NOT NULL AND is_default = 0");
        $pdo->exec("DELETE FROM grocery_categories WHERE family_id IS NOT NULL AND is_default = 0");
        $pdo->exec("DELETE FROM theme_rewards WHERE family_id IS NOT NULL");

        // Delete families and related
        $pdo->exec("DELETE FROM user_family");
        $pdo->exec("DELETE FROM family_requests");
        $pdo->exec("DELETE FROM families");
        
        // Delete users (except siteadmin) and related data
        $pdo->exec("DELETE FROM password_reset_requests");
        $pdo->exec("DELETE FROM login_logs WHERE user_id IN (SELECT id FROM users WHERE role != 'siteadmin')");
        $pdo->exec("DELETE FROM users WHERE role != 'siteadmin'");

        // Reset family shared emails
        $pdo->exec("UPDATE family_shared_emails SET family_id = NULL, allocated_at = NULL");

        // Wipe physical files
        deleteFilesInDir(__DIR__ . '/../public/uploads/meals');
        deleteFilesInDir(__DIR__ . '/../public/uploads/recipes');
        deleteFilesInDir(__DIR__ . '/../public/uploads/photos');
        deleteFilesInDir(__DIR__ . '/../public/uploads/rewards');

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Factory reset completed successfully.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function deleteFilesInDir($dirPath) {
    if (!is_dir($dirPath)) return;
    $files = glob($dirPath . '/*'); 
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file); 
        }
    }
}
