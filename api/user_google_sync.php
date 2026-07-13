<?php
session_start();
require_once __DIR__ . '/../classes/GoogleCalendarService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'];

if ($action === 'status') {
    $isConnected = GoogleCalendarService::isConnected($userId);
    echo json_encode(["status" => "success", "connected" => $isConnected]);
    exit;
} elseif ($action === 'connect') {
    $url = GoogleCalendarService::getAuthUrl();
    if ($url) {
        echo json_encode(["status" => "success", "url" => $url]);
    } else {
        echo json_encode(["status" => "error", "message" => "Google Calendar integration is not configured by the site administrator."]);
    }
    exit;
} elseif ($action === 'disconnect') {
    $result = GoogleCalendarService::disconnect($userId);
    echo json_encode($result);
    exit;
} elseif ($action === 'sync') {
    $result = GoogleCalendarService::pullEvents($userId);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
