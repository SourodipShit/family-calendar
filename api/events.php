<?php
session_start();
require_once __DIR__ . '/../classes/Event.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
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
        $familyId = $_SESSION['user']['active_family_id'];
        $userId = $_SESSION['user']['id'];
        $events = Event::getEventsByDateRange($_GET['start'], $_GET['end'], $familyId, $userId);
        
        $processedEvents = [];
        foreach ($events as $event) {
            $duration = calculateDuration($event['start_time'], $event['end_time']);
            
            // Fetch members for this specific event
            $members = Event::getEventMembers($event['id']);
            
            $processedEvents[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'startHour' => $event['start_time'],
                'endHour' => $event['end_time'],
                'duration' => $duration,
                'colorCode' => $event['color'],
                'categoryColor' => $event['color'],
                'categoryName' => $event['type'],
                'created_by' => $event['created_by'],
                'location' => $event['location'] ?? '',
                'is_all_day' => (isset($event['is_all_day']) && $event['is_all_day']) ? true : false,
                'countdown' => $event['countdown'] ?? 0,
                'tracking_status' => $event['tracking_status'] ?? null,
                'tracking_feedback' => $event['tracking_feedback'] ?? null,
                'members' => $members
            ];
        }
        
        echo json_encode($processedEvents);
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

