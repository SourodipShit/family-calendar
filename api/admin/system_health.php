<?php
session_start();
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

try {
    // 1. DB Stats
    $dbVersion = Database::run("SELECT VERSION()")->fetchColumn();
    $threadsConnected = Database::run("SHOW STATUS WHERE variable_name = 'Threads_connected'")->fetch(PDO::FETCH_ASSOC)['Value'] ?? 0;
    
    // DB Size
    $dbName = Database::run("SELECT DATABASE()")->fetchColumn();
    $dbSizeResult = Database::runPrepared("SELECT SUM(data_length + index_length) AS db_size FROM information_schema.tables WHERE table_schema = ?", [$dbName])->fetchColumn();
    $dbSizeMB = round($dbSizeResult / (1024 * 1024), 2);

    // 2. Uploads folder size
    $uploadsPath = __DIR__ . '/../../public/uploads';
    $uploadsSize = 0;
    if (is_dir($uploadsPath)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsPath));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $uploadsSize += $file->getSize();
            }
        }
    }
    $uploadsSizeMB = round($uploadsSize / (1024 * 1024), 2);

    // 3. Disk Space
    $diskFree = disk_free_space("/");
    $diskTotal = disk_total_space("/");
    $diskUsed = $diskTotal - $diskFree;
    $diskUsedPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;
    
    // Format disk space in GB
    $diskTotalGB = round($diskTotal / (1024 * 1024 * 1024), 2);
    $diskUsedGB = round($diskUsed / (1024 * 1024 * 1024), 2);

    // 4. PHP / Server Info
    $phpVersion = phpversion();
    $serverOs = php_uname('s');

    // 5. App level stats (dummy/basic)
    // You can replace email_queue with an actual query if there is an email queue table
    $emailQueue = 0; 
    
    $stats = [
        "db_version" => $dbVersion,
        "db_connections" => (int) $threadsConnected,
        "db_size_mb" => $dbSizeMB,
        "uploads_size_mb" => $uploadsSizeMB,
        "disk_used_percent" => $diskUsedPercent,
        "disk_used_gb" => $diskUsedGB,
        "disk_total_gb" => $diskTotalGB,
        "php_version" => $phpVersion,
        "server_os" => $serverOs,
        "email_queue" => $emailQueue
    ];

    echo json_encode(["status" => "success", "data" => $stats]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Could not fetch system health stats: " . $e->getMessage()]);
}
