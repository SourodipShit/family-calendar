<?php
require_once __DIR__ . '/../classes/FamilyRequest.php';
require_once __DIR__ . '/../config/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;

if (!$id || !$token) {
    die("Invalid request link.");
}

$req = Database::runPrepared("SELECT * FROM family_requests WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    die("Request not found or has been removed.");
}

$expected_token = md5($req['id'] . $req['email'] . 'FC_SECURE_SALT_2026');

if ($token !== $expected_token) {
    die("Invalid or expired token.");
}

if ($req['status'] === 'approved') {
    $msg = urlencode("This request has already been approved.");
    header("Location: ../login.php?msg=" . $msg);
    exit;
}

$result = FamilyRequest::updateStatus($id, 'approved');

if ($result['status'] === 'success') {
    // Also make sure receiver_id is set if it was somehow missing, though it shouldn't be since we verified email
    if (empty($req['receiver_id'])) {
        $receiver = Database::runPrepared("SELECT id FROM users WHERE email = ?", [$req['email']])->fetch(PDO::FETCH_ASSOC);
        if ($receiver) {
            Database::runPrepared("UPDATE family_requests SET receiver_id = ? WHERE id = ?", [$receiver['id'], $id]);
        }
    }
    
    $msg = urlencode("Request approved successfully! You can now log in to view your family calendar.");
    header("Location: ../login.php?success_msg=" . $msg);
    exit;
} else {
    die("Failed to approve request: " . $result['message']);
}
