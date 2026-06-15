<?php
session_start();
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Family.php';
require_once __DIR__ . '/../services/mail/ICS.php';
require_once __DIR__ . '/../services/mail/Mailer.php';
require_once __DIR__ . '/../services/mail/Mail.php';
require_once __DIR__ . '/../services/sms/SmsService.php';
require_once __DIR__ . '/../services/sms/SmsTemplates.php';

if (isset($_POST['save']) && isset($_POST['event_id'])) {
    if (!isset($_POST['title']) || !isset($_POST['date'])) {
        header("Location: ../users/index.php?status=error&msg=Missing required fields");
        exit;
    }

    $family_id = $_SESSION['user']['active_family_id'] ?? null;
    $user_id = $_SESSION['user']['id'] ?? null;
    $event_id = $_POST['event_id'];

    if (!$family_id || !$user_id) {
        header("Location: ../users/index.php?status=error&msg=Unauthorized");
        exit;
    }

    $date = $_POST['date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $date;
    $is_all_day = (isset($_POST['is_all_day']) && $_POST['is_all_day'] == '1') ? 1 : 0;

    if ($is_all_day) {
        $start_time = $date . " 00:00:00";
        $end_time = $end_date . " 23:59:59";
    } else {
        $st = !empty($_POST['start_time']) ? $_POST['start_time'] : "00:00";
        $et = !empty($_POST['end_time']) ? $_POST['end_time'] : "23:59";
        $start_time = $date . " " . $st . ":00";
        $end_time = $end_date . " " . $et . ":00";
    }

    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? '',
        'type_id' => $_POST['type_id'],
        'start_time' => $start_time,
        'end_time' => $end_time,
        'location' => $_POST['location'] ?? '',
        'is_all_day' => $is_all_day,
        'event_repeat' => !empty($_POST['event_repeat']) ? $_POST['event_repeat'] : null,
        'remainder' => !empty($_POST['remainder']) ? $_POST['remainder'] : null,
        'members' => !empty($_POST['member_id']) ? [$_POST['member_id']] : []
    ];

    $result = Event::update($event_id, $data, $user_id);

    $status = $result['status'];
    $msg = urlencode($result['msg']);

    header("Location: ../users/index.php?status=$status&msg=$msg");
    exit;
} else {
    header("Location: ../users/index.php");
    exit;
}

