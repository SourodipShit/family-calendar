<?php

require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../classes/Account.php';
require_once __DIR__ . '/../../../classes/Payment.php';
require_once __DIR__ . '/../../../classes/Family.php';
require_once __DIR__ . '/../../../classes/GlobalSettings.php';

// Include Stripe and Mailer (adjust paths if needed)
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

class BillingJob
{
    public static function run()
    {
        echo "Starting BillingJob...<br>\n";

        // Fetch API keys and settings
        $stripeKeyRes = GlobalSettings::getSetting('stripe_secret_key');
        $stripeKey = ($stripeKeyRes['status'] === 'success' && !empty($stripeKeyRes['data'])) ? $stripeKeyRes['data']['setting_value'] : '';

        $monthlyCostRes = GlobalSettings::getSetting('monthly_subscription_cost');
        $monthlyCost = ($monthlyCostRes['status'] === 'success' && !empty($monthlyCostRes['data'])) ? (float)$monthlyCostRes['data']['setting_value'] : 15.00; // Default $15

        $baseUrlRes = GlobalSettings::getSetting('base_url');
        $baseUrl = ($baseUrlRes['status'] === 'success' && !empty($baseUrlRes['data'])) ? rtrim($baseUrlRes['data']['setting_value'], '/') : 'http://localhost/project/family-calendar';

        if (empty($stripeKey)) {
            echo "Error: Stripe Secret Key not configured in GlobalSettings. Aborting BillingJob.<br>\n";
            return;
        }

        \Stripe\Stripe::setApiKey($stripeKey);

        // Get all accounts due for billing today
        $dueRes = Account::getDueAccounts();

        if ($dueRes['status'] !== 'success' || empty($dueRes['data'])) {
            echo "No accounts are due for billing today.<br>\n";
            return;
        }

        $dueAccounts = $dueRes['data'];
        echo "Found " . count($dueAccounts) . " accounts due for billing.<br>\n";

        foreach ($dueAccounts as $account) {
            $accountId = $account['id'];
            $familyId = $account['family_id'];
            $accountNumber = $account['account_number'];
            $invoiceDate = date('Y-m-d');

            try {
                // Fetch family details for monthly_amount and emailing
                $familyRes = Family::getFamily($familyId);
                if (!$familyRes) {
                    continue; // Skip if family not found
                }
                
                $familyName = isset($familyRes['name']) ? $familyRes['name'] : ('Family ' . $familyId);
                $familyMonthlyAmount = isset($familyRes['monthly_amount']) ? (float)$familyRes['monthly_amount'] : 0;
                $billingAmount = ($familyMonthlyAmount > 0) ? $familyMonthlyAmount : $monthlyCost;

                // Generate a Stripe Checkout Session for this family
                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Family Calendar Monthly Subscription - ' . $accountNumber,
                            ],
                            'unit_amount' => (int)($billingAmount * 100), // Stripe expects cents
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    // The site base URL should ideally come from GlobalSettings, but using a generic one for now
                    'success_url' => $baseUrl . '/payment_status.php?status=success',
                    'cancel_url' => $baseUrl . '/payment_status.php?status=failed',
                ]);

                $stripeSessionId = $checkout_session->id;
                $paymentUrl = $checkout_session->url;

                // Create the Payment record (unpaid) in our database
                $paymentRes = Payment::create($accountId, $billingAmount, $stripeSessionId, $invoiceDate);

                if ($paymentRes['status'] === 'success') {
                    $paymentId = $paymentRes['data']['id'];

                    // Family details are already fetched above, we just need to use them
                    // Get all family heads
                    $headsQuery = Database::runPrepared("
                        SELECT users.email, users.name 
                        FROM users 
                        INNER JOIN user_family ON users.id = user_family.user_id 
                        WHERE user_family.family_id = ? AND users.role = 'family-head'
                    ", [$familyId]);
                    $familyHeads = $headsQuery->fetchAll(PDO::FETCH_ASSOC);

                    // Generate PDF Invoice
                    require_once __DIR__ . '/../../../classes/PDF.php';
                    $pdfData = [
                        'family_name' => $familyName,
                        'account_number' => $accountNumber,
                        'invoice_date' => $invoiceDate,
                        'amount' => $billingAmount,
                        'stripe_link' => $paymentUrl
                    ];
                    
                    $pdfRes = PDF::generateBill($pdfData);
                    $pdfPath = '';
                    if ($pdfRes['status'] === 'success') {
                        $pdfPath = $pdfRes['file_path'];
                        Payment::updatePdfPath($paymentId, $pdfPath);
                    }

                    // Send Email
                    require_once __DIR__ . '/../../mail/Mail.php';
                    if (!empty($familyHeads)) {
                        foreach ($familyHeads as $head) {
                            Mail::sendInvoice($head['email'], $head['name'], $paymentUrl, $pdfPath, $invoiceDate, $billingAmount);
                        }
                    } else {
                        // Fallback to family email
                        $familyEmail = $familyRes['email'];
                        Mail::sendInvoice($familyEmail, $familyName, $paymentUrl, $pdfPath, $invoiceDate, $billingAmount);
                    }

                    echo "Created invoice for Account: $accountNumber | Session ID: $stripeSessionId<br>\n";
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                echo "Stripe Error for account $accountNumber: " . $e->getMessage() . "<br>\n";
            } catch (Exception $e) {
                echo "Error for account $accountNumber: " . $e->getMessage() . "<br>\n";
            }
        }

        echo "BillingJob finished.<br>\n";
    }
}
