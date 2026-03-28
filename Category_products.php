<?php
session_start();
require_once 'model/functions.php';
require_once 'model/detail.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();

// Lấy ID từ URL
$id_danhmuc = isset($_GET['danh_muc_id']) ? (int)$_GET['danh_muc_id'] : 0;

// Xử lý filter từ URL
$price_max = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 2000000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Map danh mục
$categoryMap = [
    1 => ['key' => 'quan-ao', 'name' => 'Quần áo'],
    2 => ['key' => 'giay', 'name' => 'Giày'],
    3 => ['key' => 'thiet-bi', 'name' => 'Thiết bị'],
    4 => ['key' => 'phu-kien', 'name' => 'Phụ kiện']
];

if ($id_danhmuc > 0 && isset($categoryMap[$id_danhmuc])) {
    // Gọi hàm lấy sản phẩm theo danh mục
    $products = getProductsByCategory($conn, $id_danhmuc);

    // Xử lý sản phẩm
    $products = array_map(function ($p) use ($categoryMap, $id_danhmuc) {
        // Giữ nguyên các trường từ DB
        $p['name'] = $p['ten'];
        $p['category'] = $categoryMap[$id_danhmuc]['key'];
        $p['categoryLabel'] = $categoryMap[$id_danhmuc]['name'];
        $p['rating'] = floatval($p['trung_binh_sao']);
        $p['originalPrice'] = floatval($p['gia_goc']);
        $p['price'] = floatval($p['gia']);

        // Tính discount
        $p['discount'] = 0;
        if ($p['originalPrice'] > $p['price'] && $p['originalPrice'] > 0) {
            $p['discount'] = round((($p['originalPrice'] - $p['price']) / $p['originalPrice']) * 100);
        }

        return $p;
    }, $products);

    $categoryName = $categoryMap[$id_danhmuc]['name'];
    $categoryKey = $categoryMap[$id_danhmuc]['key'];
} else {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoryName; ?> - AthleteHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/products-page.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/product-detail.css">
</head>

<body>
    <!-- ========================
         NAVBAR
         ======================== -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-custom">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dumbbell"></i>
                AthleteHub
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
                        <a class="nav-link active" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#categories">Danh mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">Về chúng tôi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Liên hệ</a>
                    </li>
                </ul>

                <div class="navbar-search-container">
                    <div class="navbar-search">
                        <input type="text" placeholder="Tìm sản phẩm..." class="search-input">
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="navbar-right">
                    <div class="nav-notification">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </div>

                    <div class="cart-icon" onclick="window.location.href='cart.php'">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
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
    </nav>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="container-custom">
            <h1><?php echo $categoryName; ?></h1>
            <p>Khám phá bộ sưu tập <?php echo strtolower($categoryName); ?> thể thao chuyên nghiệp</p>
        </div>
    </div>

    <!-- PRODUCTS PAGE -->
    <section class="products-page">
        <div class="container-custom">
            <div class="row">
                <!-- Sidebar Filter -->
                <div class="col-lg-3">
                    <div class="sidebar-filters">
                        <!-- Search Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fas fa-search"></i>
                                Tìm kiếm
                            </h4>
                            <div class="search-box">
                                <input type="text" id="searchInput" class="form-control" placeholder="Tìm sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fas fa-tag"></i>
                                Danh mục
                            </h4>
                            <div class="filter-options">
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="all">
                                    <span>Tất cả</span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="quan-ao" <?php echo $categoryKey === 'quan-ao' ? 'checked' : ''; ?>>
                                    <span>Quần áo </span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="giay" <?php echo $categoryKey === 'giay' ? 'checked' : ''; ?>>
                                    <span>Giày </span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="thiet-bi" <?php echo $categoryKey === 'thiet-bi' ? 'checked' : ''; ?>>
                                    <span>Thiết bị </span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="phu-kien" <?php echo $categoryKey === 'phu-kien' ? 'checked' : ''; ?>>
                                    <span>Phụ kiện </span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fas fa-dollar-sign"></i>
                                Giá
                            </h4>
                            <div class="price-range">
                                <input type="range" id="priceRange" min="0" max="2000000" value="<?php echo $price_max; ?>" class="form-range">
                                <div class="price-display">
                                    <span>Từ: <strong>0₫</strong></span>
                                    <span>Đến: <strong id="maxPrice"><?php echo number_format($price_max, 0, ',', '.'); ?>₫</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Size Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fas fa-expand"></i>
                                Kích thước
                            </h4>
                            <div class="size-filters">
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="xs">
                                    <span>XS</span>
                                </label>
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="s">
                                    <span>S</span>
                                </label>
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="m">
                                    <span>M</span>
                                </label>
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="l">
                                    <span>L</span>
                                </label>
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="xl">
                                    <span>XL</span>
                                </label>
                                <label class="size-label">
                                    <input type="checkbox" class="size-filter" value="xxl">
                                    <span>XXL</span>
                                </label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-group">
                            <h4 class="filter-title">
                                <i class="fas fa-star"></i>
                                Đánh giá
                            </h4>
                            <div class="rating-filters">
                                <label class="rating-label">
                                    <input type="checkbox" class="rating-filter" value="5">
                                    <span>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        (15 sản phẩm)
                                    </span>
                                </label>
                                <label class="rating-label">
                                    <input type="checkbox" class="rating-filter" value="4">
                                    <span>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        (32 sản phẩm)
                                    </span>
                                </label>
                                <label class="rating-label">
                                    <input type="checkbox" class="rating-filter" value="3">
                                    <span>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        (45 sản phẩm)
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        <button class="btn-clear-filters" id="clearFilters">
                            <i class="fas fa-times"></i>
                            Xóa tất cả bộ lọc
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9">
                    <!-- Toolbar -->
                    <div class="products-toolbar">
                        <div class="toolbar-left">
                            <span class="products-count">
                                Hiển thị <strong id="showingCount"><?php echo count($products); ?></strong> sản phẩm
                            </span>
                        </div>
                        <div class="toolbar-right">
                            <label>Sắp xếp theo:</label>
                            <select id="sortBy" class="form-select sort-select">
                                <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Phổ biến nhất</option>
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Giá: thấp đến cao</option>
                                <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Giá: cao đến thấp</option>
                                <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Đánh giá cao nhất</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid - Hiển thị đầy đủ giống index.php -->
                    <div class="products-grid" id="productsGrid">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                                    <div class="product-image">
                                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="display: block;">
                                            <?php if (!empty($product['hinh_anh_chinh'])): ?>
                                                <img src="./public/<?php echo htmlspecialchars($product['hinh_anh_chinh']); ?>"
                                                    alt="<?php echo htmlspecialchars($product['ten']); ?>"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                <i class="fas fa-shirt" style="display:none;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-shirt"></i>
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
                                                <?php echo htmlspecialchars($product['ten']); ?>
                                            </a>
                                        </h3>

                                        <div class="rating-stars">
                                            <?php echo getStarRating($product['rating']); ?>
                                            <span class="rating-text">(<?php echo $product['so_luong_danh_gia'] ?? 0; ?>)</span>
                                        </div>
                                        <div class="product-price">
                                            <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
                                        </div>
                                        <p class="product-description"><?php echo htmlspecialchars($product['mo_ta'] ?? ''); ?></p>
                                        <span class="stock-status in-stock">Còn hàng</span>

                                        <div class="product-actions">
                                            <button class="product-btn btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
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
                        <?php else: ?>
                            <div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 50px;">
                                <i class="fas fa-search fa-3x"></i>
                                <h3>Không tìm thấy sản phẩm</h3>
                                <p>Vui lòng thay đổi bộ lọc của bạn</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-section">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#">
                                        <i class="fas fa-chevron-left"></i> Trước
                                    </a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">4</a></li>
                                <li class="page-item"><a class="page-link" href="#">
                                        Tiếp <i class="fas fa-chevron-right"></i>
                                    </a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container-custom">
            <div class="cta-content">
                <h2>Không tìm thấy sản phẩm bạn muốn?</h2>
                <p>Liên hệ với chúng tôi và chúng tôi sẽ giúp bạn tìm thấy sản phẩm phù hợp nhất</p>
                <button class="btn-contact-cta">
                    <i class="fas fa-envelope"></i>
                    Liên hệ ngay
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

    <!-- Inject products data for JavaScript -->
    <script>
        window.allProducts = <?php echo json_encode($products, JSON_UNESCAPED_UNICODE); ?>;
    </script>

    <!-- Custom JS -->
    <script src="js/cart.js"></script>
    <script src="js/script.js"></script>
    <script src="js/category-products.js"></script>
</body>

</html>