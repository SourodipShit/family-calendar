<?php
$allocated = $storageDetails['allocated_storage'] ?? 500;
$used = $storageDetails['approved_storage'] ?? 500;
$percentage = ($allocated > 0) ? min(100, ($used / $allocated) * 100) : 100;

if ($percentage > 90) {
    $barColor = '#ef4444'; // red
} elseif ($percentage > 70) {
    $barColor = '#eab308'; // yellow
} else {
    $barColor = '#3b82f6'; // blue
}
?>
<div style="text-align: center; margin-bottom: 24px;">
    <span style="background-color: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
        Storage Limit Exceeded
    </span>
</div>

<h2 style="color: #0f172a; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; text-align: center;">Storage Capacity Reached!</h2>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Hi <?= htmlspecialchars($name ?? "there") ?>,
</p>

<!-- Storage Bar Section -->
<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
        <strong style="color: #0f172a;">Storage Used</strong>
        <span style="color: #64748b; font-weight: 500;"><span style="color: #ef4444; font-weight: 700;"><?= $used ?> MB</span> / <?= $allocated ?> MB</span>
    </div>
    <div style="background-color: #e2e8f0; border-radius: 9999px; height: 10px; width: 100%; overflow: hidden;">
        <div style="background-color: <?= $barColor ?>; height: 100%; width: <?= $percentage ?>%;"></div>
    </div>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Your family's storage limit has been exceeded. If you approve new photos, some of your older photos will be automatically deleted to adjust the storage capacity for the new ones.
</p>

<p style="font-size: 16px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
    Alternatively, you can manually delete some photos you no longer need to make room for new ones.
</p>

<div style="text-align: center; margin-bottom: 32px;">
    <a href="<?= $baseUrl ?? '#' ?>/users/photos.php" style="background-color: #ef4444; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; display: inline-block;">
        Manage Storage
    </a>
</div>

<p style="font-size: 14px; color: #64748b; text-align: center; margin-bottom: 32px;">
    If you have any questions, feel free to reach out to our support team.
</p>