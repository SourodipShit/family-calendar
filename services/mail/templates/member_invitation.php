<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #fef08a; color: #854d0e; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Invitation
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">You've Been Invited!</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= htmlspecialchars($name ?? "there") ?>,
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    You have been added to a Family Calendar account! Please use the link below to accept the invitation, set up your password, and join the family.
</p>

<div style="text-align: center; margin-bottom: 32px;">
    <a href="<?= $invitationLink ?? '#' ?>" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Join Family Calendar
    </a>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    If you did not expect this invitation, you can safely ignore this email.
</p>
