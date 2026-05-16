<?php

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use Dotenv\Dotenv;

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $pass;

    public $conn;

    public function connect()
    {
        if (class_exists(Dotenv::class)) {
            $dotenvPath = __DIR__ . '/.env';
            if (file_exists($dotenvPath)) {
                $dotenv = Dotenv::createImmutable(dirname($dotenvPath));
                $dotenv->safeLoad();
            }
        }

        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASSWORD'];

        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            
            $this->conn->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

        } catch (PDOException $e) {

            die("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }
}