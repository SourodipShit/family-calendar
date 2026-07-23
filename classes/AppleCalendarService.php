<?php
require_once __DIR__ . "/../config/Database.php";

class AppleCalendarService
{
    public static function saveWebCalLink($userId, $link)
    {
        try {
            $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            $settings = [];
            if ($user && $user['settings']) {
                $settings = json_decode($user['settings'], true);
            }

            $settings['apple_webcal_link'] = $link;
            Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [json_encode($settings), $userId]);
            
            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function disconnect($userId)
    {
        try {
            $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            if ($user && $user['settings']) {
                $settings = json_decode($user['settings'], true);
                if (isset($settings['apple_webcal_link'])) {
                    unset($settings['apple_webcal_link']);
                    Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [json_encode($settings), $userId]);
                }
            }

            Database::runPrepared("DELETE FROM event_sync_mappings WHERE google_event_id LIKE 'apple_%' AND local_event_id IN (SELECT id FROM events WHERE family_id = (SELECT family_id FROM users WHERE id = ?))", [$userId]);
            
            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function isConnected($userId)
    {
        try {
            $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            if ($user && $user['settings']) {
                $settings = json_decode($user['settings'], true);
                return !empty($settings['apple_webcal_link']);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function getWebCalLink($userId)
    {
        $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['settings']) {
            $settings = json_decode($user['settings'], true);
            return $settings['apple_webcal_link'] ?? null;
        }
        return null;
    }

    public static function pullEvents($userId)
    {
        $link = self::getWebCalLink($userId);
        if (!$link) return ["status" => "error", "message" => "No WebCal link configured"];

        // Convert webcal:// to https://
        if (strpos($link, 'webcal://') === 0) {
            $link = 'https://' . substr($link, 9);
        }

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($response)) {
            return ["status" => "error", "message" => "Failed to fetch events from Apple Calendar"];
        }

        try {
            $familyId = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ? LIMIT 1", [$userId])->fetchColumn();
            $defaultTypeId = Database::runPrepared("SELECT id FROM event_types WHERE family_id IS NULL OR family_id = ? LIMIT 1", [$familyId])->fetchColumn();

            $events = self::parseICS($response);

            foreach ($events as $item) {
                if (!isset($item['uid'])) continue;
                
                $appleId = 'apple_' . md5($item['uid']);
                $title = $item['summary'] ?? 'Busy';
                $description = $item['description'] ?? '';
                $location = $item['location'] ?? '';
                
                $isAllDay = $item['is_all_day'] ? 1 : 0;
                
                $startTime = $item['start_time'];
                $endTime = $item['end_time'];

                $mapping = Database::runPrepared("SELECT local_event_id FROM event_sync_mappings WHERE google_event_id = ?", [$appleId])->fetch(PDO::FETCH_ASSOC);

                if ($mapping) {
                    $localId = $mapping['local_event_id'];
                    Database::runPrepared(
                        "UPDATE events SET title=?, description=?, start_time=?, end_time=?, location=?, is_all_day=? WHERE id=?",
                        [$title, $description, $startTime, $endTime, $location, $isAllDay, $localId]
                    );
                } else {
                    Database::runPrepared(
                        "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$familyId, $title, $description, $defaultTypeId, $startTime, $endTime, $location, $isAllDay, $userId]
                    );
                    $localId = Database::getInstance()->lastInsertId();
                    
                    Database::runPrepared(
                        "INSERT INTO event_sync_mappings (local_event_id, google_event_id) VALUES (?, ?)",
                        [$localId, $appleId]
                    );
                    
                    Database::runPrepared("INSERT INTO event_members (event_id, user_id) VALUES (?, ?)", [$localId, $userId]);
                }
            }

            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    private static function parseICS($icsData)
    {
        $events = [];
        $lines = explode("\n", $icsData);
        $event = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === 'BEGIN:VEVENT') {
                $event = [];
            } elseif ($line === 'END:VEVENT') {
                if ($event) {
                    // Fallback to start time if end time is missing
                    if (!isset($event['end_time']) && isset($event['start_time'])) {
                        $event['end_time'] = date('Y-m-d H:i:s', strtotime($event['start_time'] . ' +1 hour'));
                    }
                    $events[] = $event;
                }
                $event = null;
            } elseif ($event !== null) {
                if (strpos($line, 'UID:') === 0) {
                    $event['uid'] = substr($line, 4);
                } elseif (strpos($line, 'SUMMARY:') === 0) {
                    $event['summary'] = substr($line, 8);
                } elseif (strpos($line, 'DESCRIPTION:') === 0) {
                    $event['description'] = substr($line, 12);
                } elseif (strpos($line, 'LOCATION:') === 0) {
                    $event['location'] = substr($line, 9);
                } elseif (strpos($line, 'DTSTART') === 0) {
                    $event['start_time'] = self::parseICSDate($line);
                    $event['is_all_day'] = strpos($line, 'VALUE=DATE') !== false;
                } elseif (strpos($line, 'DTEND') === 0) {
                    $event['end_time'] = self::parseICSDate($line);
                }
            }
        }

        return $events;
    }

    private static function parseICSDate($line)
    {
        $parts = explode(':', $line);
        if (count($parts) > 1) {
            $dateStr = $parts[1];
            // Basic ICS date format: YYYYMMDDTHHMMSSZ or YYYYMMDD
            if (strlen($dateStr) == 8) {
                return date('Y-m-d H:i:s', strtotime($dateStr));
            } elseif (strlen($dateStr) >= 15) {
                return date('Y-m-d H:i:s', strtotime($dateStr));
            }
        }
        return date('Y-m-d H:i:s');
    }
}
