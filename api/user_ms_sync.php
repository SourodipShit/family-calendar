<?php
session_start();
require_once __DIR__ . '/../classes/MicrosoftCalendarService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'];

if ($action === 'status') {
    $isConnected = MicrosoftCalendarService::isConnected($userId);
    echo json_encode(["status" => "success", "connected" => $isConnected]);
    exit;
} elseif ($action === 'connect') {
    $url = MicrosoftCalendarService::getAuthUrl();
    if ($url) {
        echo json_encode(["status" => "success", "url" => $url]);
    } else {
        echo json_encode(["status" => "error", "message" => "Microsoft Calendar integration is not configured by the site administrator."]);
    }
    exit;
} elseif ($action === 'disconnect') {
    $result = MicrosoftCalendarService::disconnect($userId);
    echo json_encode($result);
    exit;
} elseif ($action === 'sync') {
    $result = MicrosoftCalendarService::pullEvents($userId);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
