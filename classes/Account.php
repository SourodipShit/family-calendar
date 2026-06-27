<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/PromoCode.php";

class Account
{
    /**
     * Create a new billing account for a family.
     * Optionally apply a promo code to calculate the next billing date.
     */
    public static function create($familyId, $promoCodeStr = null)
    {
        try {
            // Generate a unique account number (e.g. FAM-12345678)
            $accountNumber = 'FAM-' . strtoupper(substr(uniqid(), -8));
            
            // Calculate next billing date
            $monthsToAdd = 1; // Default 1 month free trial or 1 month until first bill
            $accountStatus = 'trial';
            
            if (!empty($promoCodeStr)) {
                $promoCheck = PromoCode::verify($promoCodeStr);
                if ($promoCheck['status'] === 'success') {
                    $promoData = $promoCheck['data'];
                    $monthsToAdd += (int)$promoData['months_free'];
                    
                    // Increment promo code usage
                    PromoCode::incrementUsage($promoData['id']);
                }
            }
            
            // Calculate date
            $nextBillingDate = date('Y-m-d', strtotime("+$monthsToAdd months"));

            $sql = "INSERT INTO accounts (family_id, account_number, next_billing_date, account_status) VALUES (?, ?, ?, ?)";
            $params = [$familyId, $accountNumber, $nextBillingDate, $accountStatus];
            
            Database::runPrepared($sql, $params);
            $accountId = Database::getInstance()->lastInsertId();
            
            return [
                "status" => "success", 
                "message" => "Account created successfully", 
                "data" => [
                    "id" => $accountId,
                    "account_number" => $accountNumber,
                    "next_billing_date" => $nextBillingDate
                ]
            ];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to create account", "data" => $e->getMessage()];
        }
    }

    /**
     * Get an account by its family ID
     */
    public static function getByFamilyId($familyId)
    {
        try {
            $sql = "SELECT * FROM accounts WHERE family_id = ?";
            $stmt = Database::runPrepared($sql, [$familyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return ["status" => "success", "data" => $result];
            } else {
                return ["status" => "error", "message" => "Account not found"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch account", "data" => $e->getMessage()];
        }
    }
    
    /**
     * Get an account by its account ID
     */
    public static function getById($accountId)
    {
        try {
            $sql = "SELECT * FROM accounts WHERE id = ?";
            $stmt = Database::runPrepared($sql, [$accountId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return ["status" => "success", "data" => $result];
            } else {
                return ["status" => "error", "message" => "Account not found"];
            }
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch account", "data" => $e->getMessage()];
        }
    }

    /**
     * Get accounts that are due for billing today (or overdue)
     */
    public static function getDueAccounts()
    {
        try {
            $today = date('Y-m-d');
            $sql = "SELECT * FROM accounts WHERE next_billing_date <= ? AND account_status != 'suspended'";
            $stmt = Database::runPrepared($sql, [$today]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["status" => "success", "data" => $result];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to fetch due accounts", "data" => $e->getMessage()];
        }
    }

    /**
     * Push the next billing date forward (typically by 1 month after a successful payment)
     */
    public static function advanceBillingDate($accountId, $monthsToAdd = 1)
    {
        try {
            // First get the current billing date
            $accountRes = self::getById($accountId);
            if ($accountRes['status'] !== 'success') {
                return $accountRes;
            }
            
            $currentDate = $accountRes['data']['next_billing_date'];
            
            // If they are far behind, we should advance from today to avoid multiple bills instantly
            if (strtotime($currentDate) < time()) {
                $currentDate = date('Y-m-d');
            }
            
            $newDate = date('Y-m-d', strtotime("$currentDate +$monthsToAdd months"));
            
            $sql = "UPDATE accounts SET next_billing_date = ?, account_status = 'active' WHERE id = ?";
            Database::runPrepared($sql, [$newDate, $accountId]);
            
            return ["status" => "success", "message" => "Billing date advanced", "new_date" => $newDate];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => "Failed to update billing date", "data" => $e->getMessage()];
        }
    }
}
