<?php
session_start();
require_once __DIR__ . '/../classes/Coach.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'coach') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$coachId = $_SESSION['user']['id'];
$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $result = Coach::getCoachFamilies($coachId);
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
