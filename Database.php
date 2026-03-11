<?php
class Database {
private $host = "sqlXXX.epizy.com";
    private $dbname = "jwbsprmbk_athletehub";
    private $user = "jwbsprmbk_athlete";
    private $pass = "XzIUV#to3UTP";
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbname.";charset=utf8",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>