<?php
session_start();
require_once __DIR__ . '/../classes/AppleCalendarService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';
$userId = $_SESSION['user']['id'];

if ($action === 'status') {
    $isConnected = AppleCalendarService::isConnected($userId);
    echo json_encode(["status" => "success", "connected" => $isConnected]);
    exit;
} elseif ($action === 'save_link') {
    $link = $_POST['webcal_link'] ?? '';
    if (empty($link)) {
        echo json_encode(["status" => "error", "message" => "WebCal link is required"]);
        exit;
    }
    
    $result = AppleCalendarService::saveWebCalLink($userId, $link);
    echo json_encode($result);
    exit;
} elseif ($action === 'disconnect') {
    $result = AppleCalendarService::disconnect($userId);
    echo json_encode($result);
    exit;
} elseif ($action === 'sync') {
    $result = AppleCalendarService::pullEvents($userId);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
