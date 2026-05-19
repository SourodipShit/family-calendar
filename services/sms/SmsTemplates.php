<?php

class SmsTemplates
{
    public static function eventReminder($userName, $eventName, $date, $time)
    {
        return "Hi {$userName}, reminder: '{$eventName}' is scheduled on {$date} at {$time}.";
    }

    public static function eventStartingSoon($eventName, $minutes)
    {
        return "Reminder: '{$eventName}' starts in {$minutes} minutes.";
    }

    public static function custom($message)
    {
        return $message;
    }
}