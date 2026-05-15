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


// Run the Event Reminder Job
EventReminderJob::run();
