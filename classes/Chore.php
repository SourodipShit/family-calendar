<?php

class Chore
{
    /**
     * Add a new chore and its initial instances
     */
    public static function add($data)
    {
        try {
            $sql = "INSERT INTO chores (family_id, title, assigned_to, reward_id, recurrence, start_date, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            Database::runPrepared($sql, [
                $data['family_id'],
                $data['title'],
                $data['assigned_to'],
                !empty($data['reward_id']) ? $data['reward_id'] : null,
                $data['recurrence'] ?? 'once',
                $data['start_date'],
                $data['created_by'],
                $data['status'] ?? 'active'
            ]);

            $choreId = Database::getLastInsertId();

            // Generate initial instances for the next 30 days
            self::generateInstancesForChore($choreId, $data['start_date'], $data['recurrence']);

            return ["msg" => "Chore added successfully", "status" => "success", "id" => $choreId];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Edit an existing chore
     */
    public static function edit($id, $data, $userId)
    {
        try {
            $check = Database::runPrepared("SELECT created_by FROM chores WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if (!$check || $check['created_by'] != $userId) {
                return ["msg" => "Unauthorized", "status" => "error"];
            }

            $sql = "UPDATE chores SET title = ?, assigned_to = ?, reward_id = ?, recurrence = ?, start_date = ?, status = ? WHERE id = ?";
            Database::runPrepared($sql, [
                $data['title'],
                $data['assigned_to'],
                !empty($data['reward_id']) ? $data['reward_id'] : null,
                $data['recurrence'] ?? 'once',
                $data['start_date'],
                $data['status'] ?? 'active',
                $id
            ]);

            // Re-generate instances in case recurrence or start date changed
            self::generateInstancesForChore($id, $data['start_date'], $data['recurrence'] ?? 'once');

            return ["msg" => "Chore updated successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Delete a chore
     */
    public static function delete($id, $userId)
    {
        try {
            $check = Database::runPrepared("SELECT created_by FROM chores WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
            if (!$check || $check['created_by'] != $userId) {
                return ["msg" => "Unauthorized", "status" => "error"];
            }

            Database::runPrepared("DELETE FROM chores WHERE id = ?", [$id]);
            return ["msg" => "Chore deleted successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Fetch chores by date range for the calendar
     */
    public static function getByDateRange($startDate, $endDate, $familyId)
    {
        $sql = "SELECT ci.id as instance_id, ci.due_date, ci.status as instance_status, 
                       c.id as chore_id, c.title, c.recurrence, c.reward_id, c.assigned_to as assigned_to_id,
                       u.name as assigned_member, u.nickname as assigned_nickname, u.image as assigned_image,
                       tr.name as reward_name, tr.level as reward_level, tr.points as reward_points
                FROM chore_instances ci
                INNER JOIN chores c ON ci.chore_id = c.id
                INNER JOIN users u ON c.assigned_to = u.id
                LEFT JOIN theme_rewards tr ON c.reward_id = tr.id
                WHERE c.family_id = ? AND ci.due_date BETWEEN ? AND ?";
        return Database::runPrepared($sql, [$familyId, $startDate, $endDate])->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Member requests a chore to be marked as completed
     */
    public static function requestComplete($instanceId, $userId)
    {
        try {
            // Verify assignment
            $sql = "SELECT c.assigned_to FROM chore_instances ci INNER JOIN chores c ON ci.chore_id = c.id WHERE ci.id = ?";
            $check = Database::runPrepared($sql, [$instanceId])->fetch(PDO::FETCH_ASSOC);
            
            if (!$check || $check['assigned_to'] != $userId) {
                return ["msg" => "Unauthorized or chore not found", "status" => "error"];
            }

            $sql = "UPDATE chore_instances SET status = 'requested', requested_at = NOW() WHERE id = ?";
            Database::runPrepared($sql, [$instanceId]);
            return ["msg" => "Chore requested for completion", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Head of Family approves a completed chore
     */
    public static function approve($instanceId, $userId)
    {
        try {
            // Note: Ideally, we should verify that $userId is the Head of Family for this chore's family
            $sql = "UPDATE chore_instances SET status = 'complete', approved_by = ?, approved_at = NOW() WHERE id = ?";
            Database::runPrepared($sql, [$userId, $instanceId]);

            // Reward points logic can be integrated here later based on the chore's reward_id
            return ["msg" => "Chore approved successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Skip a chore instance
     */
    public static function skip($instanceId, $userId, $userRole)
    {
        try {
            if ($userRole !== 'family-head') {
                // Verify assignment if not family head
                $sql = "SELECT c.assigned_to FROM chore_instances ci INNER JOIN chores c ON ci.chore_id = c.id WHERE ci.id = ?";
                $check = Database::runPrepared($sql, [$instanceId])->fetch(PDO::FETCH_ASSOC);
                
                if (!$check || $check['assigned_to'] != $userId) {
                    return ["msg" => "Unauthorized or chore not found", "status" => "error"];
                }
            }

            $sql = "UPDATE chore_instances SET status = 'skipped' WHERE id = ?";
            Database::runPrepared($sql, [$instanceId]);
            return ["msg" => "Chore skipped successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }

    /**
     * Generate future instances of a chore up to $daysAhead
     */
    private static function generateInstancesForChore($choreId, $startDate, $recurrence, $daysAhead = 30)
    {
        $dates = [];
        $start = new DateTime($startDate);
        $end = new DateTime();
        $end->modify("+$daysAhead days");

        // If the start date is in the future past our window, just generate the first one
        if ($start > $end) {
            $end = clone $start; 
        }

        $current = clone $start;
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');

            if ($recurrence == 'once') {
                break;
            } elseif ($recurrence == 'daily') {
                $current->modify('+1 day');
            } elseif ($recurrence == 'weekly') {
                $current->modify('+1 week');
            } elseif ($recurrence == 'monthly') {
                $current->modify('+1 month');
            } elseif ($recurrence == 'quarterly') {
                $current->modify('+3 months');
            } elseif ($recurrence == 'yearly') {
                $current->modify('+1 year');
            }
        }

        // Insert instances only if they don't already exist to avoid duplicates
        foreach ($dates as $date) {
            $checkSql = "SELECT id FROM chore_instances WHERE chore_id = ? AND due_date = ?";
            $exists = Database::runPrepared($checkSql, [$choreId, $date])->fetch();
            
            if (!$exists) {
                $sql = "INSERT INTO chore_instances (chore_id, due_date, status) VALUES (?, ?, 'pending')";
                Database::runPrepared($sql, [$choreId, $date]);
            }
        }
    }

    /**
     * Cron Job function to keep chore instances updated and mark missed chores as overdue
     */
    public static function runCronJob()
    {
        try {
            // 1. Generate new instances for all active recurring chores
            $chores = Database::runPrepared("SELECT id, start_date, recurrence FROM chores WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($chores as $chore) {
                // We always look ahead 30 days from the original start date matching the recurrence pattern
                self::generateInstancesForChore($chore['id'], $chore['start_date'], $chore['recurrence'], 30);
            }
            
            // 2. Mark any past pending chores as skipped (acting as overdue)
            $sql = "UPDATE chore_instances SET status = 'skipped' WHERE due_date < CURDATE() AND status = 'pending'";
            Database::runPrepared($sql, []);
            
            return ["msg" => "Cron job ran successfully", "status" => "success"];
        } catch (Exception $e) {
            return ["msg" => $e->getMessage(), "status" => "error"];
        }
    }
}
