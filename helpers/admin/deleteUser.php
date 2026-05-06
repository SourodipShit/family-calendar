<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo '<script>window.location.href = "../../login.php";</script>';
    exit;
}

require_once __DIR__ . '/../../classes/User.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Get user to check for image
    $user = User::getUserById($userId);
    if ($user) {
        $result = User::deleteUser($userId, $user['image']);
        $status = $result['status'];
        $message = urlencode($result['message']);
        echo "<script>window.location.href = '../../siteadmin/users.php?status=$status&msg=$message';</script>";
    } else {
        echo '<script>window.location.href = "../../siteadmin/users.php?status=error&msg=User+not+found";</script>';
    }
} else {
    echo '<script>window.location.href = "../../siteadmin/users.php?status=error&msg=Invalid+request";</script>';
}
