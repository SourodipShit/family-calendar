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

    static function logout($logout_all = false)
    {
        if ($logout_all || !isset($_SESSION['accounts'])) {
            session_destroy();
        } else {
            $active_id = $_SESSION['active_account_id'] ?? null;
            if ($active_id && isset($_SESSION['accounts'][$active_id])) {
                unset($_SESSION['accounts'][$active_id]);
                if (!empty($_SESSION['accounts'])) {
                    // Set active to the first available account
                    $_SESSION['active_account_id'] = array_key_first($_SESSION['accounts']);
                    $_SESSION['user'] = $_SESSION['accounts'][$_SESSION['active_account_id']];
                    return ["status" => "switched", "message" => "Logged out of current account."];
                }
            }
            session_destroy();
        }
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

        try {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            $phone = isset($user['phone']) ? $user['phone'] : '';
            $image = isset($user['image']) ? $user['image'] : '';

            Database::runPrepared("INSERT INTO users (name, email, phone, role, password, image) VALUES (?, ?, ?, 'family-head', ?, ?)", 
                [$user['name'], $user['email'], $phone, $user['password'], $image]);
            $lastUserId = Database::getInstance()->lastInsertId();

            if($lastUserId) {
                $timezone = isset($family['timezone']) ? $family['timezone'] : null;
                $location = isset($family['location']) ? $family['location'] : null;

                Database::runPrepared("INSERT INTO families (name, email, location, timezone) VALUES (?, NULL, ?, ?)", 
                    [$family['name'], $location, $timezone]);
                $lastFamilyId = Database::getInstance()->lastInsertId();

                // Allocate family shared email
                require_once __DIR__ . '/SharedEmails.php';
                SharedEmails::allocateFamily($lastFamilyId);
                
                $allocatedData = SharedEmails::getFamilyEmail($lastFamilyId);
                $allocatedEmail = ($allocatedData && $allocatedData['status'] === 'success') ? $allocatedData['email'] : null;
                
                if ($allocatedEmail) {
                    Database::runPrepared("UPDATE families SET email = ? WHERE id = ?", [$allocatedEmail, $lastFamilyId]);
                }
                
                // Send signup success email
                require_once __DIR__ . '/../services/mail/Mail.php';
                Mail::sendSignupSuccess($user['email'], $user['name'], $allocatedEmail);

                if(Database::runPrepared("INSERT INTO user_family (user_id, family_id) VALUES (?, ?)", [$lastUserId, $lastFamilyId])) {
                    return ["status" => "success", "message" => "User and family registered successfully."];
                }
                else {
                    return ["status" => "error", "message" => "Failed to link user and family."];
                }
            }
        } catch (PDOException $th) {
            return ["status" => "error", "message" => "Failed to register user and family. " . $th->getMessage()];
        }
    }
}
