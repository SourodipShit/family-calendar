<?php
require_once __DIR__ . "/../config/Database.php";

class Payment
{
    /**
     * Create a new payment record (unpaid invoice)
     */
    public static function create($accountId, $amount, $stripeSessionId, $invoiceDate = null)
    {
        try {
            if (!$invoiceDate) {
                $invoiceDate = date('Y-m-d');
            }

            $sql = "INSERT INTO payments (account_id, invoice_date, amount, stripe_session_id, status) VALUES (?, ?, ?, ?, 'unpaid')";
            $params = [$accountId, $invoiceDate, $amount, $stripeSessionId];
            
            Database::runPrepared($sql, $params);
            $paymentId = Database::getInstance()->lastInsertId();
            
            return ["status" => "success", "message" => "Payment record created", "data" => ["id" => $paymentId]];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to create payment record", "data" => $e->getMessage()];
        }
    }

    /**
     * Get all payments for a specific account
     */
    public static function getByAccountId($accountId)
    {
        try {
            $sql = "SELECT * FROM payments WHERE account_id = ? ORDER BY invoice_date DESC, created_at DESC";
            $stmt = Database::runPrepared($sql, [$accountId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch payments", "data" => $e->getMessage()];
        }
    }

    /**
     * Update the status of a payment by its Stripe Session ID
     * (used by the Stripe Webhook)
     */
    public static function updateStatusBySessionId($sessionId, $status)
    {
        try {
            // First verify it exists
            $sql = "SELECT id, account_id FROM payments WHERE stripe_session_id = ?";
            $stmt = Database::runPrepared($sql, [$sessionId]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                return ["status" => "error", "message" => "Payment session not found"];
            }

            $updateSql = "UPDATE payments SET status = ? WHERE id = ?";
            Database::runPrepared($updateSql, [$status, $payment['id']]);
            
            return [
                "status" => "success", 
                "message" => "Payment status updated", 
                "data" => [
                    "payment_id" => $payment['id'],
                    "account_id" => $payment['account_id']
                ]
            ];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to update payment status", "data" => $e->getMessage()];
        }
    }

    /**
     * Update PDF path for an invoice
     */
    public static function updatePdfPath($paymentId, $path)
    {
        try {
            $sql = "UPDATE payments SET pdf_path = ? WHERE id = ?";
            Database::runPrepared($sql, [$path, $paymentId]);
            return ["status" => "success", "message" => "PDF path updated"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to update PDF path", "data" => $e->getMessage()];
        }
    }
}
