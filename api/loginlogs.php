<?php
require_once __DIR__ . "/../config/Database.php";

header('Content-Type: application/json');

$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;

try {
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

    $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
    $orderDir = isset($_GET['order'][0]['dir']) && strtolower($_GET['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';

    $columns = [
        0 => 'l.login_time',
        1 => 'user_name',
        2 => 'family_name',
        3 => 'l.device',
        4 => 'l.ip_address'
    ];

    $orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'l.login_time';

    $select = "SELECT l.*, MAX(u.name) AS user_name, MAX(u.nickname) AS nickname, GROUP_CONCAT(f.name SEPARATOR ', ') AS family_name";
    $from = " FROM login_logs l
              LEFT JOIN users u ON l.user_id = u.id
              LEFT JOIN user_family uf ON u.id = uf.user_id
              LEFT JOIN families f ON uf.family_id = f.id";
              
    $where = "";
    $params = [];

    if (!empty($search)) {
        $where = " WHERE (u.name LIKE :search 
                       OR f.name LIKE :search 
                       OR l.device LIKE :search 
                       OR l.browser LIKE :search 
                       OR l.os LIKE :search 
                       OR l.ip_address LIKE :search 
                       OR l.location LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $groupBy = " GROUP BY l.id";

    $totalQuery = "SELECT COUNT(DISTINCT l.id) AS total" . $from;
    $totalResult = Database::run($totalQuery)->fetch(PDO::FETCH_ASSOC);
    $recordsTotal = $totalResult ? $totalResult['total'] : 0;

    if (!empty($where)) {
        $filteredQuery = "SELECT COUNT(DISTINCT l.id) AS total" . $from . $where;
        $stmt = Database::getInstance()->prepare($filteredQuery);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $filteredResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $recordsFiltered = $filteredResult ? $filteredResult['total'] : 0;
    } else {
        $recordsFiltered = $recordsTotal;
    }

    $dataQuery = $select . $from . $where . $groupBy . " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT :start, :length";
    $stmt = Database::getInstance()->prepare($dataQuery);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$row) {
        $row['login_time_formatted'] = date('M d, Y h:i A', strtotime($row['login_time']));
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => intval($recordsTotal),
        'recordsFiltered' => intval($recordsFiltered),
        'data' => $data
    ]);
} catch (\Throwable $e) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Server Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine()
    ]);
}
