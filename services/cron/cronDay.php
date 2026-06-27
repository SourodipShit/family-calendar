<?php

/**
 * Daily Cron Entry Point
 * This file should be triggered by a system cron job once per day (e.g., at midnight).
 */

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/jobs/ChoresCreateJob.php';
require_once __DIR__ . '/jobs/BillingJob.php';

// Set default timezone for server output
date_default_timezone_set('UTC');

echo "========================================<br>\n";
echo "    FAMILY CALENDAR DAILY CRON          <br>\n";
echo "========================================<br>\n";
echo "Server Time (UTC): " . date('Y-m-d H:i:s') . "<br>\n";

$indiaTz = new DateTimeZone('Asia/Kolkata');
$indiaTime = new DateTime('now', $indiaTz);
echo "Indian Time:       " . $indiaTime->format('Y-m-d H:i:s') . "<br>\n";
echo "----------------------------------------<br><br>\n";

// Run the Chores Create Job
echo "Running daily chore instance generation...<br>\n";
ChoresCreateJob::run();

echo "<br>Running daily billing cycle...<br>\n";
BillingJob::run();

echo "<br>Daily cron execution finished.<br>\n";
echo "========================================<br><br>\n";

// Flush output buffers
if (ob_get_level() > 0) ob_flush();
flush();
