<?php
require_once 'functions.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();

// Lấy ID từ URL
$id_danhmuc = isset($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;

if ($id_danhmuc > 0) {
    // Gọi hàm lấy sản phẩm theo danh mục (hàm mình đã thảo luận ở trên)
    $products = getProductsByCategory($conn, $id_danhmuc);
} else {
    header("Location: index.php"); // Nếu không có ID thì quay về trang chủ
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AthleteHub - Cửa hàng đồ thể thao</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
</head>

<body>
    <!-- ========================
         NAVBAR MỚI
         ======================== -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-custom">
            <!-- Logo & Brand -->
            <a class="navbar-brand" href="#home">
                <i class="fas fa-dumbbell"></i>
                AthleteHub
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Navigation Links -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Danh mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Về chúng tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Liên hệ</a>
                    </li>
                </ul>

                <!-- Search Bar (Centered) -->
                <div class="navbar-search-container">
                    <div class="navbar-search">
                        <input type="text" placeholder="Tìm sản phẩm..." class="search-input">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Right Icons -->
                <div class="navbar-right">
                    <div class="nav-notification">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </div>

                    <div class="cart-icon" onclick="window.location.href='cart.php'">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </div>

                    <div class="user-account">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>
<header>
    
</header>
