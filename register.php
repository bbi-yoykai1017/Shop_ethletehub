<?php
session_start();
require_once "Database.php";
require_once "functions.php";

if (isset($_SESSION['user_id'])) {
    header('localhost: index.php');
    exit;
}

// xu ly form dang ky
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['mat_khai'] ?? '');
    $confirm_password = trim($_POST['comfirm_password'] ?? '');

    // kiem tra input 
    if (empty($ten) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin";
    } elseif (strlen($password) < 6) {
        $error_message = "Mật khẩu phải có trên 6 ký tự";
    } elseif ($password != $confirm_password) {
        $error_message = "Mật khẩu không trùng khớp";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email không hợp lệ";
    } else {
        // khoi tao db
        $db = new Database();
        $conn = $db->connect();

        // goi ham dang ky 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

</body>

</html>