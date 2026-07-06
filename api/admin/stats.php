<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

// Release session lock to prevent blocking concurrent requests
session_write_close();

try {
    // Total Users
    $totalUsers = Database::run("SELECT COUNT(*) FROM users WHERE role != 'siteadmin'")->fetchColumn();

    // Total Families
    $totalFamilies = Database::run("SELECT COUNT(*) FROM families")->fetchColumn();

    // DB Space
    $dbName = Database::run("SELECT DATABASE()")->fetchColumn();
    $dbSizeResult = Database::runPrepared("SELECT SUM(data_length + index_length) AS db_size FROM information_schema.tables WHERE table_schema = ?", [$dbName])->fetchColumn();

    // Convert to MB
    $dbSizeMB = round($dbSizeResult / (1024 * 1024), 2);

    // Events Today (optimized to be sargable)
    $eventsToday = Database::run("SELECT COUNT(*) FROM events WHERE start_time >= CURDATE() AND start_time < CURDATE() + INTERVAL 1 DAY")->fetchColumn();

    $stats = [
        "total_users" => (int) $totalUsers,
        "total_families" => (int) $totalFamilies,
        "db_space_bytes" => (int) $dbSizeResult,
        "db_space_mb" => $dbSizeMB,
        "events_today" => (int) $eventsToday,
        // Login graph optimized to scan only last 30 days
        "login_graph" => Database::run("SELECT DATE(login_time) as date, COUNT(*) as count FROM login_logs WHERE login_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(login_time) ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC),
        "recent_signups" => Database::run("SELECT u.id, u.name, u.email, u.created_at, f.name as family_name, f.approved as family_approved FROM users u LEFT JOIN user_family uf ON u.id = uf.user_id LEFT JOIN families f ON uf.family_id = f.id WHERE u.role != 'siteadmin' ORDER BY u.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)
    ];

    echo json_encode(["status" => "success", "data" => $stats]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Could not fetch stats: " . $e->getMessage()]);
}
