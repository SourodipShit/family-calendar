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
}
