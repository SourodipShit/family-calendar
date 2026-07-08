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
require_once __DIR__ . '/jobs/FetchFamilyEmailsJob.php';


// Set default timezone for server output
date_default_timezone_set('UTC');

// Set time limit to prevent 500 errors if loop runs long
set_time_limit(0);

$scriptStartTime = time();
$isCli = (php_sapi_name() === 'cli' || empty($_SERVER['REMOTE_ADDR']));
// If triggered via browser/web cron, just run once. If via CLI long-running process, run for 29 mins.
$maxExecutionTime = $isCli ? (29 * 60) : 0; 

do {
    $loopStartTime = time();

    echo "========================================<br>\n";
    echo "        FAMILY CALENDAR CRON            <br>\n";
    echo "========================================<br>\n";
    echo "Server Time (UTC): " . date('Y-m-d H:i:s') . "<br>\n";

    $indiaTz = new DateTimeZone('Asia/Kolkata');
    $indiaTime = new DateTime('now', $indiaTz);
    echo "Indian Time:       " . $indiaTime->format('Y-m-d H:i:s') . "<br>\n";
    echo "----------------------------------------<br><br>\n";

    // Run the Event Reminder Job
    echo "Checking for pending event reminders...<br>\n";
    EventReminderJob::run();

    echo "<br>Checking for family shared emails...<br>\n";
    FetchFamilyEmailsJob::run();

    echo "<br>Cron execution finished for this minute.<br>\n";
    echo "========================================<br><br>\n";

    // Flush output buffers to ensure logs are sent immediately if viewed in browser/terminal
    if (ob_get_level() > 0) ob_flush();
    flush();

    // Calculate sleep time to ensure the loop runs exactly once every 60 seconds
    if ($maxExecutionTime > 0) {
        $elapsed = time() - $loopStartTime;
        $sleepTime = 60 - $elapsed;
        
        if ($sleepTime > 0) {
            sleep($sleepTime);
        }
    }

} while ((time() - $scriptStartTime) < $maxExecutionTime);
