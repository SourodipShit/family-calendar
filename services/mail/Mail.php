<?php

class Mail
{
    /**
     * Send an event reminder email with an ICS attachment
     * 
     * @param array $user User data (name, email)
     * @param array $event Event data (title, start, end, location, description)
     * @return bool
     */
    public static function eventReminder($user, $event)
    {
        $html = Mailer::render(
            'event_reminder',
            [
                'user' => $user,
                'event' => $event
            ]
        );

        $ics = ICS::build($event);

        return Mailer::send([
            'to' => $user['email'],
            'subject' => 'Reminder: ' . $event['title'],
            'html' => $html,
            'attachments' => [
                [
                    'name' => 'reminder.ics',
                    'type' => 'text/calendar',
                    'content' => $ics
                ]
            ]
        ]);
    }

    /**
     * Send a password reset email with an OTP
     * 
     * @param array $user User data (name, email)
     * @param string $otp The 6-digit OTP code
     * @return bool
     */
    public static function passwordReset($user, $otp)
    {
        require_once __DIR__ . '/Mailer.php';
        
        $html = Mailer::render(
            'password_reset',
            [
                'user' => $user,
                'otp' => $otp
            ]
        );

        return Mailer::send([
            'to' => $user['email'],
            'subject' => 'Password Reset Request',
            'html' => $html
        ]);
    }

    public static function sendAccountApproved($email, $name)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('account_approved', ['name' => $name]);
        return Mailer::send([
            'to' => $email,
            'subject' => 'Your Family Account has been Approved',
            'html' => $html
        ]);
    }

    public static function sendMemberAddedNotification($headEmail, $headName, $newMemberName)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('member_added_notification', [
            'headName' => $headName,
            'newMemberName' => $newMemberName
        ]);
        return Mailer::send([
            'to' => $headEmail,
            'subject' => 'A new member has been added to your Family',
            'html' => $html
        ]);
    }

    public static function sendFamilyRequest($email, $receiverName, $requesterName, $approvalLink)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('family_request', [
            'receiverName' => $receiverName,
            'requesterName' => $requesterName,
            'approvalLink' => $approvalLink
        ]);
        return Mailer::send([
            'to' => $email,
            'subject' => 'New Family Connection Request - Family Calendar',
            'html' => $html
        ]);
    }

    public static function sendMemberInvitation($email, $name, $invitationLink)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('member_invitation', [
            'name' => $name,
            'invitationLink' => $invitationLink
        ]);
        return Mailer::send([
            'to' => $email,
            'subject' => 'You are invited to join Family Calendar',
            'html' => $html
        ]);
    }
    public static function sendStorageLimitExceeded($email, $name, $storageDetails)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('storage_limit_exceeded', [
            'name' => $name,
            'storageDetails' => $storageDetails
        ]);
        return Mailer::send([
            'to' => $email,
            'subject' => 'Storage Limit Exceeded - Action Required',
            'html' => $html
        ]);
    }

    public static function sendSignupSuccess($email, $name, $familyEmail)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('signup_success', [
            'name' => $name,
            'familyEmail' => $familyEmail
        ]);
        return Mailer::send([
            'to' => $email,
            'subject' => 'Sign Up Successful - Pending Approval',
            'html' => $html
        ]);
    }

    public static function sendInvoice($email, $name, $paymentUrl, $pdfPath, $invoiceDate, $amount)
    {
        require_once __DIR__ . '/Mailer.php';
        $html = Mailer::render('invoice', [
            'name' => $name,
            'paymentUrl' => $paymentUrl,
            'invoiceDate' => $invoiceDate,
            'amount' => $amount
        ]);
        
        $attachments = [];
        if (!empty($pdfPath) && file_exists($pdfPath)) {
            $attachments[] = [
                'type' => 'application/pdf',
                'name' => basename($pdfPath),
                'content' => file_get_contents($pdfPath)
            ];
        }

        return Mailer::send([
            'to' => $email,
            'subject' => 'Your Monthly Invoice - Family Calendar',
            'html' => $html,
            'attachments' => $attachments
        ]);
    }
}
