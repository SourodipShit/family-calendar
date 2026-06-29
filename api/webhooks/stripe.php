<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/GlobalSettings.php';
require_once __DIR__ . '/../../classes/Account.php';
require_once __DIR__ . '/../../classes/Payment.php';

// Include Stripe library (assuming it's installed via composer in vendor)
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// Log function for debugging
function logWebhook($message) {
    $logFile = __DIR__ . '/../../scratch/stripe_webhook.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (empty($sig_header) || empty($payload)) {
    logWebhook("Error: Missing signature or payload.");
    http_response_code(400);
    exit("Missing signature or payload");
}

// Fetch webhook secret from global settings
$webhookSecretRes = GlobalSettings::getSetting('stripe_webhook_secret');
$endpoint_secret = ($webhookSecretRes['status'] === 'success' && !empty($webhookSecretRes['data'])) 
                    ? $webhookSecretRes['data']['setting_value'] : '';

if (empty($endpoint_secret)) {
    logWebhook("Error: Stripe Webhook Secret not configured.");
    http_response_code(500);
    exit("Webhook Secret not configured");
}

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    logWebhook("Error: Invalid payload.");
    http_response_code(400);
    exit("Invalid payload");
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    logWebhook("Error: Invalid signature.");
    http_response_code(400);
    exit("Invalid signature");
}

logWebhook("Received event: " . $event->type);

// Handle the event
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;
        
        $sessionId = $session->id;
        logWebhook("Processing session: $sessionId");

        // Update payment status
        $paymentRes = Payment::updateStatusBySessionId($sessionId, 'paid');
        
        if ($paymentRes['status'] === 'success') {
            $accountId = $paymentRes['data']['account_id'];
            
            // Advance billing date by 1 month
            $advanceRes = Account::advanceBillingDate($accountId, 1);
            
            if ($advanceRes['status'] === 'success') {
                logWebhook("Successfully updated payment and advanced billing date for account ID: $accountId");
            } else {
                logWebhook("Warning: Payment marked paid, but failed to advance billing date: " . json_encode($advanceRes));
            }
        } else {
            logWebhook("Error updating payment: " . json_encode($paymentRes));
        }
        break;

    default:
        logWebhook("Unhandled event type: " . $event->type);
        break;
}

http_response_code(200);
echo json_encode(["status" => "success"]);
