<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #eef2ff; color: #4f46e5; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Password Reset
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">Reset Your Password</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= $user['name'] ?? "there" ?>,
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    We received a request to reset the password for your Family Calendar account. Please use the following One-Time Password (OTP) to proceed with your password reset. 
</p>

<!-- OTP Card -->
<div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; text-align: center; margin-bottom: 24px;">
    <p style="font-size: 14px; color: #64748b; margin-top: 0; margin-bottom: 12px; font-weight: 600;">YOUR 6-DIGIT OTP</p>
    <div style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #0f172a;">
        <?= $otp ?? "XXXXXX" ?>
    </div>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    This OTP will expire in 10 minutes.<br>
    If you did not request a password reset, please ignore this email or contact support if you have concerns.
</p>

<div style="text-align: center;">
    <a href="<?= $baseUrl ?? '#' ?>" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Go to Login
    </a>
</div>
