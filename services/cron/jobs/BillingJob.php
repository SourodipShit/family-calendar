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
                // Generate a Stripe Checkout Session for this family
                $checkout_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Family Calendar Monthly Subscription - ' . $accountNumber,
                            ],
                            'unit_amount' => (int)($monthlyCost * 100), // Stripe expects cents
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    // The site base URL should ideally come from GlobalSettings, but using a generic one for now
                    'success_url' => 'http://localhost/project/family-calendar/payment_success.php',
                    'cancel_url' => 'http://localhost/project/family-calendar/payment_failed.php',
                ]);

                $stripeSessionId = $checkout_session->id;
                $paymentUrl = $checkout_session->url;

                // Create the Payment record (unpaid) in our database
                $paymentRes = Payment::create($accountId, $monthlyCost, $stripeSessionId, $invoiceDate);

                if ($paymentRes['status'] === 'success') {
                    $paymentId = $paymentRes['data']['id'];

                    // TODO: Generate PDF Invoice here
                    // e.g., $pdfPath = InvoiceGenerator::generate($paymentId, $familyId);
                    // Payment::updatePdfPath($paymentId, $pdfPath);

                    // Fetch family details for emailing
                    $familyRes = Family::getFamily($familyId);
                    if ($familyRes['status'] === 'success') {
                        $familyName = $familyRes['data']['name'];

                        // We need the family head's email. For now, try sending to family email or fetch family head
                        $familyEmail = $familyRes['data']['email'];

                        // TODO: Send Email
                        // require_once __DIR__ . '/../../mail/Mail.php';
                        // Mail::sendInvoice($familyEmail, $familyName, $paymentUrl, $pdfPath);

                        echo "Created invoice for Account: $accountNumber | Session ID: $stripeSessionId<br>\n";
                    }
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
