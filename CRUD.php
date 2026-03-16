<?php 
session_start();

// e để phần kiểm tra quyền ở đây nha a 
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// code a viết ở dưới bình thường nha
?>