<?php

/**
 * Main Cron Entry Point
 * This file should be triggered by a system cron job (e.g., every minute)
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Event.php';
require_once __DIR__ . '/../../classes/Remainder.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../services/mail/ICS.php';
require_once __DIR__ . '/../../services/mail/Mailer.php';
require_once __DIR__ . '/../../services/mail/Mail.php';
require_once __DIR__ . '/jobs/EventReminderJob.php';


// Set default timezone for server output
date_default_timezone_set('UTC');

echo "========================================\n";
echo "        FAMILY CALENDAR CRON            \n";
echo "========================================\n";
echo "Server Time (UTC): " . date('Y-m-d H:i:s') . "\n";

$indiaTz = new DateTimeZone('Asia/Kolkata');
$indiaTime = new DateTime('now', $indiaTz);
echo "Indian Time:       " . $indiaTime->format('Y-m-d H:i:s') . "\n";
echo "----------------------------------------\n\n";

// Run the Event Reminder Job
echo "Checking for pending event reminders...\n";
EventReminderJob::run();
echo "\nCron execution finished.\n";
echo "========================================\n";
