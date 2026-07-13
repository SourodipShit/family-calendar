<?php
session_start();
require_once __DIR__ . '/../classes/GoogleCalendarService.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $userId = $_SESSION['user']['id'];

    $result = GoogleCalendarService::authenticate($code, $userId);

    if ($result['status'] === 'success') {
        header('Location: ../users/settings.php?google_sync=success');
    } else {
        header('Location: ../users/settings.php?google_sync=error&message=' . urlencode($result['message']));
    }
} else {
    header('Location: ../users/settings.php?google_sync=error&message=No+code+provided');
}
exit;
