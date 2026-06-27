<?php
require_once __DIR__ . "/../config/Database.php";

class Points
{
    public static function createInstance($userId)
    {
        try {
            $sql = "INSERT INTO user_points (user_id) VALUES (?)";
            $result = Database::runPrepared($sql, [$userId]);
            return ["status" => "success", "message" => "Points created successfully", "data" => $result];
        } catch (Exception $e) {
            return ["status" => "error", "message" => "Failed to create points", "data" => $e->getMessage()];
        }
    }

    public static function creditPoints($userId, $amount, $description = 'Points credited')
    {
        try {
            $db = Database::getInstance();
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) {
                $db->beginTransaction();
            }

            $sql = "UPDATE user_points SET balance = balance + ? WHERE user_id = ?";
            Database::runPrepared($sql, [$amount, $userId]);
            
            $sqlGet = "SELECT balance FROM user_points WHERE user_id = ?";
            $newBalance = Database::runPrepared($sqlGet, [$userId])->fetchColumn();

            self::recordTransaction($userId, $amount, 'earn', $newBalance, $description);

            if (!$inTransaction) {
                $db->commit();
            }
            return ["status" => "success", "message" => "Points credited successfully"];
        } catch (Exception $e) {
            if (isset($db) && isset($inTransaction) && !$inTransaction && $db->inTransaction()) {
                $db->rollBack();
            }
            return ["status" => "error", "message" => "Failed to credit points", "data" => $e->getMessage()];
        }
    }

    public static function debitPoints($userId, $amount, $description = 'Points debited')
    {
        try {
            $db = Database::getInstance();
            $inTransaction = $db->inTransaction();
            if (!$inTransaction) {
                $db->beginTransaction();
            }
            
            $sql = "UPDATE user_points SET balance = balance - ? WHERE user_id = ?";
            Database::runPrepared($sql, [$amount, $userId]);

            $sqlGet = "SELECT balance FROM user_points WHERE user_id = ?";
            $newBalance = Database::runPrepared($sqlGet, [$userId])->fetchColumn();

            self::recordTransaction($userId, $amount, 'redeem', $newBalance, $description);

            if (!$inTransaction) {
                $db->commit();
            }
            return ["status" => "success", "message" => "Points debited successfully"];
        } catch (Exception $e) {
            if (isset($db) && isset($inTransaction) && !$inTransaction && $db->inTransaction()) {
                $db->rollBack();
            }
            return ["status" => "error", "message" => "Failed to debit points", "data" => $e->getMessage()];
        }
    }

    public static function getPoints($userId)
    {
        try {
            $sql = "SELECT balance FROM user_points WHERE user_id = ?";
            $result = Database::runPrepared($sql, [$userId]);
            return ["status" => "success", "message" => "Points fetched successfully", "data" => $result->fetch(PDO::FETCH_ASSOC)];
        } catch (Exception $e) {
            return ["status" => "error", "message" => "Failed to fetch points", "data" => $e->getMessage()];
        }
    }

    private static function recordTransaction($userId, $points, $type, $balanceAfter, $description)
    {
        $sql = "INSERT INTO point_transactions (user_id, type, points, balance_after, description) VALUES (?, ?, ?, ?, ?)";
        Database::runPrepared($sql, [$userId, $type, $points, $balanceAfter, $description]);
    }

    public static function getTransactionsByUserId($userId)
    {
        try {
            $sql = "SELECT * FROM point_transactions WHERE user_id = ? ORDER BY created_at DESC";
            $result = Database::runPrepared($sql, [$userId])->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "message" => "Transactions fetched successfully", "data" => $result];
        } catch (Exception $e) {
            return ["status" => "error", "message" => "Failed to fetch transactions", "data" => $e->getMessage()];
        }
    }
}
