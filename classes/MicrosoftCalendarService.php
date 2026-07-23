<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/GlobalSettings.php";

class MicrosoftCalendarService
{
    private static function getSetting($key)
    {
        $res = GlobalSettings::getSetting($key);
        if ($res['status'] === 'success' && !empty($res['data'])) {
            return $res['data']['setting_value'];
        }
        return null;
    }

    public static function getAuthUrl()
    {
        $clientId = self::getSetting('ms_client_id');
        $redirectUri = self::getSetting('ms_redirect_uri');

        if (!$clientId || !$redirectUri) {
            return null;
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'offline_access Calendars.ReadWrite',
            'prompt' => 'select_account'
        ];

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query($params);
    }

    public static function authenticate($code, $userId)
    {
        $clientId = self::getSetting('ms_client_id');
        $clientSecret = self::getSetting('ms_client_secret');
        $redirectUri = self::getSetting('ms_redirect_uri');

        $ch = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['access_token'])) {
            $accessToken = $data['access_token'];
            $refreshToken = $data['refresh_token'] ?? null;
            $expiresIn = $data['expires_in'];
            $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

            try {
                $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
                $settings = [];
                if ($user && $user['settings']) {
                    $settings = json_decode($user['settings'], true);
                }

                $settings['ms_calendar_tokens'] = [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresAt
                ];

                Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [json_encode($settings), $userId]);
                
                return ["status" => "success"];
            } catch (Exception $e) {
                return ["status" => "error", "message" => $e->getMessage()];
            }
        }

        return ["status" => "error", "message" => "Failed to get access token.", "debug" => $response];
    }

    public static function disconnect($userId)
    {
        try {
            $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            if ($user && $user['settings']) {
                $settings = json_decode($user['settings'], true);
                if (isset($settings['ms_calendar_tokens'])) {
                    unset($settings['ms_calendar_tokens']);
                    Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [json_encode($settings), $userId]);
                }
            }

            Database::runPrepared("DELETE FROM event_sync_mappings WHERE google_event_id LIKE 'ms_%' AND local_event_id IN (SELECT id FROM events WHERE family_id = (SELECT family_id FROM users WHERE id = ?))", [$userId]);
            
            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function isConnected($userId)
    {
        try {
            $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            if ($user && $user['settings']) {
                $settings = json_decode($user['settings'], true);
                return isset($settings['ms_calendar_tokens']['access_token']);
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function getValidAccessToken($userId)
    {
        $user = Database::runPrepared("SELECT settings FROM users WHERE id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
        if (!$user || !$user['settings']) {
            return null;
        }

        $settings = json_decode($user['settings'], true);
        if (!isset($settings['ms_calendar_tokens'])) {
            return null;
        }

        $tokenData = $settings['ms_calendar_tokens'];

        if (strtotime($tokenData['expires_at']) < time() + 300) { 
            if (empty($tokenData['refresh_token'])) {
                return null;
            }

            $clientId = self::getSetting('ms_client_id');
            $clientSecret = self::getSetting('ms_client_secret');

            $ch = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $tokenData['refresh_token'],
                'grant_type' => 'refresh_token'
            ]));

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['access_token'])) {
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'];
                $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

                $settings['ms_calendar_tokens']['access_token'] = $accessToken;
                $settings['ms_calendar_tokens']['expires_at'] = $expiresAt;
                if (isset($data['refresh_token'])) {
                    $settings['ms_calendar_tokens']['refresh_token'] = $data['refresh_token'];
                }

                Database::runPrepared("UPDATE users SET settings = ? WHERE id = ?", [json_encode($settings), $userId]);

                return $accessToken;
            }
            return null;
        }

        return $tokenData['access_token'];
    }

    public static function pushEvent($userId, $localEventId, $eventData)
    {
        $accessToken = self::getValidAccessToken($userId);
        if (!$accessToken) return ["status" => "error", "message" => "Not authenticated"];

        $mapping = Database::runPrepared("SELECT google_event_id FROM event_sync_mappings WHERE local_event_id = ? AND google_event_id LIKE 'ms_%'", [$localEventId])->fetch(PDO::FETCH_ASSOC);
        
        $url = 'https://graph.microsoft.com/v1.0/me/events';
        $method = 'POST';

        if ($mapping) {
            $msEventId = str_replace('ms_', '', $mapping['google_event_id']);
            $url .= '/' . $msEventId;
            $method = 'PATCH';
        }

        $payload = [
            'subject' => $eventData['title'],
            'body' => [
                'contentType' => 'HTML',
                'content' => $eventData['description'] ?? ''
            ],
            'location' => [
                'displayName' => $eventData['location'] ?? ''
            ],
        ];

        if (!empty($eventData['is_all_day'])) {
            $payload['isAllDay'] = true;
            $payload['start'] = ['dateTime' => explode(' ', $eventData['start_time'])[0], 'timeZone' => 'UTC'];
            $payload['end'] = ['dateTime' => date('Y-m-d', strtotime(explode(' ', $eventData['end_time'])[0] . ' +1 day')), 'timeZone' => 'UTC'];
        } else {
            $payload['isAllDay'] = false;
            $payload['start'] = ['dateTime' => str_replace(' ', 'T', $eventData['start_time']), 'timeZone' => $eventData['timezone'] ?? 'UTC'];
            $payload['end'] = ['dateTime' => str_replace(' ', 'T', $eventData['end_time']), 'timeZone' => $eventData['timezone'] ?? 'UTC'];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $resData = json_decode($response, true);

        if (isset($resData['id']) && !$mapping) {
            Database::runPrepared(
                "INSERT INTO event_sync_mappings (local_event_id, google_event_id) VALUES (?, ?)",
                [$localEventId, 'ms_' . $resData['id']]
            );
        }

        return ["status" => "success", "ms_event_id" => $resData['id'] ?? null];
    }

    public static function deleteEvent($userId, $localEventId)
    {
        $accessToken = self::getValidAccessToken($userId);
        if (!$accessToken) return ["status" => "error", "message" => "Not authenticated"];

        $mapping = Database::runPrepared("SELECT google_event_id FROM event_sync_mappings WHERE local_event_id = ? AND google_event_id LIKE 'ms_%'", [$localEventId])->fetch(PDO::FETCH_ASSOC);
        
        if ($mapping) {
            $msEventId = str_replace('ms_', '', $mapping['google_event_id']);
            $ch = curl_init('https://graph.microsoft.com/v1.0/me/events/' . $msEventId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken
            ]);
            curl_exec($ch);
            curl_close($ch);

            Database::runPrepared("DELETE FROM event_sync_mappings WHERE local_event_id = ? AND google_event_id = ?", [$localEventId, $mapping['google_event_id']]);
        }

        return ["status" => "success"];
    }

    public static function pullEvents($userId)
    {
        $accessToken = self::getValidAccessToken($userId);
        if (!$accessToken) return ["status" => "error", "message" => "Not authenticated"];

        $startDateTime = date('Y-m-d\TH:i:s\Z', strtotime('-1 month'));
        $url = 'https://graph.microsoft.com/v1.0/me/calendarView?startDateTime=' . $startDateTime . '&endDateTime=' . date('Y-m-d\TH:i:s\Z', strtotime('+1 year'));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        
        if (!isset($data['value'])) {
            return ["status" => "error", "message" => "Failed to fetch events from Microsoft"];
        }

        try {
            $familyId = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ? LIMIT 1", [$userId])->fetchColumn();
            $defaultTypeId = Database::runPrepared("SELECT id FROM event_types WHERE family_id IS NULL OR family_id = ? LIMIT 1", [$familyId])->fetchColumn();

            foreach ($data['value'] as $item) {
                if (isset($item['isCancelled']) && $item['isCancelled']) continue;
                
                $msId = 'ms_' . $item['id'];
                $title = $item['subject'] ?? 'Busy';
                $description = $item['bodyPreview'] ?? '';
                $location = $item['location']['displayName'] ?? '';
                
                $isAllDay = $item['isAllDay'] ? 1 : 0;
                
                if ($isAllDay) {
                    $startTime = date('Y-m-d H:i:s', strtotime($item['start']['dateTime']));
                    $endTime = date('Y-m-d H:i:s', strtotime($item['end']['dateTime'] . ' -1 day')); 
                } else {
                    $startTime = date('Y-m-d H:i:s', strtotime($item['start']['dateTime']));
                    $endTime = date('Y-m-d H:i:s', strtotime($item['end']['dateTime']));
                }

                $mapping = Database::runPrepared("SELECT local_event_id FROM event_sync_mappings WHERE google_event_id = ?", [$msId])->fetch(PDO::FETCH_ASSOC);

                if ($mapping) {
                    $localId = $mapping['local_event_id'];
                    Database::runPrepared(
                        "UPDATE events SET title=?, description=?, start_time=?, end_time=?, location=?, is_all_day=? WHERE id=?",
                        [$title, $description, $startTime, $endTime, $location, $isAllDay, $localId]
                    );
                } else {
                    Database::runPrepared(
                        "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$familyId, $title, $description, $defaultTypeId, $startTime, $endTime, $location, $isAllDay, $userId]
                    );
                    $localId = Database::getInstance()->lastInsertId();
                    
                    Database::runPrepared(
                        "INSERT INTO event_sync_mappings (local_event_id, google_event_id) VALUES (?, ?)",
                        [$localId, $msId]
                    );
                    
                    Database::runPrepared("INSERT INTO event_members (event_id, user_id) VALUES (?, ?)", [$localId, $userId]);
                }
            }

            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}
