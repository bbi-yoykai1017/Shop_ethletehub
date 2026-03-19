<?php
session_start();
require_once "Database.php";
require_once "functions.php";
$db = new Database();
$conn = $db->connect();

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