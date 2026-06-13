<?php

require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../classes/GlobalSettings.php';


class FetchFamilyEmailsJob
{
    public static function run()
    {
        echo "Starting FetchFamilyEmailsJob...<br>";

        // Fetch all allocated emails
        $emails = Database::runPrepared("SELECT * FROM family_shared_emails WHERE family_id IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);

        if (empty($emails)) {
            echo "No allocated shared emails found.<br>";
            return;
        }

        foreach ($emails as $account) {
            $familyId = $account['family_id'];
            $email = $account['email_address'];
            $password = $account['password'];

            echo "Processing email account: $email (Family ID: $familyId)<br>";

            $imapHostSetting = GlobalSettings::getSetting('imap_host')['data']['setting_value'] ?? null;
            $imapPortSetting = GlobalSettings::getSetting('imap_port')['data']['setting_value'] ?? '993';
            $imapFlagsSetting = GlobalSettings::getSetting('imap_flags')['data']['setting_value'] ?? '/imap/ssl/novalidate-cert';

            // Assuming standard IMAP setup for is4sb.com or dynamically extracting domain
            $domain = explode('@', $email)[1] ?? 'is4sb.com';
            $imapHost = !empty($imapHostSetting) ? $imapHostSetting : 'mail.' . $domain;

            $hostname = '{' . $imapHost . ':' . $imapPortSetting . $imapFlagsSetting . '}INBOX';

            // Suppress warnings in case connection fails
            $inbox = @imap_open($hostname, $email, $password);

            if (!$inbox) {
                $error = imap_last_error();
                echo "Cannot connect to $email. Error: $error<br>";
                continue;
            }

            // Search for UNSEEN emails
            $mailIds = imap_search($inbox, 'UNSEEN');

            if ($mailIds) {
                rsort($mailIds); // Optional: newest first

                foreach ($mailIds as $email_number) {
                    $structure = imap_fetchstructure($inbox, $email_number);
                    $headerInfo = imap_headerinfo($inbox, $email_number);
                    $fromEmail = '';
                    if (isset($headerInfo->from[0])) {
                        $fromEmail = $headerInfo->from[0]->mailbox . "@" . $headerInfo->from[0]->host;
                    }

                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $part = $structure->parts[$i];
                            self::processPart($inbox, $email_number, $i + 1, $part, $familyId, $fromEmail);
                        }
                    } else {
                        self::processPart($inbox, $email_number, 1, $structure, $familyId, $fromEmail);
                    }
                }
            } else {
                echo "No unseen emails for $email.<br>";
            }

            imap_close($inbox);
        }

