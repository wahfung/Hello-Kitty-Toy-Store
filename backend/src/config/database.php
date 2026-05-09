<?php
/**
 * 数据库配置文件
 * Hello Kitty 玩具商城
 */

class Database {
    private static $instance = null;
    private $connection;

    // 从环境变量获取数据库配置
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $charset = 'utf8mb4';

    private function __construct() {
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->dbname = getenv('DB_NAME') ?: 'hellokitty_shop';
        $this->username = getenv('DB_USER') ?: 'kitty';
        $this->password = getenv('DB_PASS') ?: 'kitty123';

        $this->connect();
    }

    /**
     * 获取数据库单例实例
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 建立数据库连接
     */
    private function connect(): void {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);

            // 记录连接成功日志
            error_log("[" . date('Y-m-d H:i:s') . "] [INFO] Database connection established successfully");

        } catch (PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] [ERROR] Database connection failed: " . $e->getMessage());
            throw new Exception("数据库连接失败，请稍后再试");
        }
    }

    /**
     * 获取 PDO 连接对象
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * 执行查询并返回所有结果
     */
    public function fetchAll(string $sql, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] [ERROR] Query failed: " . $e->getMessage());
            throw new Exception("查询失败");
        }
    }

    /**
     * 执行查询并返回单条结果
     */
    public function fetchOne(string $sql, array $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] [ERROR] Query failed: " . $e->getMessage());
            throw new Exception("查询失败");
        }
    }

    /**
     * 执行插入/更新/删除操作
     */
    public function execute(string $sql, array $params = []): bool {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] [ERROR] Execute failed: " . $e->getMessage());
            throw new Exception("操作失败");
        }
    }

    /**
     * 获取最后插入的ID
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }

    // 防止克隆
    private function __clone() {}

    // 防止反序列化
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
