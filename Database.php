<?php
class Database {
    private $host = "localhost";
    private $dbname = "athletehub";
    private $user = "root";
    private $pass = "";
    public $conn;
    public function connect() {
        $this->conn = null;
        try {
            // Chú ý dùng biến $this-> để lấy giá trị từ thuộc tính class
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>