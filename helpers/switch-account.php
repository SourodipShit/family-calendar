<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_id = $_POST['target_user_id'] ?? null;
    
    if ($target_id && isset($_SESSION['accounts'][$target_id])) {
        $_SESSION['active_account_id'] = $target_id;
        $_SESSION['user'] = $_SESSION['accounts'][$target_id];
        
        $redirect = ($_SESSION['user']['role'] === 'siteadmin') ? '../siteadmin/index.php' : '../users/index.php';
        header("Location: " . $redirect);
        exit;
    }
}

// Fallback redirect
header("Location: ../signup.php");
exit;
