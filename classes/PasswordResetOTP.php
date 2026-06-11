<?php
require_once __DIR__ . '/../config/Database.php';

class PasswordResetOTP
{

    public static function generateRandomOTP()
    {
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function findByUser($userId)
    {
        $sql = "SELECT * FROM password_reset_requests WHERE user_id = ?";
        $results = Database::runPrepared($sql, [$userId])->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            return [
                'status' => 'success',
                'data' => $results[0]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'No OTP found for user'
            ];
        }
    }

    public static function setotp($userId)
    {

        $otp = self::generateRandomOTP();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        try {
            $entry = self::findByUser($userId);
            if ($entry['status'] !== 'error') {
                $sql = "UPDATE password_reset_requests SET otp = ?, expires_at = ? WHERE user_id = ?";
                Database::runPrepared($sql, [$otp, $expiresAt, $userId]);
            } else {
                $sql = "INSERT INTO password_reset_requests (user_id, otp, expires_at, created_at) VALUES (?, ?, ?, NOW())";
                Database::runPrepared($sql, [$userId, $otp, $expiresAt]);
            }
            return [
                'status' => 'success',
                'data' => [
                    'otp' => $otp,
                    'expires_at' => $expiresAt
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function verify_otp($userId, $otp)
    {
        $entry = self::findByUser($userId);
        if ($entry['status'] === 'error') {
            return $entry;
        }

        if ($entry['data']['otp'] !== $otp) {
            return [
                'status' => 'error',
                'message' => 'Invalid OTP'
            ];
        }

        if (strtotime($entry['data']['expires_at']) < time()) {
            return [
                'status' => 'error',
                'message' => 'OTP has expired'
            ];
        }

        return [
            'status' => 'success',
            'data' => $entry['data']
        ];
    }
}
