<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Coach.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'coach') {
        echo "Unauthorized.";
        exit;
    }

    $coachId = $_SESSION['user']['id'];
    $familyCoachId = $_POST['family_coach_id'] ?? null;
    
    if (!$familyCoachId || !isset($_FILES['calendar_csv']) || $_FILES['calendar_csv']['error'] !== UPLOAD_ERR_OK) {
        $msg = "Upload failed or missing fields.";
    } else {
        // Basic check if the family_coach_id belongs to the logged in coach
        $res = Coach::getFamilyCoachDetails($familyCoachId);
        if ($res['status'] !== 'success' || $res['data']['coach_id'] != $coachId) {
            $msg = "Invalid record.";
        } else {
            $tmpName = $_FILES['calendar_csv']['tmp_name'];
            $name = basename($_FILES['calendar_csv']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            
            if ($ext !== 'csv') {
                $msg = "Please upload a valid CSV file.";
            } else {
                $uploadDir = __DIR__ . '/../assets/uploads/coach_csv/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = 'calendar_' . $familyCoachId . '_' . time() . '.csv';
                $dest = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $dest)) {
                    $csvLink = 'assets/uploads/coach_csv/' . $fileName;
                    $updateRes = Coach::updateFamilyCoachCsvLink($familyCoachId, $csvLink);
                    $msg = $updateRes['message'];
                } else {
                    $msg = "Error moving uploaded file.";
                }
            }
        }
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
