<?php

require_once __DIR__ . '/../../../classes/GlobalSettings.php';

class InfobipProvider
{
    private static $sender = "InfoSMS";

    public static function send($to, $message)
    {
        $nl = (php_sapi_name() === 'cli') ? "\n" : "<br>\n";
        try {
            echo $nl . "[DEBUG] Infobip SMS: Starting send flow..." . $nl;

            // Fetch API Key dynamically
            $apiKeyRes = GlobalSettings::getSetting('infobip_api_key');
            $apiKey = ($apiKeyRes['status'] === 'success' && !empty($apiKeyRes['data']['setting_value'])) 
                ? $apiKeyRes['data']['setting_value'] 
                : '';

            // Fetch Base URL dynamically
            $baseUrlRes = GlobalSettings::getSetting('infobip_api_base_url');
            $baseUrl = ($baseUrlRes['status'] === 'success' && !empty($baseUrlRes['data']['setting_value'])) 
                ? rtrim($baseUrlRes['data']['setting_value'], '/') 
                : '';

            // Fetch Sender dynamically
            $senderRes = GlobalSettings::getSetting('infobip_sender');
            $sender = ($senderRes['status'] === 'success' && !empty($senderRes['data']['setting_value'])) 
                ? $senderRes['data']['setting_value'] 
                : self::$sender;

            if (!empty($baseUrl) && strpos($baseUrl, 'http://') !== 0 && strpos($baseUrl, 'https://') !== 0) {
                $baseUrl = 'https://' . $baseUrl;
            }

            $obscuredKey = !empty($apiKey) ? (substr($apiKey, 0, 10) . '...' . substr($apiKey, -4)) : 'EMPTY';
            echo "[DEBUG] Infobip SMS: API Key = " . $obscuredKey . $nl;
            echo "[DEBUG] Infobip SMS: Base URL = " . (!empty($baseUrl) ? $baseUrl : 'EMPTY') . $nl;
            echo "[DEBUG] Infobip SMS: Sender ID = " . $sender . $nl;

            if (empty($apiKey) || empty($baseUrl)) {
                echo "[DEBUG] Infobip SMS: Error - missing API Key or Base URL." . $nl;
                return [
                    'success' => false,
                    'error' => 'Infobip provider is not configured properly. Missing API Key or Base URL in settings.'
                ];
            }

            // Add 'App ' prefix if it isn't already included
            if (strpos($apiKey, 'App ') !== 0) {
                $apiKey = 'App ' . $apiKey;
            }

            $url = $baseUrl . "/sms/3/messages";
            echo "[DEBUG] Infobip SMS: Request URL = " . $url . $nl;

            $payload = [
                "messages" => [
                    [
                        "sender" => $sender,
                        "destinations" => [
                            [
                                "to" => $to
                            ]
                        ],
                        "content" => [
                            "text" => $message
                        ]
                    ]
                ]
            ];

            $payloadJson = json_encode($payload);
            echo "[DEBUG] Infobip SMS: Payload = " . $payloadJson . $nl;

            $response = false;
            $httpCode = 0;

            if (!function_exists('curl_init')) {
                echo "[DEBUG] Infobip SMS: cURL is not available. Falling back to native PHP streams (file_get_contents)..." . $nl;

                $options = [
                    'http' => [
                        'method'  => 'POST',
                        'header'  => [
                            "Authorization: " . $apiKey,
                            "Content-Type: application/json",
                            "Accept: application/json"
                        ],
                        'content' => $payloadJson,
                        'ignore_errors' => true,
                        'timeout' => 10
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ]
                ];

                $context = stream_context_create($options);
                $response = @file_get_contents($url, false, $context);

                if (isset($http_response_header) && is_array($http_response_header)) {
                    foreach ($http_response_header as $header) {
                        if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches)) {
                            $httpCode = intval($matches[1]);
                            break;
                        }
                    }
                }

                echo "[DEBUG] Infobip SMS: HTTP Status Code = " . $httpCode . $nl;
                echo "[DEBUG] Infobip SMS: Raw Response = " . ($response ? $response : 'EMPTY RESPONSE') . $nl . $nl;

                if ($response === false) {
                    $errorMsg = error_get_last();
                    return [
                        'success' => false,
                        'error' => 'HTTP request failed: ' . ($errorMsg ? $errorMsg['message'] : 'Unknown error')
                    ];
                }
            } else {
                echo "[DEBUG] Infobip SMS: Initiating cURL request..." . $nl;
                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_HTTPHEADER => [
                        "Authorization: " . $apiKey,
                        "Content-Type: application/json",
                        "Accept: application/json"
                    ],
                    CURLOPT_POSTFIELDS => $payloadJson
                ]);

                $response = curl_exec($ch);
                $error = curl_error($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                echo "[DEBUG] Infobip SMS: HTTP Status Code = " . $httpCode . $nl;
                echo "[DEBUG] Infobip SMS: cURL Error = " . ($error ? $error : 'NONE') . $nl;
                echo "[DEBUG] Infobip SMS: Raw Response = " . ($response ? $response : 'EMPTY RESPONSE') . $nl . $nl;

                if ($error) {
                    return [
                        'success' => false,
                        'error' => $error
                    ];
                }
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'response' => json_decode($response, true)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Infobip API returned error status: ' . $httpCode . '. Response: ' . $response
                ];
            }
        } catch (Throwable $e) {
            echo "[DEBUG] Infobip SMS: Exception caught = " . $e->getMessage() . $nl . $nl;
            return [
                'success' => false,
                'error' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }
}
