<?php
require_once "Database.php";
require_once "functions.php";
$db = new Database();
$conn = $db->connect();
$items = getallproduct($conn);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm - AthleteHub</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/products-page.css">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-custom">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dumbbell"></i>
                AthleteHub
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
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

                <div class="navbar-right d-flex align-items-center">
                    <div class="nav-notification">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </div>

                    <div class="cart-icon">
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

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="container-custom">
            <h1>Danh sách sản phẩm</h1>
            <p>Khám phá bộ sưu tập đồ thể thao chuyên nghiệp của chúng tôi</p>
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
                                <input type="text" id="searchInput" class="form-control" placeholder="Tìm sản phẩm...">
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
                                    <input type="checkbox" class="category-filter" value="all" checked>
                                    <span>Tất cả</span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="quan-ao">
                                    <span>Quần áo <span class="badge">45</span></span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="giay">
                                    <span>Giày <span class="badge">32</span></span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="thiet-bi">
                                    <span>Thiết bị <span class="badge">28</span></span>
                                </label>
                                <label class="filter-label">
                                    <input type="checkbox" class="category-filter" value="phu-kien">
                                    <span>Phụ kiện <span class="badge">56</span></span>
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
                                <input type="range" id="priceRange" min="0" max="2000000" value="2000000" class="form-range">
                                <div class="price-display">
                                    <span>Từ: <strong>0₫</strong></span>
                                    <span>Đến: <strong id="maxPrice">2.000.000₫</strong></span>
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
                                Hiển thị <strong id="showingCount">12</strong> sản phẩm
                            </span>
                        </div>
                        <div class="toolbar-right">
                            <label>Sắp xếp theo:</label>
                            <select id="sortBy" class="form-select sort-select">
                                <option value="popular">Phổ biến nhất</option>
                                <option value="newest">Mới nhất</option>
                                <option value="price-low">Giá: thấp đến cao</option>
                                <option value="price-high">Giá: cao đến thấp</option>
                                <option value="rating">Đánh giá cao nhất</option>
                                <option value="bestseller">Bán chạy nhất</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="products-grid-page" id="productsGrid">
                        <!-- Products will be inserted here by JavaScript -->
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

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-top">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">AthleteHub</h4>
                            <p style="color: #c0c0c0;">Chúng tôi cung cấp sản phẩm thể thao chất lượng cao.</p>
                            <div class="footer-socials">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
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
                            <h4 class="footer-title">Hỗ trợ</h4>
                            <ul class="footer-links">
                                <li><a href="#">Liên hệ</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">Chính sách</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên lạc</h4>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <p>+84 (0) 123 456 789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                 <div class="footer-copyright">
                        &copy;
                        <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
                    </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
    // Kiểm tra nếu chưa có thì mới gán, dùng 'var' hoặc gán thẳng vào 'window'
    window.allProducts = <?php echo json_encode($items); ?>;
</script>
    <script src="js/products-page.js"></script>
    
</body>

</html>