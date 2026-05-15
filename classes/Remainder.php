<?php

require_once __DIR__ . "/../config/Database.php";

class Remainder
{
    /**
     * Create a new reminder
     */
    public static function create($data)
    {
        try {
            $sql = "INSERT INTO event_reminders (event_id, member_id, type, sent_at, status) VALUES (?, ?, ?, ?, ?)";
            Database::runPrepared($sql, [
                $data['event_id'],
                $data['member_id'],
                $data['type'],
                $data['sent_at'] ?? null,
                $data['status'] ?? 'pending'
            ]);
            return ["status" => "success", "message" => "Reminder created successfully", "id" => Database::getInstance()->lastInsertId()];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get a reminder by ID
     */
    public static function getById($id)
    {
        try {
            $stmt = Database::runPrepared("SELECT * FROM event_reminders WHERE id = ?", [$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result ?: null];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Update a reminder
     */
    public static function update($id, $data)
    {
        try {
            $fields = [];
            $params = [];
            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;

            $sql = "UPDATE event_reminders SET " . implode(", ", $fields) . " WHERE id = ?";
            Database::runPrepared($sql, $params);
            return ["status" => "success", "message" => "Reminder updated successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Delete a reminder
     */
    public static function delete($id)
    {
        try {
            Database::runPrepared("DELETE FROM event_reminders WHERE id = ?", [$id]);
            return ["status" => "success", "message" => "Reminder deleted successfully"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Get all reminders for an event
     */
    public static function getByEvent($eventId)
    {
        try {
            $stmt = Database::runPrepared("SELECT * FROM event_reminders WHERE event_id = ?", [$eventId]);
            return ["status" => "success", "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
