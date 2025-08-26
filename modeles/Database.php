<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Charger le .env
        $env = parse_ini_file(__DIR__ . '/../.env');

        $this->host     = $env['DB_HOST'] ?? 'localhost';
        $this->db_name  = $env['DB_NAME'] ?? '';
        $this->username = $env['DB_USER'] ?? '';
        $this->password = $env['DB_PASS'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
