<?php
session_start();
require_once __DIR__ . '/../classes/Event.php';

header('Content-Type: application/json');
$action = $_GET['action'];

switch ($action) {
    case 'getEvents':
        getEventsByDateRange();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getEventsByDateRange()
{
    try {
        $familyId = $_SESSION['user']['families'][0]['id'];
        $events = Event::getEventsByDateRange($_GET['start'], $_GET['end'], $familyId);
        $filteredEvents = [];
        foreach ($events as $key => $event) {
            $duration = calculateDuration($event['start_time'], $event['end_time']);
            $filteredEvents[$key] = [
                'title' => $event['title'],
                'startHour' => $event['start_time'],
                'endHour' => $event['end_time'],
                'duration' => $duration,
                'colorCode' => $event['color'],
                'id' => $event['id'],
                'user_id' => $event['user_id'],
                'member' => $event['member'],
                'location' => $event['location'] ?? '',
                'is_all_day' => (isset($event['is_all_day']) && $event['is_all_day']) ? true : false
            ];
        }
        echo json_encode($filteredEvents);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}


function calculateDuration($startTime, $endTime)
{
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $interval = $start->diff($end);
    $hours = $interval->h;
    $minutes = $interval->i;
    // Convert to decimal
    $decimal = $hours + ($minutes / 60);
    return round($decimal, 2); // optional rounding
}
