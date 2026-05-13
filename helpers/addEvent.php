<?php
session_start();
require_once __DIR__ . '/../classes/Event.php';

if (isset($_POST['save'])) {
    // Basic validation
    if (!isset($_POST['title']) || !isset($_POST['date'])) {
        header("Location: ../users/index.php?status=error&msg=Missing required fields");
        exit;
    }

    $family_id = $_SESSION['user']['families'][0]['family_id'] ?? null;
    $user_id = $_SESSION['user']['id'] ?? null;

    if (!$family_id || !$user_id) {
        header("Location: ../users/index.php?status=error&msg=Unauthorized");
        exit;
    }

    $date = $_POST['date'];
    $is_all_day = (isset($_POST['is_all_day']) && $_POST['is_all_day'] == '1') ? 1 : 0;

    // Construct datetime strings for SQL
    if ($is_all_day) {
        $start_time = $date . " 00:00:00";
        $end_time = $date . " 23:59:59";
    } else {
        $st = !empty($_POST['start_time']) ? $_POST['start_time'] : "00:00";
        $et = !empty($_POST['end_time']) ? $_POST['end_time'] : "23:59";
        $start_time = $date . " " . $st . ":00";
        $end_time = $date . " " . $et . ":00";
    }

    // Map form fields to Event class expected array
    $data = [
        'family_id' => $family_id,
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? '',
        'type_id' => $_POST['type_id'],
        'start_time' => $start_time,
        'end_time' => $end_time,
        'location' => $_POST['location'] ?? '',
        'is_all_day' => $is_all_day,
        'event_repeat' => !empty($_POST['event_repeat']) ? $_POST['event_repeat'] : null,
        'remainder' => !empty($_POST['remainder']) ? $_POST['remainder'] : null,
        'created_by' => $user_id,
        'members' => !empty($_POST['member_id']) ? [$_POST['member_id']] : []
    ];

    // Call the updated Event::add method which now handles members too
    $result = Event::add($data);

    // Redirect back to index with status and message
    $status = $result['status']; // success or error
    $msg = urlencode($result['msg']);

    header("Location: ../users/index.php?status=$status&msg=$msg");
    exit;
} else {
    // If accessed directly, redirect back
    header("Location: ../users/index.php");
    exit;
}
