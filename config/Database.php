<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/config.php';

        $host = $config['database']['host'];
        $dbname = $config['database']['dbname'];
        $user = $config['database']['user'];
        $pass = $config['database']['pass'];

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("DB Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }

    // ✅ 1. Run raw SQL (NO params)
    public static function run($sql)
    {
        return self::getInstance()->query($sql);
    }

    // ✅ 2. Run prepared query (WITH params) – RECOMMENDED
    public static function runPrepared($sql, array $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function getLastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }
}
