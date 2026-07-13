<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/GlobalSettings.php";

class GoogleCalendarService
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
        $clientId = self::getSetting('google_client_id');
        $redirectUri = self::getSetting('google_redirect_uri');

        if (!$clientId || !$redirectUri) {
            return null;
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }

    public static function authenticate($code, $userId)
    {
        $clientId = self::getSetting('google_client_id');
        $clientSecret = self::getSetting('google_client_secret');
        $redirectUri = self::getSetting('google_redirect_uri');

        $ch = curl_init('https://oauth2.googleapis.com/token');
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

            // Save to database
            try {
                // Check if user already has tokens
                $existing = Database::runPrepared("SELECT id FROM google_oauth_tokens WHERE user_id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $sql = "UPDATE google_oauth_tokens SET access_token = ?, expires_at = ? " . ($refreshToken ? ", refresh_token = '$refreshToken'" : "") . " WHERE user_id = ?";
                    Database::runPrepared($sql, [$accessToken, $expiresAt, $userId]);
                } else {
                    Database::runPrepared(
                        "INSERT INTO google_oauth_tokens (user_id, access_token, refresh_token, expires_at) VALUES (?, ?, ?, ?)",
                        [$userId, $accessToken, $refreshToken, $expiresAt]
                    );
                }
                return ["status" => "success"];
            } catch (Exception $e) {
                return ["status" => "error", "message" => $e->getMessage()];
            }
        }

        return ["status" => "error", "message" => "Failed to get access token."];
    }

    public static function disconnect($userId)
    {
        try {
            Database::runPrepared("DELETE FROM google_oauth_tokens WHERE user_id = ?", [$userId]);
            Database::runPrepared("DELETE FROM event_sync_mappings WHERE local_event_id IN (SELECT id FROM events WHERE family_id = (SELECT family_id FROM users WHERE id = ?))", [$userId]);
            return ["status" => "success"];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public static function isConnected($userId)
    {
        try {
            $token = Database::runPrepared("SELECT id FROM google_oauth_tokens WHERE user_id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);
            return !empty($token);
        } catch (Exception $e) {
            return false;
        }
    }

    private static function getValidAccessToken($userId)
    {
        $tokenData = Database::runPrepared("SELECT access_token, refresh_token, expires_at FROM google_oauth_tokens WHERE user_id = ?", [$userId])->fetch(PDO::FETCH_ASSOC);

        if (!$tokenData) {
            return null;
        }

        if (strtotime($tokenData['expires_at']) < time() + 300) { // Refresh if expiring within 5 minutes
            if (empty($tokenData['refresh_token'])) {
                return null;
            }

            $clientId = self::getSetting('google_client_id');
            $clientSecret = self::getSetting('google_client_secret');

            $ch = curl_init('https://oauth2.googleapis.com/token');
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

                Database::runPrepared(
                    "UPDATE google_oauth_tokens SET access_token = ?, expires_at = ? WHERE user_id = ?",
                    [$accessToken, $expiresAt, $userId]
                );

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

        $mapping = Database::runPrepared("SELECT google_event_id FROM event_sync_mappings WHERE local_event_id = ?", [$localEventId])->fetch(PDO::FETCH_ASSOC);
        
        $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';
        $method = 'POST';

        if ($mapping) {
            $url .= '/' . $mapping['google_event_id'];
            $method = 'PUT';
        }

        $payload = [
            'summary' => $eventData['title'],
            'description' => $eventData['description'] ?? '',
            'location' => $eventData['location'] ?? '',
        ];

        if (!empty($eventData['is_all_day'])) {
            $payload['start'] = ['date' => explode(' ', $eventData['start_time'])[0]];
            // Google expects end date to be exclusive for all-day events
            $payload['end'] = ['date' => date('Y-m-d', strtotime(explode(' ', $eventData['end_time'])[0] . ' +1 day'))];
        } else {
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
                [$localEventId, $resData['id']]
            );
        }

        return ["status" => "success", "google_event_id" => $resData['id'] ?? null];
    }

    public static function deleteEvent($userId, $localEventId)
    {
        $accessToken = self::getValidAccessToken($userId);
        if (!$accessToken) return ["status" => "error", "message" => "Not authenticated"];

        $mapping = Database::runPrepared("SELECT google_event_id FROM event_sync_mappings WHERE local_event_id = ?", [$localEventId])->fetch(PDO::FETCH_ASSOC);
        
        if ($mapping) {
            $ch = curl_init('https://www.googleapis.com/calendar/v3/calendars/primary/events/' . $mapping['google_event_id']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken
            ]);
            curl_exec($ch);
            curl_close($ch);

            Database::runPrepared("DELETE FROM event_sync_mappings WHERE local_event_id = ?", [$localEventId]);
        }

        return ["status" => "success"];
    }

    public static function pullEvents($userId)
    {
        $accessToken = self::getValidAccessToken($userId);
        if (!$accessToken) return ["status" => "error", "message" => "Not authenticated"];

        $ch = curl_init('https://www.googleapis.com/calendar/v3/calendars/primary/events?timeMin=' . urlencode(date('c', strtotime('-1 month'))) . '&singleEvents=true&orderBy=startTime');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        
        if (!isset($data['items'])) {
            return ["status" => "error", "message" => "Failed to fetch events from Google"];
        }

        try {
            $familyId = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ? LIMIT 1", [$userId])->fetchColumn();
            $defaultTypeId = Database::runPrepared("SELECT id FROM event_types WHERE family_id IS NULL OR family_id = ? LIMIT 1", [$familyId])->fetchColumn();

            foreach ($data['items'] as $item) {
                if ($item['status'] === 'cancelled') continue;
                
                $googleId = $item['id'];
                $title = $item['summary'] ?? 'Busy';
                $description = $item['description'] ?? '';
                $location = $item['location'] ?? '';
                
                $isAllDay = isset($item['start']['date']) ? 1 : 0;
                if ($isAllDay) {
                    $startTime = $item['start']['date'] . ' 00:00:00';
                    $endTime = date('Y-m-d H:i:s', strtotime($item['end']['date'] . ' -1 day')); // Google end date is exclusive
                } else {
                    $startTime = date('Y-m-d H:i:s', strtotime($item['start']['dateTime']));
                    $endTime = date('Y-m-d H:i:s', strtotime($item['end']['dateTime']));
                }

                $mapping = Database::runPrepared("SELECT local_event_id FROM event_sync_mappings WHERE google_event_id = ?", [$googleId])->fetch(PDO::FETCH_ASSOC);

                if ($mapping) {
                    // Update local event
                    $localId = $mapping['local_event_id'];
                    Database::runPrepared(
                        "UPDATE events SET title=?, description=?, start_time=?, end_time=?, location=?, is_all_day=? WHERE id=?",
                        [$title, $description, $startTime, $endTime, $location, $isAllDay, $localId]
                    );
                } else {
                    // Insert new local event
                    Database::runPrepared(
                        "INSERT INTO events (family_id, title, description, type_id, start_time, end_time, location, is_all_day, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$familyId, $title, $description, $defaultTypeId, $startTime, $endTime, $location, $isAllDay, $userId]
                    );
                    $localId = Database::getInstance()->lastInsertId();
                    
                    Database::runPrepared(
                        "INSERT INTO event_sync_mappings (local_event_id, google_event_id) VALUES (?, ?)",
                        [$localId, $googleId]
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
