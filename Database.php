<?php
class Database
{
    /*private $host = "localhost";
    private $dbname = "jwbspmbk_athletehub";
    private $user = "jwbspmbk_athlete";
    private $pass = "XzIUV#to3UTP";
    */
    private $host = "localhost";
    private $dbname = "athletehub";
    private $user = "root";
    private $pass = "";
    public $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            $this->conn = null;
        }
        return $this->conn;
    }
}
?>