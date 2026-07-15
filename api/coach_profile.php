<?php
session_start();
require_once __DIR__ . '/../classes/Coach.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/File.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'coach') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        try {
            $coachData = Coach::getByUserId($userId);
            if ($coachData['status'] !== 'success') {
                throw new Exception("Profile not found");
            }
            $profileId = $coachData['data']['profile']['id'];
            
            // Handle User Updates (name, phone)
            $userData = [];
            if (isset($_POST['name'])) $userData['name'] = trim($_POST['name']);
            if (isset($_POST['phone'])) $userData['phone'] = trim($_POST['phone']);
            
            // Handle Image Upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $upload = File::upload($_FILES['profile_image'], 'profiles');
                
                if ($upload['status'] === 'success') {
                    $userData['image'] = $upload['filePath'];
                } else {
                    throw new Exception($upload['message'] ?? 'Image upload failed.');
                }
            }
            
            if (!empty($userData)) {
                $userUpdate = User::updateUser($userId, $userData);
                if ($userUpdate['status'] === 'success') {
                    // Update session data
                    foreach ($userData as $k => $v) {
                        $_SESSION['user'][$k] = $v;
                    }
                }
            }
            
            // Handle Coach Profile Updates
            $profileUpdates = [];
            if (isset($_POST['description'])) $profileUpdates['description'] = trim($_POST['description']);
            if (isset($_POST['category_id']) && !empty($_POST['category_id'])) $profileUpdates['category_id'] = (int)$_POST['category_id'];
            
            if (!empty($profileUpdates)) {
                $dataToUpdate = ['profile' => $profileUpdates];
                $coachUpdate = Coach::update($profileId, $dataToUpdate);
                if ($coachUpdate['status'] !== 'success') {
                    throw new Exception($coachUpdate['message']);
                }
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } elseif ($action === 'add_certification') {
        try {
            $certData = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'file' => $_FILES['cert_image'] ?? null
            ];
            
            if (empty($certData['name'])) {
                throw new Exception("Certification name is required.");
            }
            
            /** @var array $result */
            $result = Coach::addSingleCertification($userId, $certData);
            
            if ($result['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Certification added.']);
            } else {
                throw new Exception($result['message']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } elseif ($action === 'edit_certification') {
        try {
            $certId = (int)($_POST['cert_id'] ?? 0);
            if (!$certId) throw new Exception("Invalid certification ID.");

            $certData = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'file' => $_FILES['cert_image'] ?? null
            ];
            
            if (empty($certData['name'])) {
                throw new Exception("Certification name is required.");
            }
            
            /** @var array $result */
            $result = Coach::updateSingleCertification($certId, $userId, $certData);
            
            if ($result['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Certification updated.']);
            } else {
                throw new Exception($result['message']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } elseif ($action === 'delete_certification') {
        try {
            $certId = (int)($_POST['cert_id'] ?? 0);
            if (!$certId) throw new Exception("Invalid certification ID.");
            
            /** @var array $result */
            $result = Coach::deleteSingleCertification($certId, $userId);
            
            if ($result['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Certification deleted.']);
            } else {
                throw new Exception($result['message']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
}
