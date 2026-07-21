<?php
require_once __DIR__ . '/Points.php';

class Chore
{
    /**
     * Add a new chore and its initial instances
     */
    public static function add($data)
    {
        try {
            $sql = "INSERT INTO chores (family_id, title, assigned_to, reward_id, recurrence, repeat_until, start_date, created_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            Database::runPrepared($sql, [
                $data['family_id'],
                $data['title'],
                $data['assigned_to'],
                !empty($data['reward_id']) ? $data['reward_id'] : null,
                $data['recurrence'] ?? 'once',
                !empty($data['repeat_until']) ? $data['repeat_until'] : null,
                $data['start_date'],
                $data['created_by'],
                $data['status'] ?? 'active'
            ]);

            $choreId = Database::getLastInsertId();

            // Generate instances up to repeat_until
            self::generateInstancesForChore($choreId, $data['start_date'], $data['recurrence'], $data['repeat_until'] ?? null);

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

            $sql = "UPDATE chores SET title = ?, assigned_to = ?, reward_id = ?, recurrence = ?, repeat_until = ?, start_date = ?, status = ? WHERE id = ?";
            Database::runPrepared($sql, [
                $data['title'],
                $data['assigned_to'],
                !empty($data['reward_id']) ? $data['reward_id'] : null,
                $data['recurrence'] ?? 'once',
                !empty($data['repeat_until']) ? $data['repeat_until'] : null,
                $data['start_date'],
                $data['status'] ?? 'active',
                $id
            ]);

            // Re-generate instances in case recurrence or start date changed
            self::generateInstancesForChore($id, $data['start_date'], $data['recurrence'] ?? 'once', $data['repeat_until'] ?? null);

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
    public static function getByDateRange($startDate, $endDate, $familyId, $userId = null)
    {
        // Fallback to active session user if userId is not provided
        if (!$userId && isset($_SESSION) && isset($_SESSION['user']['id'])) {
            $userId = $_SESSION['user']['id'];
        }

        $sql = "SELECT ci.id as instance_id, ci.due_date, ci.status as instance_status, 
                       c.id as chore_id, c.title, c.recurrence, c.reward_id, c.assigned_to as assigned_to_id, c.created_by,
                       u.name as assigned_member, u.nickname as assigned_nickname, u.image as assigned_image,
                       tr.name as reward_name, tr.level as reward_level, tr.points as reward_points
                FROM chore_instances ci
                INNER JOIN chores c ON ci.chore_id = c.id
                INNER JOIN users u ON c.assigned_to = u.id
                LEFT JOIN theme_rewards tr ON c.reward_id = tr.id
                WHERE ci.due_date BETWEEN ? AND ?
                AND (
                    c.family_id = ?
                    OR c.assigned_to = ? 
                    OR c.created_by = ?
                    OR c.assigned_to IN (
                        SELECT receiver_id FROM family_requests WHERE requester_id = ? AND status = 'approved'
                    )
                    OR c.assigned_to IN (
                        SELECT requester_id FROM family_requests WHERE receiver_id = ? AND status = 'approved'
                    )
                    OR c.created_by IN (
                        SELECT receiver_id FROM family_requests WHERE requester_id = ? AND status = 'approved'
                    )
                    OR c.created_by IN (
                        SELECT requester_id FROM family_requests WHERE receiver_id = ? AND status = 'approved'
                    )
                )";
        return Database::runPrepared($sql, [$startDate, $endDate, $familyId, $userId, $userId, $userId, $userId, $userId, $userId])->fetchAll(PDO::FETCH_ASSOC);
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
            
            if (!$check || ($userId !== 0 && $check['assigned_to'] != $userId)) {
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
     * Head of Family or Creator (for external users) approves a completed chore
     */
    public static function approve($instanceId, $userId, $userRole = 'member')
    {
        try {
            // Get the chore details and points
            $sqlInfo = "SELECT c.assigned_to, c.title, tr.points, c.created_by, c.family_id 
                        FROM chore_instances ci 
                        JOIN chores c ON ci.chore_id = c.id 
                        LEFT JOIN theme_rewards tr ON c.reward_id = tr.id 
                        WHERE ci.id = ?";
            $info = Database::runPrepared($sqlInfo, [$instanceId])->fetch(PDO::FETCH_ASSOC);

            if (!$info) {
                return ["msg" => "Chore not found", "status" => "error"];
            }

            // Check if assignee is external
            $sqlExternal = "SELECT COUNT(*) FROM user_family WHERE user_id = ? AND family_id = ?";
            $isInternal = Database::runPrepared($sqlExternal, [$info['assigned_to'], $info['family_id']])->fetchColumn() > 0;
            $isExternal = !$isInternal;

            $canApprove = false;
            if ($userRole === 'family-head') {
                $canApprove = true;
            } elseif ($isExternal && $info['created_by'] == $userId) {
                $canApprove = true;
            }

            if (!$canApprove) {
                return ["msg" => "Unauthorized to approve this chore", "status" => "error"];
            }

            $sql = "UPDATE chore_instances SET status = 'complete', approved_by = ?, approved_at = NOW() WHERE id = ?";
            Database::runPrepared($sql, [$userId, $instanceId]);

            // Reward points logic
            if ($info && !empty($info['points']) && $info['points'] > 0) {
                Points::creditPoints($info['assigned_to'], (int)$info['points'], "Completed chore: " . $info['title']);
            }

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
     * Generate future instances of a chore up to $repeatUntil
     */
    private static function generateInstancesForChore($choreId, $startDate, $recurrence, $repeatUntil)
    {
        $dates = [];
        $start = new DateTime($startDate);
        
        // If it's a one-time chore, just do start date
        if ($recurrence == 'once' || empty($repeatUntil)) {
            $end = clone $start;
        } else {
            $end = new DateTime($repeatUntil);
        }

        // Failsafe in case end date is before start date
        if ($start > $end) {
            $end = clone $start; 
        }

        $current = clone $start;
        // Limit to max 5 years ahead to prevent infinite loops or huge inserts if user selects a crazy date
        $maxEnd = (clone $start)->modify('+5 years');
        if ($end > $maxEnd) {
            $end = $maxEnd;
        }

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
}
