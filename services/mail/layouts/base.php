<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px; color: #1e293b;">

    <div style="max-width: 600px; margin: auto;">
        
        <!-- Header / Logo Area -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #4f46e5; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                <span style="color: #ef4444;">A</span>scinate <span style="font-weight: 300; color: #64748b;">Family</span>
            </h1>
        </div>

        <!-- Main Card -->
        <div style="background: #ffffff; padding: 40px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">

            <?= $content ?? "" ?>

            <!-- Footer Divider -->
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center;">
                <p style="font-size: 13px; color: #94a3b8; margin: 0;">
                    &copy; <?= date('Y') ?> Ascinate Technology. All rights reserved.
                </p>
                <div style="margin-top: 10px;">
                    <a href="#" style="color: #6366f1; text-decoration: none; font-size: 13px; font-weight: 500;">Settings</a>
                    <span style="color: #cbd5e1; margin: 0 8px;">&bull;</span>
                    <a href="#" style="color: #6366f1; text-decoration: none; font-size: 13px; font-weight: 500;">Unsubscribe</a>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
