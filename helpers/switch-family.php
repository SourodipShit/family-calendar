<?php
session_start();
require_once __DIR__ . "/../config/Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_family_id = $_POST['target_family_id'] ?? null;
    $user_id = $_SESSION['user']['id'] ?? null;
    
    if ($target_family_id && $user_id) {
        // Verify user belongs to this family
        $check = Database::runPrepared("SELECT 1 FROM user_family WHERE user_id = ? AND family_id = ?", [$user_id, $target_family_id])->fetch();
        if ($check) {
            $_SESSION['user']['active_family_id'] = $target_family_id;
            
            // Update active account data if stored in session accounts array
            if (isset($_SESSION['active_account_id']) && isset($_SESSION['accounts'][$_SESSION['active_account_id']])) {
                $_SESSION['accounts'][$_SESSION['active_account_id']]['active_family_id'] = $target_family_id;
            }
            
            header("Location: ../users/index.php");
            exit;
        }
    }
}

// Fallback redirect
header("Location: ../users/index.php");
exit;
