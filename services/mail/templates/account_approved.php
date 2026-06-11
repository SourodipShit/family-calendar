<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Account Approved
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">Welcome to Family Calendar!</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= htmlspecialchars($name ?? "there") ?>,
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Great news! Your family account has been successfully approved by the site administrator. You can now log in and start organizing your family's schedule, recipes, and more.
</p>

<div style="text-align: center; margin-bottom: 32px;">
    <a href="<?= $baseUrl ?? '#' ?>/login.php" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Log In Now
    </a>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    If you have any questions, feel free to reach out to our support team.
</p>
