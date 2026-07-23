<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Coach.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'family-head') {
        echo json_encode(["status" => "error", "message" => "Unauthorized. Only family heads can hire coaches."]);
        exit;
    }

    $familyId = $_SESSION['user']['family_id'] ?? null; 
    if (!$familyId) {
        // Fallback to query user_family
        $stmt = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ?", [$_SESSION['user']['id']]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $familyId = $res ? $res['family_id'] : null;
    }

    $coachId = $_POST['coach_id'] ?? null;
    $planId = $_POST['plan_id'] ?? null;

    if (!$familyId || !$coachId || !$planId) {
        $msg = "Missing required fields.";
    } else {
        // Fetch actual price from database to prevent tampering and fix the $25 bug
        $stmt = Database::runPrepared("SELECT price FROM coach_plans WHERE id = ? AND coach_id = ?", [$planId, $coachId]);
        $planData = $stmt->fetch(PDO::FETCH_ASSOC);
        $priceAtHire = $planData ? $planData['price'] : 0;

        $result = Coach::hireCoach($familyId, $coachId, $planId, $priceAtHire);
        $msg = $result['message'];
    }
    
    // Redirect back
    $_SESSION['flash_msg'] = $msg;
    $_SESSION['flash_type'] = (isset($result['status']) && $result['status'] === 'success') ? 'success' : 'error';

    if (isset($_SERVER['HTTP_REFERER'])) {
        $url = parse_url($_SERVER['HTTP_REFERER']);
        $path = $url['path'] ?? '';
        $query = [];
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            unset($query['msg']);
            unset($query['type']);
        }
        $newQuery = !empty($query) ? '?' . http_build_query($query) : '';
        header("Location: " . $path . $newQuery);
        exit;
    }
    
    echo $msg;
    exit;
}
