<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../classes/User.php";
require_once __DIR__ . "/../classes/PasswordResetOTP.php";
require_once __DIR__ . "/../services/mail/Mail.php";

$action = $_GET['action'];

switch ($action) {
    case 'find_user':
        $email = $_POST['email'];
        $user = User::findByEmail($email);
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }
        $_SESSION['reset_user'] = $user;
        echo json_encode(['status' => 'success', 'data' => $user]);
        break;
    case 'set_otp':
        $user_id = $_POST['user_id'];
        $otp = PasswordResetOTP::setotp($user_id);
        if ($otp['status'] === 'error') {
            echo json_encode($otp);
            exit;
        }

        $user = User::getUserById($user_id);
        if ($user) {
            Mail::passwordReset($user, $otp['data']['otp']);
        }

        echo json_encode(['status' => 'success', 'data' => $otp['data']]);
        break;
    case 'verify_otp':
        $user_id = $_POST['user_id'];
        $otp = $_POST['otp'];
        $otp = PasswordResetOTP::verify_otp($user_id, $otp);
        if ($otp['status'] === 'error') {
            echo json_encode($otp);
            exit;
        }
        echo json_encode(['status' => 'success', 'data' => $otp['data']]);
        break;
    case 'reset_password':
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
            exit;
        }

        $user = User::getUserById($user_id);
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $updateResult = User::updateUser($user_id, ['password' => $hashed_password]);

        if ($updateResult['status'] === 'error') {
            echo json_encode($updateResult);
            exit;
        }
        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully']);
        break;
}
