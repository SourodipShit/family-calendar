<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_prefix = isset($path_prefix) ? $path_prefix : "../";
require_once __DIR__ . '/../classes/FamilyViewDevice.php';

$token = $_COOKIE['family_view_token'] ?? null;
$family_id = false;

if ($token) {
    $family_id = FamilyViewDevice::verifyTokenAndGetFamilyId($token);
}

if (!$family_id) {
    // Invalid or missing token
    setcookie('family_view_token', '', time() - 3600, "/");
    header("Location: " . $path_prefix . "users/index.php");
    exit;
}
