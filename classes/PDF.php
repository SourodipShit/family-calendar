<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    /**
     * Generate an invoice bill and save it to the server
     *
     * @param array $data Expected to contain family_name, invoice_date, amount, stripe_link, account_number, etc.
     * @return array Status and file paths
     */
    public static function generateBill($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = self::getInvoiceHtml($data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $output = $dompdf->output();
        
        // Define path
        $uploadDir = __DIR__ . '/../public/uploads/pdfs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'invoice_' . ($data['account_number'] ?? time()) . '_' . date('Y_m_d') . '.pdf';
        $filePath = $uploadDir . $fileName;
        
        if (file_put_contents($filePath, $output) !== false) {
            return [
                'status' => 'success',
                'message' => 'PDF generated successfully',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'public_path' => '../public/uploads/pdfs/' . $fileName
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to write PDF file to disk'
            ];
        }
    }
    
    private static function getInvoiceHtml($data)
    {
        $familyName = htmlspecialchars($data['family_name'] ?? 'Valued Customer');
        $accountNumber = htmlspecialchars($data['account_number'] ?? 'N/A');
        $invoiceDate = htmlspecialchars($data['invoice_date'] ?? date('Y-m-d'));
        $amount = number_format($data['amount'] ?? 0, 2);
        $stripeLink = htmlspecialchars($data['stripe_link'] ?? '#');
        $invoiceTitle = htmlspecialchars($data['invoice_title'] ?? 'Monthly Subscription Invoice');
        $itemDescription = htmlspecialchars($data['item_description'] ?? 'Monthly Subscription Plan');
        
        return "
        <html>
        <head>
            <style>
                body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #333; }
                .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
                .header { display: table; width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
                .header-left { display: table-cell; vertical-align: top; }
                .header-right { display: table-cell; text-align: right; }
                h1 { margin: 0; color: #4a4a4a; }
                .details { margin-bottom: 30px; line-height: 1.6; }
                .table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
                .table th { background: #eee; border-bottom: 1px solid #ddd; padding: 10px; }
                .table td { padding: 10px; border-bottom: 1px solid #eee; }
                .total { font-weight: bold; font-size: 1.2em; color: #d9534f; }
                .btn { display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='invoice-box'>
                <div class='header'>
                    <div class='header-left'>
                        <h1>Family Calendar</h1>
                        <p>{$invoiceTitle}</p>
                    </div>
                    <div class='header-right'>
                        <strong>Invoice Date:</strong> {$invoiceDate}<br>
                        <strong>Account No:</strong> {$accountNumber}
                    </div>
                </div>
                
                <div class='details'>
                    <strong>Billed To:</strong><br>
                    {$familyName}
                </div>
                
                <table class='table'>
                    <tr>
                        <th>Description</th>
                        <th style='text-align: right;'>Amount</th>
                    </tr>
                    <tr>
                        <td>{$itemDescription}</td>
                        <td style='text-align: right;'>\${$amount}</td>
                    </tr>
                    <tr>
                        <td style='text-align: right;' class='total'>Total Due:</td>
                        <td style='text-align: right;' class='total'>\${$amount}</td>
                    </tr>
                </table>
                
                <div style='text-align: center; margin-top: 40px;'>
                    <p>Please pay your invoice securely via Stripe.</p>
                    <a href='{$stripeLink}' class='btn'>Pay Invoice Now</a>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
