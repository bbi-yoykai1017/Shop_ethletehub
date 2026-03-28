<?php
session_start();
require_once 'model/functions.php';
require_once 'model/detail.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();
$products = getallproduct($conn);

// Xử lý sản phẩm - thêm các trường tính toán
$products = processProducts($products);

// Giới hạn hiển thị 6 sản phẩm
$displayProducts = array_slice($products, 0, 6);
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
                                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                                        <i class="fas fa-user-circle fa-lg"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                        <?php if (isset($_SESSION['user_name'])): ?>
                                            <li>
                                                <h6 class="dropdown-header"> <?php echo htmlspecialchars($_SESSION['user_name']); ?></h6>
                                            </li>
                                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i> Hồ sơ của tôi</a></li>
                                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i> Đơn hàng đã mua</a></li>
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

    <!-- ========================
         HERO SECTION MỚI
         ======================== -->
    <section class="hero" id="home">
        <div class="container-custom">
            <div class="row align-items-center">
                <!-- Hero Image (Left) -->
                <div class="col-lg-6 col-md-6">
                    <div class="hero-image hero-image-left">
                        <img src="https://conndesign.vn/wp-content/uploads/2024/04/thiet-ke-shop-the-thao-8.jpg" alt="Đồ thể thao chuyên nghiệp"
                            class="hero-img">
                    </div>
                </div>

                <!-- Hero Content (Right) -->
                <div class="col-lg-6 col-md-6">
                    <div class="hero-content">
                        <h1>
                            Nâng cấp <span class="highlight">Thể lực</span>
                            <br>
                            Của bạn
                        </h1>
                        <p>Khám phá bộ sưu tập đồ thể thao chuyên nghiệp với công nghệ tối tân. Từ quần áo tập luyện đến
                            thiết bị thể dục, chúng tôi có mọi thứ bạn cần để đạt được mục tiêu của mình.</p>

                        <div class="hero-buttons">
                            <button class="btn-custom btn-primary-custom">
                                <i class="fas fa-shopping-bag"></i>
                                Mua sắm ngay
                            </button>
                            <button class="btn-custom btn-secondary-custom">
                                <i class="fas fa-info-circle"></i>
                                Tìm hiểu thêm
                            </button>
                        </div>

                        <div class="hero-stats">
                            <div class="stat-item">
                                <div class="stat-number">50K+</div>
                                <div class="stat-label">Khách hàng</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">1000+</div>
                                <div class="stat-label">Sản phẩm</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">4.8★</div>
                                <div class="stat-label">Đánh giá</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================
         CATEGORIES SECTION MỚI
         ======================== -->
    <section class="categories" id="categories">
        <div class="container-custom">
            <div class="section-title">
                <h2>Danh Mục Sản Phẩm</h2>
                <p class="section-subtitle">Chọn từ nhiều loại sản phẩm thể thao chất lượng cao</p>
            </div>

            <!-- Search & Dropdown Controls -->
            <div class="categories-controls">
                <!-- Dropdown Menu -->
                <div class="categories-dropdown-wrapper">
                    <button class="btn-categories-dropdown" id="categoriesDropdownBtn">
                        <i class="fas fa-list"></i>
                        Xem tất cả danh mục
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="categories-dropdown-menu" id="categoriesDropdownMenu">
                        <div>
                            <a href="Category_products.php?danh_muc_id=1" class="dropdown-cat-item" onclick="window.location.href=this.href">
                                <i class="fas fa-person-running"></i>
                                <span>Quần áo</span>
                                <span class="badge">200+</span>
                            </a>
                        </div>

                        <a href="Category_products.php?danh_muc_id=2" class="dropdown-cat-item" onclick="window.location.href=this.href">
                            <i class="fas fa-shoe-prints"></i>
                            <span>Giày</span>
                            <span class="badge">150+</span>
                        </a>
                        <a href="Category_products.php?danh_muc_id=3" class="dropdown-cat-item" onclick="window.location.href=this.href">
                            <i class="fas fa-dumbbell"></i>
                            <span>Thiết bị</span>
                            <span class="badge">300+</span>
                        </a>
                        <a href="Category_products.php?danh_muc_id=4" class="dropdown-cat-item" onclick="window.location.href=this.href">
                            <i class="fas fa-briefcase"></i>
                            <span>Phụ kiện</span>
                            <span class="badge">250+</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="row" id="categoriesGrid">
                <div class="col-lg-3 col-md-6">
                    <div class="category-card" onclick="window.location.href='Category_products.php?danh_muc_id=1'" id="quanao" data-category="quanao">
                        <div class="category-img">
                            <i class="fas fa-person-running"></i>
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-info">
                            <h3>Quần áo</h3>
                            <p>Quần áo tập luyện thoáng khí</p>
                            <span class="category-badge">200+ sản phẩm</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="category-card" onclick="window.location.href='Category_products.php?danh_muc_id=2'" id="giay" data-category="giay">

                        <div class="category-img">
                            <i class="fas fa-shoe-prints"></i>
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-info">
                            <h3>Giày</h3>
                            <p>Giày thể thao đa năng</p>
                            <span class="category-badge">150+ sản phẩm</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="category-card" onclick="window.location.href='Category_products.php?danh_muc_id=3'" id="thietbi" data-category="thietbi">
                        <div class="category-img">
                            <i class="fas fa-dumbbell"></i>
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-info">
                            <h3>Thiết bị</h3>
                            <p>Dụng cụ tập luyện chuyên dụng</p>
                            <span class="category-badge">300+ sản phẩm</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="category-card" onclick="window.location.href='Category_products.php?danh_muc_id=4'" id="phukien" data-category="phukien">
                        <div class="category-img">
                            <i class="fas fa-briefcase"></i>
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-info">
                            <h3>Phụ kiện</h3>
                            <p>Túi, mũ, dây buộc và nhiều hơn</p>
                            <span class="category-badge">250+ sản phẩm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================
         PRODUCTS SECTION
         ======================== -->
    <section class="products" id="products">
        <div class="container-custom">
            <div class="section-title">
                <h2>Sản Phẩm Nổi Bật</h2>
                <p class="section-subtitle">Những sản phẩm bán chạy nhất của chúng tôi</p>
            </div>

            <div class="filter-buttons">
                <button class="filter-btn active">Tất cả</button>
                <button class="filter-btn">Quần áo</button>
                <button class="filter-btn">Giày</button>
                <button class="filter-btn">Thiết bị</button>
                <button class="filter-btn">Phụ kiện</button>
            </div>
            <div class="products-grid">
                <?php foreach ($displayProducts as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="display: block;">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="./public/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        onerror="this.src='public/placeholder.svg'; this.style.display='block';">
                                <?php else: ?>
                                    <img src="public/placeholder.svg" alt="No image">
                                <?php endif; ?>
                            </a>

                            <?php if ($product['discount'] > 0): ?>
                                <span class="product-badge sale">-<?php echo $product['discount']; ?>%</span>
                            <?php endif; ?>
                            <span class="product-rating">
                                <i class="fas fa-star"></i>
                                <?php echo number_format($product['rating'], 1); ?>
                            </span>
                            <button class="btn-quick-view">Xem nhanh</button>
                        </div>

                        <div class="product-info">
                            <div class="product-category"><?php echo $product['categoryLabel']; ?></div>

                            <h3 class="product-name">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>"
                                    style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>

                            <div class="rating-stars">
                                <?php echo getStarRating($product['rating']); ?>
                                <span class="rating-text">(<?php echo $product['so_luong_danh_gia'] ?? 0; ?>)</span>
                            </div>
                            <div class="product-price">
                                <span class="price-current"><?php echo formatPrice($product['price']); ?></span>

                            </div>
                            <p class="product-description"><?php echo htmlspecialchars($product['description'] ?? ''); ?>
                            </p>
                            <span class="stock-status in-stock">Còn hàng</span>

                            <div class="product-actions">
                                <button class="product-btn btn-add-cart" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                                    <i class="fas fa-shopping-cart"></i> Thêm
                                </button>
                                <button class="btn-buy-now-detail" onclick="window.location.href='ThanhToan.php?id=<?php echo $product['id']; ?>'">
                                    <i class="fas fa-bolt"></i>
                                    Mua Ngay
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="view-all-btn">
                <button class="btn-custom btn-primary-custom" onclick="window.location.href='products.php'">
                    <i class="fas fa-arrow-right"></i>
                    Xem tất cả sản phẩm
                </button>
            </div>
        </div>
    </section>

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
    <script src="js/script.js"></script>
    <script src="js/categories.js"></script>
</body>

</html>