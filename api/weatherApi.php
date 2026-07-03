<?php

/**
 * API Endpoint: Get Current Weather
 * 
 * Returns weather data (current temp, high/low, conditions, and icon) 
 * for the user's auto-detected IP location or provided coordinates.
 */

header('Content-Type: application/json');

// Check if latitude and longitude are provided via GET, otherwise use IP auto-detect
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $latitude = (float)$_GET['lat'];
    $longitude = (float)$_GET['lon'];
    $locationName = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : 'Custom Location';
    $timezone = 'auto'; // Let open-meteo auto-resolve timezone based on lat/lon
} else {
    // 1. Get current location based on user's IP address (instead of server's IP)
    $clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $clientIp = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }

    // If local, don't pass IP so ip-api uses the server's public IP for testing
    $ipQuery = '';
    if ($clientIp && !in_array($clientIp, ['127.0.0.1', '::1'])) {
        $ipQuery = $clientIp;
    }

    $ipApiUrl = "http://ip-api.com/json/" . $ipQuery;

    // Suppress errors if API is unreachable
    $ipResponse = @file_get_contents($ipApiUrl);

    if (!$ipResponse) {
        echo json_encode(['error' => 'Failed to auto-detect location.']);
        exit;
    }

    $ipData = json_decode($ipResponse, true);

    if (isset($ipData['status']) && $ipData['status'] !== 'success') {
        echo json_encode(['error' => 'Error from IP API: ' . $ipData['message']]);
        exit;
    }

    $latitude = $ipData['lat'];
    $longitude = $ipData['lon'];
    $locationName = $ipData['city'] . ', ' . $ipData['region'];
    $timezone = urlencode($ipData['timezone']);
}

// Function to map WMO weather codes to text descriptions and OpenWeatherMap PNG icon URLs
function getWeatherDetails($code, $isDay)
{
    $dayNight = $isDay ? 'd' : 'n'; // 'd' for day, 'n' for night

    // Map WMO codes to OpenWeatherMap icon prefixes
    $iconMap = [
        0 => '01',
        1 => '02',
        2 => '02',
        3 => '04',
        45 => '50',
        48 => '50',
        51 => '09',
        53 => '09',
        55 => '09',
        61 => '10',
        63 => '10',
        65 => '10',
        71 => '13',
        73 => '13',
        75 => '13',
        77 => '13',
        80 => '09',
        81 => '09',
        82 => '09',
        85 => '13',
        86 => '13',
        95 => '11',
        96 => '11',
        99 => '11',
    ];

    $textMap = [
        0 => 'Clear sky',
        1 => 'Mainly clear',
        2 => 'Partly cloudy',
        3 => 'Overcast',
        45 => 'Fog',
        48 => 'Depositing rime fog',
        51 => 'Light drizzle',
        53 => 'Moderate drizzle',
        55 => 'Dense drizzle',
        61 => 'Slight rain',
        63 => 'Moderate rain',
        65 => 'Heavy rain',
        71 => 'Slight snow fall',
        73 => 'Moderate snow fall',
        75 => 'Heavy snow fall',
        77 => 'Snow grains',
        80 => 'Slight rain showers',
        81 => 'Moderate rain showers',
        82 => 'Violent rain showers',
        85 => 'Slight snow showers',
        86 => 'Heavy snow showers',
        95 => 'Thunderstorm',
        96 => 'Thunderstorm with slight hail',
        99 => 'Thunderstorm with heavy hail',
    ];

    $iconPrefix = isset($iconMap[$code]) ? $iconMap[$code] : '01';
    $text = isset($textMap[$code]) ? $textMap[$code] : 'Unknown';

    // Construct the public icon URL (using standard 2x size from OpenWeatherMap)
    $iconUrl = "https://openweathermap.org/img/wn/{$iconPrefix}{$dayNight}@2x.png";

    return ['text' => $text, 'icon_url' => $iconUrl];
}

// 2. Open-Meteo API URL with daily max/min temps, Celsius (default)
$apiUrl = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&daily=temperature_2m_max,temperature_2m_min&timezone={$timezone}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout
curl_setopt($ch, CURLOPT_USERAGENT, 'FamilyCalendarApp/1.0 (Contact: admin@ascinate.in)'); // Add User-Agent

$response = curl_exec($ch);
$curlError = curl_error($ch);

if ($curlError) {
    echo json_encode(['error' => 'Weather API Request Error: ' . $curlError]);
    exit;
}

$data = json_decode($response, true);

if (isset($data['error']) && $data['error'] == true) {
    echo json_encode(['error' => 'Open-Meteo API Error: ' . (isset($data['reason']) ? $data['reason'] : 'Unknown error')]);
    exit;
}

// Get weather details including icon URL
$isDay = isset($data['current_weather']['is_day']) ? $data['current_weather']['is_day'] : 1;
$weatherDetails = getWeatherDetails($data['current_weather']['weathercode'], $isDay);

// Extract only the details we want
$widgetData = [
    'location' => $locationName,
    'current_temperature' => round($data['current_weather']['temperature']) . '°C',
    'condition_icon_url' => $weatherDetails['icon_url'],
    'condition_text' => $weatherDetails['text'],
    'high_temperature' => 'H: ' . round($data['daily']['temperature_2m_max'][0]) . '°',
    'low_temperature' => 'L: ' . round($data['daily']['temperature_2m_min'][0]) . '°',
];

// Return JSON
echo json_encode($widgetData);
