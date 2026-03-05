<?php
class Database {
    private $host = "localhost";
    private $dbname = "shop_ethletehub"; 
    private $user = "root";
    private $pass = "";
    private $port = "3307";

    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->dbname.";charset=utf8",
                $this->user,
                $this->pass
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>