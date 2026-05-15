<?php

class EventReminderJob
{
    /**
     * Run the event reminder check and dispatch emails
     */
    public static function run()
    {
        // Fetch tasks that meet the basic SQL criteria (past threshold, not yet started, mail not sent)
        $tasks = Event::getPendingReminders();

        if (empty($tasks)) {
            return;
        }

        foreach ($tasks as $task) {
            try {
                // Get the family's specific timezone
                $familyTimezone = $task['family']['timezone'] ?? 'UTC';
                $tz = new DateTimeZone($familyTimezone);

                // Current time in family's local timezone
                $now = new DateTime('now', $tz);

                // Event start time interpreted in the family's local timezone
                $eventStart = new DateTime($task['start_time'], $tz);

                // Calculate the exact time the reminder should be sent
                $reminderMinutes = (int)($task['remainder'] ?? 0);
                $reminderThreshold = clone $eventStart;
                $reminderThreshold->modify("-{$reminderMinutes} minutes");

                /**
                 * Compare local times:
                 * 1. Is the current local time at or past the reminder threshold?
                 * 2. Is the event still in the future?
                 */
                if ($now >= $reminderThreshold && $now < $eventStart) {
                    
                    // Map aliases for the mailer and ICS generator
                    $task['start'] = $task['start_time'];
                    $task['end'] = $task['end_time'];

                    // Dispatch the reminder email
                    $success = Mail::eventReminder($task['user'], $task);

                    if ($success) {
                        // Mark associated reminders as sent
                        if (!empty($task['remainders'])) {
                            foreach ($task['remainders'] as $r) {
                                Remainder::update($r['id'], [
                                    'status' => 'sent',
                                    'sent_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    } else {
                        // Mark as failed if dispatch failed
                        if (!empty($task['remainders'])) {
                            foreach ($task['remainders'] as $r) {
                                Remainder::update($r['id'], ['status' => 'failed']);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Skip tasks with invalid timezone data or date parsing errors
                continue;
            }
        }
    }
}
