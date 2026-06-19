<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Connection Request
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">New Family Request</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= htmlspecialchars($receiverName ?? "there") ?>,
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    <strong><?= htmlspecialchars($requesterName ?? "Someone") ?></strong> has requested to connect with your Family Calendar! By approving this request, they will be added to your family as an external member, allowing you to share events and collaborate.
</p>

<div style="text-align: center; margin-bottom: 32px;">
    <a href="<?= $approvalLink ?? '#' ?>" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Approve Request
    </a>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    If you do not want to connect with this user, you can safely ignore this email.
</p>
