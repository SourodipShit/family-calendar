<?php
session_start();
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/FamilyRequest.php';
require_once __DIR__ . '/../config/Database.php';

if (isset($_GET['id']) && isset($_SESSION['user']['families'])) {
    $user_id = $_GET['id'];
    $active_family_id = null;
    
    // Assume the first family in session is the primary active family context
    if (!empty($_SESSION['user']['families'])) {
        $active_family_id = $_SESSION['user']['families'][0]['id'];
    }

    if ($active_family_id) {
        $pdo = Database::getInstance();
        try {
            $pdo->beginTransaction();

            // 1. Remove native connection
            Database::runPrepared("DELETE FROM user_family WHERE user_id = ? AND family_id = ?", [$user_id, $active_family_id]);
            
            // 2. Remove external connection (linked requests)
            Database::runPrepared("DELETE FROM family_requests WHERE (requester_id = ? OR receiver_id = ?) AND family_id = ?", [$user_id, $user_id, $active_family_id]);

            $pdo->commit();
            $_SESSION['success_msg'] = "Member successfully removed from this family.";

        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_msg'] = "Failed to remove member from family: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Active family not found.";
    }
} else {
    $_SESSION['error_msg'] = "Invalid request or session expired.";
}

echo '<script>window.location.href = "../users/index.php";</script>';
