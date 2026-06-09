<?php

class Mailer
{
    public static function send($data)
    {
        $boundary = md5(time());

        $headers = [];

        require_once __DIR__ . '/../../classes/GlobalSettings.php';
        $mailFromRes = GlobalSettings::getSetting('mail_from_address');
        $mailFrom = 'info@mycompanycalendar.com'; // Default
        if ($mailFromRes['status'] === 'success' && !empty($mailFromRes['data']['setting_value'])) {
            $mailFrom = $mailFromRes['data']['setting_value'];
        }

        $headers[] = "MIME-Version: 1.0";
        $headers[] = "From: Family Calendar <{$mailFrom}>";
        $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";

        $message = "";

        // HTML PART
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $data['html'] . "\r\n";

        // ATTACHMENTS
        if (!empty($data['attachments'])) {

            foreach ($data['attachments'] as $file) {

                $message .= "--{$boundary}\r\n";

                $message .= "Content-Type: {$file['type']}; name=\"{$file['name']}\"\r\n";

                $message .= "Content-Disposition: attachment; filename=\"{$file['name']}\"\r\n";

                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";

                $message .= chunk_split(
                    base64_encode($file['content'])
                );
            }
        }

        $message .= "--{$boundary}--";

        return mail(
            $data['to'],
            $data['subject'],
            $message,
            implode("\r\n", $headers)
        );
    }

    public static function render($template, $data = [])
    {
        require_once __DIR__ . '/../../classes/GlobalSettings.php';
        $baseUrlData = GlobalSettings::getSetting('base_url');
        $data['baseUrl'] = (!empty($baseUrlData['data']) && !empty($baseUrlData['data']['setting_value'])) ? rtrim($baseUrlData['data']['setting_value'], '/') : '#';

        extract($data);

        ob_start();

        include __DIR__ . "/templates/{$template}.php";

        $content = ob_get_clean();

        ob_start();

        include __DIR__ . "/layouts/base.php";

        return ob_get_clean();
    }
}
