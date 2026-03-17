<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Bạn có thể thêm kiểm tra quyền ở đây
?>