<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . "/../classes/Photo.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';
$family_id = $_POST['family_id'] ?? $_SESSION['user']['active_family_id'] ?? null;

if (!$family_id) {
    echo json_encode(["status" => "error", "message" => "No family selected"]);
    exit;
}

if ($action == 'upload') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $data = [
            'file' => $_FILES['file'],
            'family_id' => $family_id,
            'size' => $_FILES['file']['size'], // The class logic will override this with the compressed size
            'metadata' => $_POST['metadata'] ?? null,
            'uploaded_by' => $_SESSION['user']['id']
        ];
        $result = Photo::uploadPhoto($data);

        if ($result['status'] === 'success') {
            $storageDetails = Photo::getPhotoStorageDetails($family_id);
            if ($storageDetails['status'] === 'success') {
                $total = $storageDetails['data']['total_storage'];
                $allocated = $storageDetails['data']['allocated_storage'];
                
                if ($total > $allocated) {
                    $heads = Database::runPrepared("
                        SELECT u.email, u.name 
                        FROM users u
                        JOIN user_family uf ON u.id = uf.user_id
                        WHERE uf.family_id = ? AND u.role = 'family-head'
                    ", [$family_id])->fetchAll(PDO::FETCH_ASSOC);
                    
                    require_once __DIR__ . "/../services/mail/Mail.php";
                    foreach ($heads as $head) {
                        Mail::sendStorageLimitExceeded($head['email'], $head['name'], $storageDetails['data']);
                    }
                }
            }
        }

        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded or upload error"]);
    }
    exit;
}

if ($action == 'approve') {
    $photoId = $_POST['id'] ?? null;
    if ($photoId) {
        $photoToApprove = Database::runPrepared("SELECT file_size FROM photos WHERE id = ?", [$photoId])->fetch(PDO::FETCH_ASSOC);
        if ($photoToApprove) {
            $family = Database::runPrepared("SELECT storage_allocated FROM families WHERE id = ?", [$family_id])->fetch(PDO::FETCH_ASSOC);
            $allocatedBytes = ($family['storage_allocated'] ?? 500) * 1024 * 1024;
            
            $currentApproved = Database::runPrepared("SELECT SUM(file_size) as total FROM photos WHERE family_id = ? AND status = 'approved'", [$family_id])->fetch(PDO::FETCH_ASSOC);
            $approvedBytes = $currentApproved['total'] ? (int)$currentApproved['total'] : 0;
            
            $newSize = (int)$photoToApprove['file_size'];
            
            if (($approvedBytes + $newSize) > $allocatedBytes) {
                $overage = ($approvedBytes + $newSize) - $allocatedBytes;
                $oldestPhotos = Database::runPrepared("SELECT id, file_size FROM photos WHERE family_id = ? AND status = 'approved' ORDER BY created_at ASC", [$family_id])->fetchAll(PDO::FETCH_ASSOC);
                
                $freedSpace = 0;
                foreach ($oldestPhotos as $oldPhoto) {
                    if ($freedSpace >= $overage) break;
                    Photo::deletePhoto($oldPhoto['id']);
                    $freedSpace += (int)$oldPhoto['file_size'];
                }
            }
        }

        $result = Photo::approvePhoto($photoId);
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "Missing photo ID"]);
    }
    exit;
}

if ($action == 'delete') {
    $photoId = $_POST['id'] ?? null;
    if ($photoId) {
        $result = Photo::deletePhoto($photoId);
        echo json_encode($result);
    } else {
        echo json_encode(["status" => "error", "message" => "Missing photo ID"]);
    }
    exit;
}

if ($action == 'getPending') {
    $result = Photo::getPendingPhotos($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getByFamily') {
    $result = Photo::getByFamily($family_id);
    echo json_encode($result);
    exit;
}

if ($action == 'getStorageDetails') {
    $result = Photo::getPhotoStorageDetails($family_id);
    echo json_encode($result);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;