        echo "Finished FetchFamilyEmailsJob.<br>";
    }

    private static function processPart($inbox, $email_number, $partNumber, $part, $familyId, $fromEmail = '')
    {
        $isAttachment = false;
        $filename = '';

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                if (strtolower($object->attribute) == 'filename') {
                    $filename = $object->value;
                    $isAttachment = true;
                }
            }
        }

        if ($part->ifparameters) {
            foreach ($part->parameters as $object) {
                if (strtolower($object->attribute) == 'name') {
                    $filename = $object->value;
                    $isAttachment = true;
                }
            }
        }

        $isImage = ($part->type == TYPEIMAGE || ($isAttachment && in_array(strtolower($part->subtype), ['jpeg', 'png', 'gif', 'jpg'])));
        $isIcs = ($isAttachment && strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'ics') || (strtolower($part->subtype) == 'calendar');

        if ($isImage || $isIcs) {
            $data = imap_fetchbody($inbox, $email_number, $partNumber);

            // Decode if needed
            if ($part->encoding == ENCBASE64) {
                $data = base64_decode($data);
            } elseif ($part->encoding == ENCQUOTEDPRINTABLE) {
                $data = quoted_printable_decode($data);
            }

            if ($isImage) {
                self::processImage($data, $filename, $familyId, $email_number, $partNumber, $fromEmail);
            } elseif ($isIcs) {
                self::processIcs($data, $familyId, $fromEmail);
            }
        }
    }

    private static function processImage($imageData, $filename, $familyId, $email_number, $partNumber, $fromEmail = '')
    {
        $uploadDirRel = "../public/uploads/photos";
        $uploadDir = __DIR__ . "/../../../public/uploads/photos";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $filename ?: "email_image_{$email_number}_{$partNumber}.jpg";
        $fileNameUnique = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $dbFilePath = $uploadDirRel . "/" . $fileNameUnique;
        $fullPath = $uploadDir . "/" . $fileNameUnique;

        file_put_contents($fullPath, $imageData);

        $width = null;
        $height = null;
        $info = @getimagesize($fullPath);
        if ($info !== false) {
            $width = $info[0];
            $height = $info[1];
        }

        $metadata = [
            'original_name' => $filename,
            'source' => 'email'
        ];
        if ($width) $metadata['width'] = $width;
        if ($height) $metadata['height'] = $height;

        $fileSize = filesize($fullPath);
        $metadataJson = json_encode($metadata);

        $userId = null;
        if ($fromEmail) {
            $senderUser = Database::runPrepared("SELECT u.id FROM users u JOIN user_family uf ON u.id = uf.user_id WHERE u.email = ? AND uf.family_id = ? LIMIT 1", [$fromEmail, $familyId])->fetch(PDO::FETCH_ASSOC);
            if ($senderUser) {
                $userId = $senderUser['id'];
            }
        }

        Database::runPrepared("INSERT INTO photos(family_id, photo, file_size, metadata, status, uploaded_by) VALUES(?, ?, ?, ?, 'pending', ?)", [
            $familyId,
            $dbFilePath,
            $fileSize,
            $metadataJson,
            $userId
        ]);

        echo "Saved and queued photo: $dbFilePath<br>";
    }

    private static function processIcs($icsData, $familyId, $fromEmail = '')
    {
        if (preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $icsData, $matches)) {
            require_once __DIR__ . '/../../../classes/Event.php';
            foreach ($matches[1] as $eventStr) {
                $title = 'Email Event';
                $description = '';
                $location = '';
                $startTimeStr = '';
                $endTimeStr = '';

                if (preg_match('/SUMMARY:(.*)/i', $eventStr, $m)) $title = trim($m[1]);
                if (preg_match('/DESCRIPTION:(.*)/i', $eventStr, $m)) {
                    $description = trim(str_replace(['\\n', '\\N', '\\,'], ["\n", "\n", ','], $m[1]));
                }
                if (preg_match('/LOCATION:(.*)/i', $eventStr, $m)) $location = trim($m[1]);
                if (preg_match('/DTSTART[A-Z0-9;=]*:(.*)/i', $eventStr, $m)) $startTimeStr = trim($m[1]);
                if (preg_match('/DTEND[A-Z0-9;=]*:(.*)/i', $eventStr, $m)) $endTimeStr = trim($m[1]);

                $startTime = self::parseIcsDate($startTimeStr);
                $endTime = self::parseIcsDate($endTimeStr);

                $isAllDay = 0;
                if (preg_match('/DTSTART[^:]*VALUE=DATE/i', $eventStr) || strlen(trim($startTimeStr)) === 8) {
                    $isAllDay = 1;
                }

                $eventRepeat = null;
                if (preg_match('/RRULE:.*FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)/i', $eventStr, $m)) {
                    $eventRepeat = ucfirst(strtolower(trim($m[1])));
                }

                $remainder = null;
                if (preg_match('/TRIGGER:(.*?)$/im', $eventStr, $m)) {
                    $trigger = strtoupper(trim($m[1]));
                    $mins = 0;
                    if (preg_match('/(\d+)M/', $trigger, $t)) $mins = (int)$t[1];
                    elseif (preg_match('/(\d+)H/', $trigger, $t)) $mins = (int)$t[1] * 60;
                    elseif (preg_match('/(\d+)D/', $trigger, $t)) $mins = (int)$t[1] * 1440;

                    if (in_array((string)$mins, ['5', '15', '30', '60', '1440'])) {
                        $remainder = (string)$mins;
                    }
                }

                if (!$startTime) {
                    $startTime = date('Y-m-d H:i:s');
                }
                if (!$endTime) {
                    $endTime = $startTime;
                }

                $typeId = 1;

                $userId = 0;
                if ($fromEmail) {
                    $senderUser = Database::runPrepared("SELECT u.id FROM users u JOIN user_family uf ON u.id = uf.user_id WHERE u.email = ? AND uf.family_id = ? LIMIT 1", [$fromEmail, $familyId])->fetch(PDO::FETCH_ASSOC);
                    if ($senderUser) {
                        $userId = $senderUser['id'];
                    }
                }

                if (!$userId) {
                    $user = Database::runPrepared("SELECT user_id FROM user_family uf JOIN users u ON uf.user_id = u.id WHERE uf.family_id = ? AND u.role = 'family-head' LIMIT 1", [$familyId])->fetch(PDO::FETCH_ASSOC);
                    $userId = $user ? $user['user_id'] : 0;
                }

                $createdBy = $userId;
                $members = $userId ? [$userId] : [];

                $eventData = [
                    'family_id' => $familyId,
                    'title' => $title,
                    'description' => $description,
                    'type_id' => $typeId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => $location,
                    'is_all_day' => $isAllDay,
                    'event_repeat' => $eventRepeat,
                    'remainder' => $remainder,
                    'created_by' => $createdBy,
                    'members' => $members
                ];

                Event::add($eventData);

                echo "Added ICS Event: $title<br>";
            }
        }
    }

    private static function parseIcsDate($dateStr)
    {
        if (empty($dateStr)) return null;

        $dateStr = str_replace('Z', '', $dateStr);
        if (strlen($dateStr) >= 15) {
            $year = substr($dateStr, 0, 4);
            $month = substr($dateStr, 4, 2);
            $day = substr($dateStr, 6, 2);
            $hour = substr($dateStr, 9, 2);
            $min = substr($dateStr, 11, 2);
            $sec = substr($dateStr, 13, 2);
            return "$year-$month-$day $hour:$min:$sec";
        } elseif (strlen($dateStr) == 8) {
            $year = substr($dateStr, 0, 4);
            $month = substr($dateStr, 4, 2);
            $day = substr($dateStr, 6, 2);
            return "$year-$month-$day 00:00:00";
        }
        return null;
    }
}
