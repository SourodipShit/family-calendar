<?php
session_start();
ob_start();
require_once __DIR__ . '/../classes/Coach.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../services/mail/Mailer.php';
require_once __DIR__ . '/../classes/File.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['user']['role'];

switch ($action) {
    case 'list_families':
        if ($userRole !== 'coach') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        $result = Coach::getCoachFamilies($userId);
        echo json_encode($result);
        break;

    case 'upload_csv':
        if ($userRole !== 'coach') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        $familyCoachId = $_POST['family_coach_id'] ?? null;
        if (!$familyCoachId || !isset($_FILES['csv_file'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
            exit;
        }

        $upload = File::upload($_FILES['csv_file'], 'plans');
        
        if ($upload['status'] === 'success') {
            $csvLink = $upload['filePath'];
            $update = Coach::updateFamilyCoachCsvLink($familyCoachId, $csvLink);
            
            if ($update['status'] !== 'success') {
                ob_clean();
                echo json_encode(['status' => 'error', 'message' => 'Failed to save CSV link: ' . $update['message']]);
                exit;
            }

            // Fetch family info to send email
            $details = Coach::getFamilyCoachDetails($familyCoachId);
            if ($details['status'] === 'success') {
                $familyId = $details['data']['family_id'];
                try {
                    $headsQuery = Database::runPrepared("
                        SELECT users.email, users.name 
                        FROM users 
                        INNER JOIN user_family ON users.id = user_family.user_id 
                        WHERE user_family.family_id = ? AND users.role = 'family-head'
                    ", [$familyId]);
                    $familyHeads = $headsQuery->fetchAll(PDO::FETCH_ASSOC);

                    require_once __DIR__ . '/../services/mail/Mail.php';
                    foreach ($familyHeads as $head) {
                        Mail::coachPlanUploaded($head['email'], $head['name']);
                    }
                } catch (Throwable $e) {
                    error_log("Failed to send coach plan email: " . $e->getMessage());
                }
            }
            
            ob_clean();
            echo json_encode(['status' => 'success', 'message' => 'Plan uploaded successfully.']);
        } else {
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => $upload['message']]);
        }
        break;

    case 'import_csv':
        if ($userRole !== 'family-head') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        $familyCoachId = $_POST['family_coach_id'] ?? null;
        if (!$familyCoachId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
            exit;
        }

        $details = Coach::getFamilyCoachDetails($familyCoachId);
        if ($details['status'] !== 'success' || empty($details['data']['csv_link'])) {
            echo json_encode(['status' => 'error', 'message' => 'No CSV plan found']);
            exit;
        }

        $csvLink = $details['data']['csv_link'];
        $coachId = $details['data']['coach_id'];
        $familyId = $details['data']['family_id'];
        
        $realPath = __DIR__ . '/../' . ltrim($csvLink, './');
        if (!file_exists($realPath)) {
            echo json_encode(['status' => 'error', 'message' => 'CSV file not found on server']);
            exit;
        }

        try {
            Database::getInstance()->beginTransaction();

            if (($handle = fopen($realPath, "r")) !== FALSE) {
                $header = fgetcsv($handle, 1000, ","); // Skip header
                // Expected format: Date, Title, Description, StartTime, EndTime
                
                // Get a default type_id
                $typeStmt = Database::runPrepared("SELECT id FROM event_types WHERE LOWER(name) = 'coaching' AND (family_id IS NULL OR family_id = ?) LIMIT 1", [$familyId]);
                $defaultType = $typeStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$defaultType) {
                    $typeStmt = Database::runPrepared("SELECT id FROM event_types WHERE is_default = 1 AND (family_id IS NULL OR family_id = ?) LIMIT 1", [$familyId]);
                    $defaultType = $typeStmt->fetch(PDO::FETCH_ASSOC);
                }
                $typeId = $defaultType ? $defaultType['id'] : 1;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) < 2) continue; // Skip empty rows
                    $rawDate = trim($data[0]);
                    
                    // Attempt to parse multiple common date formats
                    $parsedDateObj = DateTime::createFromFormat('d-m-Y', $rawDate);
                    if (!$parsedDateObj) $parsedDateObj = DateTime::createFromFormat('Y-m-d', $rawDate);
                    if (!$parsedDateObj) $parsedDateObj = DateTime::createFromFormat('d/m/Y', $rawDate);
                    if (!$parsedDateObj) $parsedDateObj = DateTime::createFromFormat('m/d/Y', $rawDate);
                    
                    if ($parsedDateObj) {
                        $date = $parsedDateObj->format('Y-m-d');
                    } else {
                        // Fallback to strtotime if all else fails
                        $time = strtotime($rawDate);
                        $date = $time ? date('Y-m-d', $time) : date('Y-m-d');
                    }
                    
                    $title = trim($data[1]);
                    $desc = isset($data[2]) ? trim($data[2]) : '';
                    $startTime = isset($data[3]) && !empty(trim($data[3])) ? $date . ' ' . trim($data[3]) : $date . ' 09:00:00';
                    $endTime = isset($data[4]) && !empty(trim($data[4])) ? $date . ' ' . trim($data[4]) : $date . ' 10:00:00';
                    $isAllDay = (isset($data[3]) && empty(trim($data[3]))) ? 1 : 0;

                    $sql = "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, event_repeat, remainder, countdown, created_by)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    Database::runPrepared($sql, [
                        $familyId, $title, $desc, $typeId, $startTime, $endTime, null, $isAllDay, null, null, 0, $userId
                    ]);
                    $eventId = Database::getLastInsertId();

                    $trackSql = "INSERT INTO coach_event_tracking (event_id, coach_id, status) VALUES (?, ?, 'pending')";
                    Database::runPrepared($trackSql, [$eventId, $coachId]);
                }
                fclose($handle);
            }
            
            // Mark as imported (or clear link)
            Database::runPrepared("UPDATE family_coaches SET csv_link = NULL WHERE id = ?", [$familyCoachId]);

            Database::getInstance()->commit();
            ob_clean();
            echo json_encode(['status' => 'success', 'message' => 'Plan imported successfully']);
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            ob_clean();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'mark_complete':
        $eventId = $_POST['event_id'] ?? null;
        if (!$eventId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing event ID']);
            exit;
        }
        
        try {
            $sql = "UPDATE coach_event_tracking SET status = 'family_completed', updated_at = NOW() WHERE event_id = ?";
            Database::runPrepared($sql, [$eventId]);
            echo json_encode(['status' => 'success', 'message' => 'Task marked as complete! Pending coach review.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'coach_review':
        if ($userRole !== 'coach') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $eventId = $_POST['event_id'] ?? null;
        $status = $_POST['status'] ?? null; // coach_approved or reopened
        $feedback = $_POST['feedback'] ?? '';
        
        if (!$eventId || !in_array($status, ['coach_approved', 'reopened'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            exit;
        }
        
        try {
            // Verify coach owns this tracking record
            $checkSql = "SELECT * FROM coach_event_tracking WHERE event_id = ? AND coach_id = ?";
            $check = Database::runPrepared($checkSql, [$eventId, $userId])->fetch(PDO::FETCH_ASSOC);
            if (!$check) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized or record not found']);
                exit;
            }

            $sql = "UPDATE coach_event_tracking SET status = ?, feedback = ?, updated_at = NOW() WHERE event_id = ? AND coach_id = ?";
            Database::runPrepared($sql, [$status, $feedback, $eventId, $userId]);
            echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;
        
    case 'get_coach_events':
        if ($userRole !== 'coach') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        
        if (!$start || !$end) {
            echo json_encode(['error' => 'Missing dates']);
            exit;
        }
        
        try {
            $sql = "SELECT e.*, e.created_by, et.name AS type, et.colour AS color, 
                           cet.status as tracking_status, cet.feedback as tracking_feedback,
                           f.name as family_name, f.id as f_id
                    FROM events AS e 
                    INNER JOIN event_types AS et ON e.type_id = et.id 
                    INNER JOIN coach_event_tracking cet ON e.id = cet.event_id
                    INNER JOIN families f ON e.family_id = f.id
                    WHERE cet.coach_id = ? AND e.start_time BETWEEN ? AND ?";
                    
            $events = Database::runPrepared($sql, [$userId, $start, $end])->fetchAll(PDO::FETCH_ASSOC);
            
            $processedEvents = [];
            $familyColors = [];
            
            foreach ($events as $event) {
                $fId = $event['f_id'];
                
                if (!isset($familyColors[$fId])) {
                    // Generate a consistent, vibrant color based on family_id
                    $hash = md5((string)$fId . 'salt123');
                    $r = hexdec(substr($hash, 0, 2)) % 150 + 50; // 50-200 to keep it vibrant
                    $g = hexdec(substr($hash, 2, 2)) % 150 + 50;
                    $b = hexdec(substr($hash, 4, 2)) % 150 + 50;
                    $familyColors[$fId] = sprintf("#%02x%02x%02x", $r, $g, $b);
                }
                
                $color = $familyColors[$fId];
                
                $processedEvents[] = [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'startHour' => $event['start_time'],
                    'endHour' => $event['end_time'],
                    'colorCode' => $color,
                    'categoryColor' => $event['color'],
                    'categoryName' => $event['type'],
                    'is_all_day' => (isset($event['is_all_day']) && $event['is_all_day']) ? true : false,
                    'family_id' => $event['f_id'],
                    'family_name' => $event['family_name'],
                    'tracking_status' => $event['tracking_status'],
                    'tracking_feedback' => $event['tracking_feedback']
                ];
            }
            echo json_encode($processedEvents);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
