<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        New Member Added
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">Family Member Added</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= htmlspecialchars($headName ?? "there") ?>,
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    A new member, <strong><?= htmlspecialchars($newMemberName ?? "someone") ?></strong>, has been successfully added to your family account.
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    You can manage your family members by logging into your account.
</p>

<div style="text-align: center; margin-bottom: 32px;">
    <a href="<?= $baseUrl ?? '#' ?>/login.php" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Manage Family
    </a>
</div>
