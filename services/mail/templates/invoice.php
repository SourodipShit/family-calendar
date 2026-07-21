<?php
// Extracted variables: $name, $paymentUrl, $invoiceDate, $amount, $baseUrl, $invoiceTitle
$title = $invoiceTitle ?? 'Your Monthly Invoice';
?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<p>Hi <?php echo htmlspecialchars($name ?? 'Family'); ?>,</p>
<p>Your invoice for Family Calendar is now available.</p>
<p>
    <strong>Invoice Date:</strong> <?php echo htmlspecialchars($invoiceDate ?? date('Y-m-d')); ?><br>
    <strong>Amount Due:</strong> $<?php echo htmlspecialchars(number_format($amount ?? 0.00, 2)); ?>
</p>
<p>You can pay your invoice securely using the link below:</p>
<p>
    <a href="<?php echo htmlspecialchars($paymentUrl ?? '#'); ?>" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Pay Invoice Now</a>
</p>
<p>A PDF copy of your invoice is attached to this email.</p>
<p>Thank you for using Family Calendar!</p>