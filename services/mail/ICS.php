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
        
        $start = date('Ymd\THis', strtotime($event['start']));
        $end = date('Ymd\THis', strtotime($event['end']));
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
            "DTSTART:{$start}",
            "DTEND:{$end}",
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
