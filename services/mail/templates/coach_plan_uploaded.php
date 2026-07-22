<?php
$name = $data['name'] ?? 'User';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Coach Plan</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #2c3e50; margin: 0;">New Coach Plan Available</h1>
        </div>
        
        <p style="color: #34495e; font-size: 16px; line-height: 1.6;">Hello <strong><?= htmlspecialchars($name) ?></strong>,</p>
        
        <p style="color: #34495e; font-size: 16px; line-height: 1.6;">Your coach has just uploaded a new plan for your family.</p>
        <p style="color: #34495e; font-size: 16px; line-height: 1.6;">Please log in to your family dashboard and click <strong>"Import Plan to Calendar"</strong> to begin.</p>
        
        <div style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
            <a href="<?= rtrim(str_replace('/api', '', "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])), '/') ?>/users/index.php" style="display: inline-block; padding: 12px 24px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px;">Go to Dashboard</a>
        </div>
        
        <p style="color: #7f8c8d; font-size: 14px; text-align: center; margin-top: 30px;">
            Thank you for using Family Calendar!
        </p>
    </div>
</body>
</html>
