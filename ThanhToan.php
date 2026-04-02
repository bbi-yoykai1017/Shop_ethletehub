<?php
require_once 'Database.php';
require_once 'model/functions.php';
session_start();

// Kiểm tra user đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = (int) $_SESSION['user_id'];

// Lấy thông tin user từ database
$stmt = $conn->prepare("SELECT id, ten, email, so_dien_thoai FROM nguoi_dung WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Lấy giỏ hàng từ database
$cart = [];
$stmt = $conn->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$giohang = $stmt->fetch(PDO::FETCH_ASSOC);

if ($giohang) {
    $stmt = $conn->prepare("
        SELECT 
            ctgh.id as chi_tiet_id,
            ctgh.san_pham_id as id,
            sp.ten as name,
            sp.gia as price,
            sp.hinh_anh_chinh as image,
            ctgh.kich_thuoc_id,
            ks.ten as size,
            ctgh.mau_sac_id,
            ms.ten as color,
            ctgh.so_luong as quantity
        FROM chi_tiet_gio_hang ctgh
        JOIN san_pham sp ON ctgh.san_pham_id = sp.id
        LEFT JOIN kich_thuoc ks ON ctgh.kich_thuoc_id = ks.id
        LEFT JOIN mau_sac ms ON ctgh.mau_sac_id = ms.id
        WHERE ctgh.gio_hang_id = ?
    ");
    $stmt->execute([$giohang['id']]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra giỏ hàng rỗng
if (empty($cart)) {
    header("Location: cart.php");
    exit;
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
    <!-- ========================
         NAVBAR MỚI
         ======================== -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container px-4">
            <div class="row">
                <div class="col-12">
                    <!-- Logo & Brand -->
                    <a class="navbar-brand" href="#home">
                        <i class="fas fa-dumbbell"></i>
                        AthleteHub
                    </a>
                </div>
                <div class="col-12">
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
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="CRUDuser.php">Quản trị</a>
                                </li>
                            <?php endif; ?>
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
                                <span class="cart-count"></span>
                            </div>
                            <div class="user-account-wrapper d-flex align-items-center">
                                <div class="user-action-dropdown dropdown">
                                    <a href="#" class="user-icon-link me-2 text-decoration-none dropdown-toggle"
                                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="color: white;">
                                        <i class="fas fa-user-circle fa-lg"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                        <?php if (isset($_SESSION['user_name'])): ?>
                                            <li>
                                                <h6 class="dropdown-header">
                                                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                                </h6>
                                            </li>
                                            <li><a class="dropdown-item" href="profile.php"><i
                                                        class="fas fa-user-edit me-2"></i> Hồ sơ của tôi</a></li>
                                            <li><a class="dropdown-item" href="orders.php"><i
                                                        class="fas fa-shopping-bag me-2"></i> Đơn hàng đã mua</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="logout.php">
                                                    <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <a class="dropdown-item" href="login.php">
                                                    <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="register.php">
                                                    <i class="fas fa-user-plus me-2"></i> Đăng ký
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </nav>
</body>
</html>
