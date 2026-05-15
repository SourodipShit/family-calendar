<?php
require_once __DIR__ . '/../../classes/GlobalSettings.php';
require_once __DIR__ . '/../../classes/File.php';
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic admin check could be added here if needed
// if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'siteadmin') {
//     echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
//     exit;
// }

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

function getSettingValue($key) {
    $stmt = Database::runPrepared("SELECT setting_value FROM global_settings WHERE setting_key = ?", [$key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['setting_value'] : null;
}

function updateOrInsertSetting($key, $value) {
    $stmt = Database::runPrepared("SELECT id FROM global_settings WHERE setting_key = ?", [$key]);
    $exists = $stmt->fetch();

    if ($exists) {
        Database::runPrepared("UPDATE global_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
    } else {
        Database::runPrepared("INSERT INTO global_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
    }
}

switch ($action) {
    case 'fetch_all':
        fetchAllSettings();
        break;

    case 'update_logo':
        updateLogo();
        break;

    case 'add_timezone':
        addTimezone($data);
        break;

    case 'update_timezone':
        updateTimezone($data);
        break;

    case 'delete_timezone':
        deleteTimezone($data);
        break;

    case 'update_setting':
        updateSettingAction($data);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function fetchAllSettings() {
    $settings = GlobalSettings::getAllSettings();
    echo json_encode($settings);
}

function updateLogo() {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        // Fetch the old logo path to delete it later
        $oldLogoPath = getSettingValue('site_logo');

        // Change working directory to siteadmin so File::upload's "../public/uploads" 
        // points to the correct root-level directory, consistent with other modules.
        $originalDir = getcwd();
        chdir(__DIR__ . '/../../siteadmin');
        
        // Delete old logo if it exists
        if ($oldLogoPath && !empty($oldLogoPath)) {
            File::deleteFile($oldLogoPath);
        }

        $upload = File::upload($_FILES['logo'], 'logo');
        
        // Change back to original directory
        chdir($originalDir);

        if ($upload['status'] === 'success') {
            $imagePath = $upload['filePath'];
            updateOrInsertSetting('site_logo', $imagePath);
            echo json_encode(['status' => 'success', 'message' => 'Logo updated successfully', 'path' => $imagePath]);
        } else {
            echo json_encode($upload);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No logo file uploaded or upload error.']);
    }
}

function addTimezone($data) {
    $newTimezone = $data['timezone_data'] ?? null;
    if (!$newTimezone) {
        echo json_encode(['status' => 'error', 'message' => 'Timezone data missing']);
        return;
    }

    $timezonesJson = getSettingValue('timezone') ?: '[]';
    $timezones = json_decode($timezonesJson, true);
    if (!is_array($timezones)) $timezones = [];

    $timezones[] = $newTimezone;
    
    $updatedJson = json_encode($timezones);
    updateOrInsertSetting('timezone', $updatedJson);
    echo json_encode(['status' => 'success', 'message' => 'Timezone added successfully', 'data' => $timezones]);
}

function updateTimezone($data) {
    $index = $data['index'] ?? null;
    $updatedTimezone = $data['timezone_data'] ?? null;

    if ($index === null || !$updatedTimezone) {
        echo json_encode(['status' => 'error', 'message' => 'Index or timezone data missing']);
        return;
    }

    $timezonesJson = getSettingValue('timezone') ?: '[]';
    $timezones = json_decode($timezonesJson, true);

    if (isset($timezones[$index])) {
        $timezones[$index] = $updatedTimezone;
        $updatedJson = json_encode($timezones);
        updateOrInsertSetting('timezone', $updatedJson);
        echo json_encode(['status' => 'success', 'message' => 'Timezone updated successfully', 'data' => $timezones]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Timezone index not found']);
    }
}

function deleteTimezone($data) {
    $index = $data['index'] ?? null;

    if ($index === null) {
        echo json_encode(['status' => 'error', 'message' => 'Index missing']);
        return;
    }

    $timezonesJson = getSettingValue('timezone') ?: '[]';
    $timezones = json_decode($timezonesJson, true);

    if (isset($timezones[$index])) {
        array_splice($timezones, $index, 1);
        $updatedJson = json_encode($timezones);
        updateOrInsertSetting('timezone', $updatedJson);
        echo json_encode(['status' => 'success', 'message' => 'Timezone deleted successfully', 'data' => $timezones]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Timezone index not found']);
    }
}

function updateSettingAction($data) {
    $key = $data['key'] ?? null;
    $value = $data['value'] ?? null;

    if (!$key) {
        echo json_encode(['status' => 'error', 'message' => 'Setting key missing']);
        return;
    }

    updateOrInsertSetting($key, $value);
    echo json_encode(['status' => 'success', 'message' => 'Setting updated successfully']);
}
