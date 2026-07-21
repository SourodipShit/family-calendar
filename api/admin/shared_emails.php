<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/SharedEmails.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        $emailAddress = $_POST['email_address'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($emailAddress) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Email and password are required."]);
            exit;
        }

        $result = SharedEmails::create($emailAddress, $password);
        echo json_encode($result);
        break;

    case 'read':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $email = SharedEmails::getById($id);
            if ($email) {
                echo json_encode(["status" => "success", "data" => $email]);
            } else {
                echo json_encode(["status" => "error", "message" => "Shared email not found."]);
            }
        } else {
            $emails = SharedEmails::getAll();
            echo json_encode(["status" => "success", "data" => $emails]);
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $emailAddress = $_POST['email_address'] ?? '';
        $password = $_POST['password'] ?? '';
        $familyId = $_POST['family_id'] ?? null;

        if (empty($id) || empty($emailAddress) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "ID, Email, and password are required."]);
            exit;
        }

        $result = SharedEmails::update($id, $emailAddress, $password, $familyId);
        echo json_encode($result);
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            echo json_encode(["status" => "error", "message" => "ID is required."]);
            exit;
        }

        $result = SharedEmails::delete($id);
        echo json_encode($result);
        break;

    case 'upload_csv':
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(["status" => "error", "message" => "Please upload a valid CSV file."]);
            exit;
        }

        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'csv') {
            echo json_encode(["status" => "error", "message" => "Invalid file format. Only CSV allowed."]);
            exit;
        }

        $handle = fopen($fileTmpPath, "r");
        if ($handle !== FALSE) {
            $successCount = 0;
            $errorCount = 0;
            $isFirstRow = true;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($isFirstRow) {
                    $isFirstRow = false; // Skip header
                    continue;
                }

                // Assuming column 0 is email and column 1 is password
                if (isset($data[0]) && isset($data[1])) {
                    $emailAddress = trim($data[0]);
                    $password = trim($data[1]);

                    if (!empty($emailAddress) && !empty($password)) {
                        $res = SharedEmails::create($emailAddress, $password);
                        if ($res['status'] === 'success') {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    }
                }
            }
            fclose($handle);
            echo json_encode([
                "status" => "success", 
                "message" => "CSV processed. Successfully added $successCount emails. Failed to add $errorCount emails."
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error reading the CSV file."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
