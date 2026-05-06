<?php
require_once __DIR__ . '/../config/Database.php';


class Auth
{
    static function login($email, $password)
    {
        $user = Database::runPrepared("SELECT * FROM users WHERE email=?", [$email])->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user['families'] = Database::runPrepared("SELECT * FROM families INNER JOIN user_family ON families.id = user_family.family_id WHERE user_family.user_id=?", [$user['id']])->fetchAll(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                return ["status" => "success", "data" => $user];
            } else {
                return ["status" => "error", "message" => "Invalid Password."];
            }
        } else {
            return ["status" => "error", "message" => "Invalid Email."];
        }
    }

    static function logout()
    {
        session_destroy();
        return ["status" => "success", "message" => "Logged out successfully."];
    }
}
