<?php
session_start();
require_once __DIR__ . '/../classes/User.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user to get image path before deletion
    $user = User::getUserById($user_id);

    if ($user) {
        $image_path = $user['image'];

        // Call the delete method
        $result = User::deleteUser($user_id, $image_path);

        if ($result['status'] === 'success') {
            $_SESSION['success_msg'] = $result['message'];
        } else {
            $_SESSION['error_msg'] = $result['message'];
        }
    } else {
        $_SESSION['error_msg'] = "User not found";
    }
} else {
    $_SESSION['error_msg'] = "Invalid request";
}

echo '<script>window.location.href = "../users/index.php";</script>';
