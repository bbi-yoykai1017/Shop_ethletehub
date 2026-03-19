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
        $result = registerUser($conn, $ten, $email, $password);

        if ($result['success']) {
            $success_message = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ";

            // clean form 
            $ten = '';
            $email = '';
            $password = '';
            $confirm_password = '';
        } else {
            $error_message = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dumbbell"></i> AthleteHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- register container -->
     <div class="register-container">
        <div class="register-card">
            <!-- header -->
             <div class="register-header">
                <h1><i class="fas fa-user-plus"></i> Đăng ký</h1>
                <p>Tạo tài khoản AthleteHub của bạn</p>
             </div>
        </div>
     </div>
</body>

</html>