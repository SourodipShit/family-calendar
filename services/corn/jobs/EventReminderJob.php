<?php

class EventReminderJob
{
    /**
     * Run the event reminder check and dispatch emails
     */
    public static function run()
    {
        // Fetch tasks that meet the basic SQL criteria (upcoming reminders, future events, mail pending)
        $tasks = Event::getPendingReminders();

        if (empty($tasks)) {
            echo "No pending reminders found in database query.\n";
            return;
        }

        echo "Found " . count($tasks) . " potential reminder tasks. Processing...\n\n";

        foreach ($tasks as $task) {
            try {
                $eventTitle = $task['title'];
                $targetUser = $task['user']['name'];
                $targetEmail = $task['user']['email'];

                echo "Checking Event: '{$eventTitle}' for User: '{$targetUser}' ({$targetEmail})\n";

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

                echo "  - Family Timezone: {$familyTimezone}\n";
                echo "  - Current Local Time: " . $now->format('Y-m-d H:i:s') . "\n";
                echo "  - Threshold Time:     " . $reminderThreshold->format('Y-m-d H:i:s') . "\n";
                echo "  - Event Start Time:   " . $eventStart->format('Y-m-d H:i:s') . "\n";

                /**
                 * Compare local times:
                 * 1. Is the current local time at or past the reminder threshold?
                 * 2. Is the event still in the future?
                 */
                if ($now >= $reminderThreshold && $now < $eventStart) {
                    echo "  >>> THRESHOLD MET: Sending email...\n";
                    
                    // Map aliases for the mailer and ICS generator
                    $task['start'] = $task['start_time'];
                    $task['end'] = $task['end_time'];

                    // Dispatch the reminder email
                    $success = Mail::eventReminder($task['user'], $task);

                    if ($success) {
                        echo "  SUCCESS: Mail sent to {$targetEmail}.\n";
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
                        echo "  FAILED: Mail dispatch error.\n";
                        // Mark as failed if dispatch failed
                        if (!empty($task['remainders'])) {
                            foreach ($task['remainders'] as $r) {
                                Remainder::update($r['id'], ['status' => 'failed']);
                            }
                        }
                    }
                } else {
                    if ($now < $reminderThreshold) {
                        echo "  Wait: Too early for this reminder.\n";
                    } else {
                        echo "  Skip: Event already started or passed.\n";
                    }
                }
                echo "\n";
            } catch (Exception $e) {
                echo "  ERROR: " . $e->getMessage() . "\n\n";
                continue;
            }
        }
    }
}
