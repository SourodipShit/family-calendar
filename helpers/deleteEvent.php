<?php
session_start();
require_once __DIR__ . '/../classes/Event.php';

if (isset($_GET['id'])) {
    $user_id = $_SESSION['user']['id'] ?? null;
    $event_id = $_GET['id'];

    if (!$user_id) {
        header("Location: ../users/index.php?status=error&msg=Unauthorized");
        exit;
    }

    $result = Event::delete($event_id, $user_id);
    $status = $result['status'];
    $msg = urlencode($result['msg']);

    header("Location: ../users/index.php?status=$status&msg=$msg");
    exit;
} else {
    header("Location: ../users/index.php");
    exit;
}
