<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo '<script>window.location.href = "../../login.php";</script>';
    exit;
}

require_once __DIR__ . '/../../classes/Family.php';

if (isset($_GET['id'])) {
    $familyId = $_GET['id'];

    $result = Family::delete($familyId);
    $status = $result['status'];
    $message = urlencode($result['message']);
    echo "<script>window.location.href = '../../siteadmin/families.php?status=$status&msg=$message';</script>";
} else {
    echo '<script>window.location.href = "../../siteadmin/families.php?status=error&msg=Invalid+request";</script>';
}
