<?php

require_once __DIR__ . "/Remainder.php";

class  Event
{
    public static function getEventsByDateRange($startDate, $endDate, $familyId)
    {
        $sql = "SELECT e.*, et.name AS type, et.colour AS color, em.user_id, u.name as member, u.nickname as member_nickname
                FROM events AS e 
                INNER JOIN event_types AS et ON e.type_id = et.id 
                INNER JOIN event_members AS em ON e.id = em.event_id 
                INNER JOIN users u ON em.user_id = u.id
                WHERE e.start_time BETWEEN ? AND ? AND e.family_id = ?";
        $events = Database::runPrepared($sql, [$startDate, $endDate, $familyId])->fetchAll(PDO::FETCH_ASSOC);
        return $events;
    }

    public static function add($data)
    {
        try {
            $sql = "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, event_repeat, remainder, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            Database::runPrepared($sql, [
                $data['family_id'],
                $data['title'],
                $data['description'] ?? null,
                $data['type_id'],
                $data['start_time'],
                $data['end_time'] ?? null,
                $data['location'] ?? null,
                $data['is_all_day'] ?? 0,
                $data['event_repeat'] ?? null,
                $data['remainder'] ?? null,
                $data['created_by']
            ]);

            $id = Database::getInstance()->lastInsertId();
            if (isset($data['members'])) {
                foreach ($data['members'] as $memberId) {
                    Event::addMember($id, $memberId);
                }
            }
            return ["msg" => "Event added successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    public static function addMember($eventId, $userId)
    {
        $sql = "INSERT INTO event_members (event_id, user_id) VALUES (?, ?)";
        Database::runPrepared($sql, [$eventId, $userId]);
        
        // Automatically create placeholder reminders for the member
        self::addReminder($eventId, $userId);
    }

    /**
     * Create default reminders for a member (Mail, SMS, Call)
     */
    public static function addReminder($eventId, $memberId)
    {
        // Use Remainder class to create all 3 types
        Remainder::create(['event_id' => $eventId, 'member_id' => $memberId, 'type' => 'mail']);
        Remainder::create(['event_id' => $eventId, 'member_id' => $memberId, 'type' => 'sms']);
        Remainder::create(['event_id' => $eventId, 'member_id' => $memberId, 'type' => 'call']);
    }

    /**
     * Fetch events that are due for a reminder
     */
    public static function getPendingReminders()
    {
        $sql = "SELECT e.*, u.id as user_id, u.name as user_name, u.email as user_email, 
                       er.id as reminder_id, er.type as reminder_type, er.status as reminder_status,
                       f.id as f_id, f.name as f_name, f.email as f_email, f.location as f_location, 
                       f.timezone as f_timezone, f.settings as f_settings, f.approved as f_approved
                FROM events e
                INNER JOIN event_reminders er ON e.id = er.event_id
                INNER JOIN users u ON er.member_id = u.id
                INNER JOIN families f ON e.family_id = f.id
                WHERE e.remainder IS NOT NULL
                  AND DATE_SUB(e.start_time, INTERVAL CAST(e.remainder AS UNSIGNED) MINUTE) >= DATE_ADD(NOW(), INTERVAL '05:30' HOUR_MINUTE)
                  AND e.start_time > DATE_ADD(NOW(), INTERVAL '05:30' HOUR_MINUTE)
                  AND er.status = 'pending'
                  AND er.type = 'mail'";

        try {
            $results = Database::run($sql)->fetchAll(PDO::FETCH_ASSOC);
            $events = [];

            foreach ($results as $row) {
                $eventTask = $row;
                // Nest user data
                $eventTask['user'] = [
                    'id' => $row['user_id'],
                    'name' => $row['user_name'],
                    'email' => $row['user_email']
                ];
                // Nest family data
                $eventTask['family'] = [
                    'id' => $row['f_id'],
                    'name' => $row['f_name'],
                    'email' => $row['f_email'],
                    'location' => $row['f_location'],
                    'timezone' => $row['f_timezone'],
                    'settings' => $row['f_settings'],
                    'approved' => $row['f_approved']
                ];
                // Nest reminder data as plural as requested
                $eventTask['remainders'] = [
                    [
                        'id' => $row['reminder_id'],
                        'type' => $row['reminder_type'],
                        'status' => $row['reminder_status']
                    ]
                ];
                $events[] = $eventTask;
            }

            return $events;
        } catch (Exception $e) {
            return [];
        }
    }
}
