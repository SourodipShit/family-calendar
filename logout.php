<?php
session_start();
require_once __DIR__ . '/classes/Auth.php';
$logout_all = isset($_GET['all']) && $_GET['all'] == '1';
$res = Auth::logout($logout_all);

if (isset($res['status']) && $res['status'] === 'switched') {
    // If user is a siteadmin, they'd go to siteadmin/index.php, else users/index.php
    $redirect = (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'siteadmin') ? 'siteadmin/index.php' : 'users/index.php';
    echo "<script>window.location.href='{$redirect}';</script>";
} else {
    echo "<script>window.location.href='login.php?logout=success';</script>";
}
