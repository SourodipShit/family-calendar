<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'approve') {
    $family_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$family_id) {
        echo json_encode(["status" => "error", "message" => "Family ID is required."]);
        exit;
    }

    try {
        // Ensure the approved column exists in the families table
        Database::runPrepared("UPDATE families SET approved = 1 WHERE id = ?", [$family_id]);
        
        // Fetch family head details and the family email to send email and SMS
        $stmt = Database::runPrepared("SELECT u.name, u.email, u.phone, f.email AS family_email FROM users u JOIN user_family uf ON u.id = uf.user_id JOIN families f ON f.id = uf.family_id WHERE uf.family_id = ? AND u.role = 'family-head'", [$family_id]);
        $heads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($heads) {
            require_once __DIR__ . '/../../services/mail/Mail.php';
            require_once __DIR__ . '/../../services/sms/SmsService.php';
            require_once __DIR__ . '/../../services/sms/SmsTemplates.php';

            foreach ($heads as $head) {
                if (!empty($head['email'])) {
                    Mail::sendAccountApproved($head['email'], $head['name'], $head['family_email']);
                }
                
                if (!empty($head['phone'])) {
                    $smsText = SmsTemplates::accountApproved($head['name']);
                    SmsService::send($head['phone'], $smsText);
                }
            }
        }

        echo json_encode(["status" => "success", "message" => "Family approved successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to approve family: " . $e->getMessage()]);
    }
} elseif ($action === 'lock') {
    $family_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$family_id) {
        echo json_encode(["status" => "error", "message" => "Family ID is required."]);
        exit;
    }

    try {
        Database::runPrepared("UPDATE families SET is_locked = 1 WHERE id = ?", [$family_id]);
        echo json_encode(["status" => "success", "message" => "Family locked successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to lock family: " . $e->getMessage()]);
    }
} elseif ($action === 'unlock') {
    $family_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$family_id) {
        echo json_encode(["status" => "error", "message" => "Family ID is required."]);
        exit;
    }

    try {
        Database::runPrepared("UPDATE families SET is_locked = 0 WHERE id = ?", [$family_id]);
        echo json_encode(["status" => "success", "message" => "Family unlocked successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to unlock family: " . $e->getMessage()]);
    }
} elseif ($action === 'update_monthly_amount') {
    $family_id = $_POST['id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    if (!$family_id || $amount === null) {
        echo json_encode(["status" => "error", "message" => "Family ID and amount are required."]);
        exit;
    }

    require_once __DIR__ . '/../../classes/Family.php';
    $result = Family::updateMonthlyAmount($family_id, $amount);
    echo json_encode($result);
} elseif ($action === 'get_account') {
    $family_id = $_GET['id'] ?? $_POST['id'] ?? null;
    if (!$family_id) {
        echo json_encode(["status" => "error", "message" => "Family ID is required."]);
        exit;
    }

    require_once __DIR__ . '/../../classes/Account.php';
    $result = Account::getByFamilyId($family_id);
    echo json_encode($result);
} elseif ($action === 'update_account') {
    $account_id = $_POST['account_id'] ?? null;
    $next_billing_date = $_POST['next_billing_date'] ?? null;
    $account_status = $_POST['account_status'] ?? null;
    
    if (!$account_id || !$next_billing_date || !$account_status) {
        echo json_encode(["status" => "error", "message" => "Account ID, billing date, and status are required."]);
        exit;
    }

    require_once __DIR__ . '/../../classes/Account.php';
    $result = Account::update($account_id, $next_billing_date, $account_status);
    echo json_encode($result);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action."]);
}
