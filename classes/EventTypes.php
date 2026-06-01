<?php
require_once __DIR__ . '/../config/Database.php';

class EventTypes
{
    static function getAllEventTypes()
    {
        $types = Database::runPrepared("SELECT et.*, f.* FROM event_types AS et INNER JOIN families AS f ON et.family_id = f.id")->fetchAll(PDO::FETCH_ASSOC);
        return $types;
    }

    static function createEventType($data)
    {
        try {
            $allow_multiple_day = !empty($data['allow_multiple_day']) ? 1 : 0;
            $result = Database::runPrepared("INSERT INTO event_types (name, is_default, colour, family_id, allow_multiple_day) VALUES (?, ?, ?, ?, ?)", [
                $data['name'],
                $data['is_default'],
                $data['colour'],
                $data['family_id'],
                $allow_multiple_day
            ]);
            if ($result) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Event Type created successfully',
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    static function updateEventType($data)
    {
        try {
            $allow_multiple_day = !empty($data['allow_multiple_day']) ? 1 : 0;
            $result = Database::runPrepared("UPDATE event_types SET name = ?, is_default = ?, colour = ?, family_id = ?, allow_multiple_day = ? WHERE id = ?", [
                $data['name'],
                $data['is_default'],
                $data['colour'],
                $data['family_id'],
                $allow_multiple_day,
                $data['id']
            ]);
            if ($result) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Event Type updated successfully',
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    static function deleteEventType($data)
    {
        try {
            $result = Database::runPrepared("DELETE FROM event_types WHERE id = ?", [
                $data['id']
            ]);
            if ($result) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Event Type deleted successfully',
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    static function getEventTypeById($data)
    {
        try {
            $type = Database::runPrepared("SELECT * FROM event_types WHERE id = ?", [
                $data['id']
            ])->fetch(PDO::FETCH_ASSOC);
            return $type;
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    static function getEventTypesByFamilyId($data)
    {
        try {
            $types = Database::runPrepared("SELECT * FROM event_types WHERE family_id = ? OR (is_default = 1 AND family_id IS NULL)", [
                $data['family_id']
            ])->fetchAll(PDO::FETCH_ASSOC);
            return $types;
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
