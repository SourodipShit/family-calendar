<?php

require_once __DIR__ . "/../config/Database.php";

class LoginLogs
{

    public static function track($user_id)
    {
        $data = self::prepareLoginData();

        $sql = "INSERT INTO login_logs (user_id, device, browser, os, ip_address, location, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $user_id,
            $data['device'],
            $data['browser'],
            $data['os'],
            $data['ip_address'],
            $data['location'],
            $data['address']
        ];

        Database::runPrepared($sql, $params);
        return true;
    }

    public static function getLastLogins($limit = 5)
    {
        $limit = (int)$limit;
        $sql = "SELECT 
                    l.*, 
                    MAX(u.name) AS user_name, 
                    GROUP_CONCAT(f.name SEPARATOR ', ') AS family_name 
                FROM login_logs l
                LEFT JOIN users u ON l.user_id = u.id
                LEFT JOIN user_family uf ON u.id = uf.user_id
                LEFT JOIN families f ON uf.family_id = f.id
                GROUP BY l.id
                ORDER BY l.login_time DESC 
                LIMIT {$limit}";

        return Database::runPrepared($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function prepareLoginData()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        // Determine OS
        $os = "Unknown OS";
        $osArray = [
            '/windows/i'            =>  'Windows',
            '/macintosh|mac os x/i' =>  'Mac OS',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android'
        ];

        foreach ($osArray as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $os = $value;
                break;
            }
        }

        // Determine Browser
        $browser = "Unknown Browser";
        $browserArray = [
            '/edg/i'       => 'Edge',
            '/edge/i'      => 'Edge',
            '/opr/i'       => 'Opera',
            '/opera/i'     => 'Opera',
            '/firefox/i'   => 'Firefox',
            '/chrome/i'    => 'Chrome',
            '/safari/i'    => 'Safari'
        ];

        foreach ($browserArray as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $browser = $value;
                break;
            }
        }

        // Determine Device
        $device = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            $device = 'Tablet';
        } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            $device = 'Mobile';
        }

        // Determine IP Address
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

        // If on localhost, fetch the actual public IP to get real location data
        if ($ip_address === '127.0.0.1' || $ip_address === '::1') {
            $public_ip = @file_get_contents('https://api.ipify.org');
            if ($public_ip) $ip_address = $public_ip;
        }

        $location = null;
        $address = null;

        // Fetch location data (simplified using file_get_contents)
        $apiURL = "http://ip-api.com/json/{$ip_address}";
        $ctx = stream_context_create(['http' => ['timeout' => 3]]);
        $response = @file_get_contents($apiURL, false, $ctx);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['status']) && $data['status'] === 'success') {
                $lat = $data['lat'] ?? '';
                $lon = $data['lon'] ?? '';
                $location = trim($lat . ' ' . $lon);

                $city = $data['city'] ?? '';
                $region = $data['regionName'] ?? '';
                $country = $data['country'] ?? '';

                $addressParts = array_filter([$city, $region, $country]);
                $address = implode(', ', $addressParts);
            }
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
            'ip_address' => $ip_address,
            'location' => $location,
            'address' => $address
        ];
    }
}
