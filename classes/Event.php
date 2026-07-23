<?php

require_once __DIR__ . "/Remainder.php";
require_once __DIR__ . "/GoogleCalendarService.php";
require_once __DIR__ . "/MicrosoftCalendarService.php";

class  Event
{
    public static function getEventsByDateRange($startDate, $endDate, $familyId, $userId = null)
    {
        $sql = "SELECT e.*, e.created_by, et.name AS type, et.colour AS color,
                       cet.status AS tracking_status, cet.feedback AS tracking_feedback
                FROM events AS e 
                INNER JOIN event_types AS et ON e.type_id = et.id 
                LEFT JOIN coach_event_tracking cet ON e.id = cet.event_id
                WHERE e.start_time BETWEEN ? AND ? 
                AND (
                    e.family_id = ? 
                    OR 
                    e.id IN (
                        SELECT event_id 
                        FROM event_members 
                        WHERE user_id = ?
                    )
                    OR 
                    e.id IN (
                        SELECT em2.event_id 
                        FROM event_members em2 
                        JOIN family_requests fr ON fr.receiver_id = em2.user_id 
                        WHERE fr.requester_id = ? AND fr.status = 'approved'
                    )
                    OR 
                    e.id IN (
                        SELECT em3.event_id 
                        FROM event_members em3 
                        JOIN family_requests fr ON fr.requester_id = em3.user_id 
                        WHERE fr.receiver_id = ? AND fr.status = 'approved'
                    )
                )";
        $events = Database::runPrepared($sql, [
            $startDate, 
            $endDate, 
            $familyId, 
            $userId, 
            $userId, 
            $userId
        ])->fetchAll(PDO::FETCH_ASSOC);
        return $events;
    }

    public static function getEventMembers($eventId)
    {
        $sql = "SELECT em.user_id, u.name as member, u.nickname as member_nickname, u.color
                FROM event_members AS em 
                INNER JOIN users u ON em.user_id = u.id
                WHERE em.event_id = ?";
        return Database::runPrepared($sql, [$eventId])->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function add($data)
    {
        try {
            $userId = $data['created_by'];
            $fId = $data['family_id'];

            $sql = "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, event_repeat, remainder, countdown, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            Database::runPrepared($sql, [
                $fId,
                $data['title'],
                $data['description'] ?? null,
                $data['type_id'],
                $data['start_time'],
                $data['end_time'] ?? null,
                $data['location'] ?? null,
                $data['is_all_day'] ?? 0,
                $data['event_repeat'] ?? null,
                $data['remainder'] ?? null,
                $data['countdown'] ?? 0,
                $userId
            ]);

            $id = Database::getInstance()->lastInsertId();
            
            if (isset($data['members'])) {
                foreach ($data['members'] as $memberId) {
                    Event::addMember($id, $memberId);
                }
            }

            GoogleCalendarService::pushEvent($userId, $id, $data);
            MicrosoftCalendarService::pushEvent($userId, $id, $data);

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

    public static function update($id, $data, $userId)
    {
        try {
            $check = Database::runPrepared("SELECT created_by FROM events WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if (!$check || $check['created_by'] != $userId) {
                return ["msg" => "Unauthorized", "status" => "error"];
            }

            $sql = "UPDATE events SET title = ?, description = ?, type_id = ?, start_time = ?, end_time = ?, location = ?, is_all_day = ?, event_repeat = ?, remainder = ?, countdown = ? WHERE id = ?";
            $params = [
                $data['title'], $data['description'] ?? null, $data['type_id'], $data['start_time'],
                $data['end_time'] ?? null, $data['location'] ?? null, $data['is_all_day'] ?? 0,
                $data['event_repeat'] ?? null, $data['remainder'] ?? null, $data['countdown'] ?? 0, $id
            ];
            Database::runPrepared($sql, $params);

            // Members are only updated for the explicit event ID being edited, as other families have different members
            if (isset($data['members'])) {
                Database::runPrepared("DELETE FROM event_members WHERE event_id = ?", [$id]);
                Database::runPrepared("DELETE FROM event_reminders WHERE event_id = ?", [$id]);
                foreach ($data['members'] as $memberId) {
                    Event::addMember($id, $memberId);
                }
            }

            GoogleCalendarService::pushEvent($userId, $id, $data);
            MicrosoftCalendarService::pushEvent($userId, $id, $data);

            return ["msg" => "Event updated successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    public static function delete($id, $userId)
    {
        try {
            $check = Database::runPrepared("SELECT created_by FROM events WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if (!$check || $check['created_by'] != $userId) {
                return ["msg" => "Unauthorized", "status" => "error"];
            }

            GoogleCalendarService::deleteEvent($userId, $id);
            MicrosoftCalendarService::deleteEvent($userId, $id);

            Database::runPrepared("DELETE FROM event_reminders WHERE event_id = ?", [$id]);
            Database::runPrepared("DELETE FROM event_members WHERE event_id = ?", [$id]);
            Database::runPrepared("DELETE FROM events WHERE id = ?", [$id]);

            return ["msg" => "Event deleted successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
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
                  AND er.status = 'pending'
                  AND er.type = 'mail'
                  AND DATE_SUB(e.start_time, INTERVAL CAST(e.remainder AS UNSIGNED) MINUTE) <= DATE_ADD(UTC_TIMESTAMP(), INTERVAL 24 HOUR)
                  AND e.start_time >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 24 HOUR)";

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
