<?php
require_once "Database.php";
$db = new Database();
$conn = $db->connect();

// thong bao
$message = "";
// lay du lieu tu nguoi dung
if($_SERVER['REQUEST_METHOD'] == "POST") 
   $ten = $_POST['ten'];
   $email = $_POST['email'];
   $so_dien_thoai = $_POST['so_dien_thoai'];
   $dia_chi = $_POST['dia_chi'];
   $mat_khau

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