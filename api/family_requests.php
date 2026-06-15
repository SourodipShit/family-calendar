<?php
require_once __DIR__ . '/../classes/FamilyRequest.php';
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$family_id = $_SESSION['user']['active_family_id'] ?? null;
$user_id = $_SESSION['user']['id'];
$user_email = $_SESSION['user']['email'];

switch ($action) {
    case 'create':
        if (empty($data['email'])) {
            echo json_encode(['status' => 'error', 'message' => 'Email is required']);
            exit;
        }

        $receiver_email = $data['email'];
        $receiver = Database::runPrepared("SELECT id FROM users WHERE email = ?", [$receiver_email])->fetch(PDO::FETCH_ASSOC);
        if (!$receiver) {
            echo json_encode(['status' => 'error', 'message' => 'User not found with that email.']);
            exit;
        }
        $receiver_id = $receiver['id'];

        $receiver_family = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ? LIMIT 1", [$receiver_id])->fetch(PDO::FETCH_ASSOC);
        if (!$receiver_family) {
            echo json_encode(['status' => 'error', 'message' => 'That user does not belong to any family.']);
            exit;
        }

        $data['requester_id'] = $user_id;
        $data['family_id'] = $receiver_family['family_id'];
        $data['receiver_id'] = $receiver_id;

        $result = FamilyRequest::create($data);
        echo json_encode($result);
        exit;

    case 'getByFamily':
        if (!$family_id) {
            echo json_encode(['status' => 'error', 'message' => 'Family context not found']);
            exit;
        }
        $requests = FamilyRequest::getByFamily($family_id);
        echo json_encode(['status' => 'success', 'data' => $requests]);
        exit;

    case 'getMyRequests':
        $requests = FamilyRequest::getByUserEmail($user_email);
        echo json_encode(['status' => 'success', 'data' => $requests]);
        exit;

    case 'updateStatus':
        $id = $data['id'] ?? null;
        $status = $data['status'] ?? null; 
        
        if (!$id || !in_array($status, ['approved', 'rejected'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing id/status']);
            exit;
        }
        
        $req = Database::runPrepared("SELECT * FROM family_requests WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        
        if ($req && ($req['email'] === $user_email || $req['requester_id'] == $user_id)) {
            // If the user's email matches but receiver_id is empty, update it so they can be added to user_family
            if ($req['email'] === $user_email && empty($req['receiver_id'])) {
                Database::runPrepared("UPDATE family_requests SET receiver_id = ? WHERE id = ?", [$user_id, $id]);
            }
            $result = FamilyRequest::updateStatus($id, $status);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or request not found']);
        }
        exit;

    case 'delete':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing request ID']);
            exit;
        }
        
        // Authorization check for deletion
        $req = Database::runPrepared("SELECT * FROM family_requests WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        if ($req && ($req['requester_id'] == $user_id || $req['email'] === $user_email)) {
            $result = FamilyRequest::delete($id);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or request not found']);
        }
        exit;

    case 'delink':
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing request ID']);
            exit;
        }
        $result = FamilyRequest::delink($id, $user_id);
        echo json_encode($result);
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}
