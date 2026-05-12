<?php

require_once __DIR__ . "/../config/Database.php";

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
    }
}
