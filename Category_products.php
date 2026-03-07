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
  <!-- ========================
         FOOTER
         ======================== -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-top">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">AthleteHub</h4>
                            <p style="color: #c0c0c0; margin-bottom: 20px;">Chúng tôi cung cấp sản phẩm thể thao chất
                                lượng cao với giá cạnh tranh tốt nhất trên thị trường.</p>
                            <div class="footer-socials">
                                <a href="#" class="social-link">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên kết nhanh</h4>
                            <ul class="footer-links">
                                <li><a href="index.php"><i class="fas fa-angle-right"></i>Trang chủ</a></li>
                                <li><a href="products.php"><i class="fas fa-angle-right"></i>Sản phẩm</a></li>
                                <li><a href="#categories"><i class="fas fa-angle-right"></i>Danh mục</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Về chúng tôi</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Hỗ trợ khách hàng</h4>
                            <ul class="footer-links">
                                <li><a href="#"><i class="fas fa-angle-right"></i>Liên hệ chúng tôi</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Chính sách giao hàng</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Chính sách hoàn trả</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Câu hỏi thường gặp</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Theo dõi đơn hàng</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên lạc</h4>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <p>123 Đường Thể Thao, TP. Hồ Chí Minh, Việt Nam</p>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <p>+84 (0) 123 456 789</p>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <p>support@athletehub.vn</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="row mt-5">
                <div class="col-lg-6 mx-auto">
                    <div class="footer-newsletter">
                        <h3>Đăng ký nhận tin</h3>
                        <p>Nhận ưu đãi độc quyền, cập nhật sản phẩm mới và nhiều hơn nữa trực tiếp vào hộp thư của bạn.
                        </p>
                        <form class="newsletter-form">
                            <input type="email" class="newsletter-input" placeholder="Nhập email của bạn" required>
                            <button type="submit" class="newsletter-btn">
                                <i class="fas fa-paper-plane"></i>
                                Đăng ký
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        &copy;
                        <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
                    </div>
                    <ul class="footer-bottom-links">
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Điều khoản dịch vụ</a></li>
                        <li><a href="#">Sitemap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <script src="js/categories.js"></script>


</html>
