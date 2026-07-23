<?php
session_start();
require_once __DIR__ . '/../classes/MicrosoftCalendarService.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $userId = $_SESSION['user']['id'];

    $result = MicrosoftCalendarService::authenticate($code, $userId);

    if ($result['status'] === 'success') {
        header('Location: ../users/settings.php?ms_sync=success');
    } else {
        header('Location: ../users/settings.php?ms_sync=error&message=' . urlencode($result['message']));
    }
} else {
    header('Location: ../users/settings.php?ms_sync=error&message=No+code+provided');
}
exit;
