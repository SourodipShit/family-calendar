<?php

class ICS
{
    /**
     * Build an ICS file content for an event
     * 
     * @param array $event Event data (title, start, end, location, description)
     * @return string
     */
    public static function build($event)
    {
        $summary = self::escapeString($event['title'] ?? 'Event');
        $description = self::escapeString($event['description'] ?? '');
        $location = self::escapeString($event['location'] ?? '');
        
        $isAllDay = !empty($event['is_all_day']);
        
        if ($isAllDay) {
            $start = date('Ymd', strtotime($event['start']));
            // For all-day events, the end date must be the day after the event ends (exclusive)
            $end = date('Ymd', strtotime(date('Y-m-d', strtotime($event['end']))) + 86400);
            $dtstart = "DTSTART;VALUE=DATE:{$start}";
            $dtend = "DTEND;VALUE=DATE:{$end}";
        } else {
            $start = date('Ymd\THis', strtotime($event['start']));
            $end = date('Ymd\THis', strtotime($event['end']));
            $dtstart = "DTSTART:{$start}";
            $dtend = "DTEND:{$end}";
        }
        
        $dtstamp = gmdate('Ymd\THis\Z');
        $uid = uniqid() . '@familycalendar.com';

        $ics = [
            "BEGIN:VCALENDAR",
            "VERSION:2.0",
            "PRODID:-//FamilyCalendar//EN",
            "METHOD:PUBLISH",
            "BEGIN:VEVENT",
            "UID:{$uid}",
            "DTSTAMP:{$dtstamp}",
            $dtstart,
            $dtend,
            "SUMMARY:{$summary}",
            "DESCRIPTION:{$description}",
            "LOCATION:{$location}",
            "END:VEVENT",
            "END:VCALENDAR"
        ];

        return implode("\r\n", $ics);
    }

    /**
     * Escape special characters for ICS format
     * 
     * @param string $string
     * @return string
     */
    private static function escapeString($string)
    {
        return str_replace(['\\', ',', ';', "\n", "\r"], ['\\\\', '\,', '\;', '\\n', ''], $string);
    }
}
