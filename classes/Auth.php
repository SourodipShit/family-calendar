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

    static function register($data)
    {
        $user = $data['user'];
        $family = $data['family'];

        $existingUser = Database::runPrepared("SELECT * FROM users WHERE email=?", [$user['email']])->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            return ["status" => "error", "message" => "User already exists."];
        }

        $existingFamily = Database::runPrepared("SELECT * FROM families WHERE email=?", [$family['email']])->fetch(PDO::FETCH_ASSOC);
        if ($existingFamily) {
            return ["status" => "error", "message" => "Family already exists."];
        }

        try {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            $phone = isset($user['phone']) ? $user['phone'] : '';
            $image = isset($user['image']) ? $user['image'] : '';

            Database::runPrepared("INSERT INTO users (name, email, phone, role, password, image) VALUES (?, ?, ?, 'family-head', ?, ?)", 
                [$user['name'], $user['email'], $phone, $user['password'], $image]);
            $lastUserId = Database::getInstance()->lastInsertId();

            if($lastUserId) {
                $familyEmail = isset($family['email']) ? $family['email'] : '';
                $location = isset($family['location']) ? $family['location'] : null;
                $timezone = isset($family['timezone']) ? $family['timezone'] : null;

                Database::runPrepared("INSERT INTO families (name, email, location, timezone) VALUES (?, ?, ?, ?)", 
                    [$family['name'], $familyEmail, $location, $timezone]);
                $lastFamilyId = Database::getInstance()->lastInsertId();

                if(Database::runPrepared("INSERT INTO user_family (user_id, family_id) VALUES (?, ?)", [$lastUserId, $lastFamilyId])) {
                    return ["status" => "success", "message" => "User and family registered successfully."];
                }
                else {
                    return ["status" => "error", "message" => "Failed to link user and family."];
                }
            }
        } catch (PDOException $th) {
            return ["status" => "error", "message" => "Failed to register user and family."];
        }
    }
}
