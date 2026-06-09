<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #e0e7ff; color: #4338ca; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        New Event Invitation
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">You're Invited!</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= $user['name'] ?? "" ?>, a new event has been added to your family calendar.
</p>

<!-- Event Card -->
<div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div style="background-color: #6366f1; padding: 16px; text-align: center;">
        <h3 style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 600;"><?= $event['title'] ?? "" ?></h3>
    </div>
    <div style="padding: 24px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #64748b; width: 80px;">When:</td>
                <td style="padding: 8px 0; font-size: 14px; color: #1e293b; font-weight: 600;"><?= date('F j, Y, g:i a', strtotime($event['start'] ?? "")) ?></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Where:</td>
                <td style="padding: 8px 0; font-size: 14px; color: #1e293b; font-weight: 600;"><?= $event['location'] ?? 'Not specified' ?></td>
            </tr>
            <?php if (!empty($event['description'])): ?>
            <tr>
                <td colspan="2" style="padding: 16px 0 0 0; font-size: 14px; color: #64748b; line-height: 1.5; border-top: 1px solid #f1f5f9;">
                    <?= $event['description'] ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    We've attached a calendar invite (.ics) so you can easily add this to your phone.
</p>

<div style="text-align: center;">
    <a href="#" style="background-color: #6366f1; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        View in Calendar
    </a>
</div>
