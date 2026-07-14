<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Coach.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
        echo "Unauthorized.";
        exit;
    }

    $familyCoachId = $_POST['family_coach_id'] ?? null;
    $action = $_POST['action'] ?? null;
    
    if (!$familyCoachId || !$action) {
        $msg = "Missing fields.";
    } else {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $res = Coach::updateFamilyCoachStatus($familyCoachId, $status);
        $msg = $res['message'];
    }
    
    // Redirect back
    if (isset($_SERVER['HTTP_REFERER'])) {
        $url = parse_url($_SERVER['HTTP_REFERER']);
        $path = $url['path'] ?? '';
        $query = [];
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
        }
        $query['msg'] = $msg;
        header("Location: " . $path . "?" . http_build_query($query));
        exit;
    }
    
    echo $msg;
    exit;
}
