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
                        <a class="nav-link active" href="index1.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Sản phẩm</a>
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
                    
                    <div class="cart-icon" onclick="window.location.href='cart.html'">
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

    <!-- ========================
         HERO SECTION MỚI
         ======================== -->
    <section class="hero" id="home">
        <div class="container-custom">
            <div class="row align-items-center">
                <!-- Hero Image (Left) -->
                <div class="col-lg-6 col-md-6">
                    <div class="hero-image hero-image-left">
                        <img src="https://via.placeholder.com/450x550?text=Đồ+Thể+Thao" alt="Đồ thể thao chuyên nghiệp" class="hero-img">
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
                        <p>Khám phá bộ sưu tập đồ thể thao chuyên nghiệp với công nghệ tối tân. Từ quần áo tập luyện đến thiết bị thể dục, chúng tôi có mọi thứ bạn cần để đạt được mục tiêu của mình.</p>
                        
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
                        <a href="#quanao" class="dropdown-cat-item">
                            <i class="fas fa-person-running"></i>
                            <span>Quần áo</span>
                            <span class="badge">200+</span>
                        </a>
                        <a href="#giay" class="dropdown-cat-item">
                            <i class="fas fa-shoe-prints"></i>
                            <span>Giày</span>
                            <span class="badge">150+</span>
                        </a>
                        <a href="#thietbi" class="dropdown-cat-item">
                            <i class="fas fa-dumbbell"></i>
                            <span>Thiết bị</span>
                            <span class="badge">300+</span>
                        </a>
                        <a href="#phukien" class="dropdown-cat-item">
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
                    <div class="category-card" id="quanao" data-category="quanao">
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
                    <div class="category-card" id="giay" data-category="giay">
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
                    <div class="category-card" id="thietbi" data-category="thietbi">
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
                    <div class="category-card" id="phukien" data-category="phukien">
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
                <!-- Product 1 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-shirt"></i>
                        <span class="product-badge sale">-30%</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.8
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Quần áo</div>
                        <h3 class="product-name">Áo tập Pro Performance</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half"></i>
                            <span class="rating-text">(120)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">299.000₫</span>
                            <span class="price-original">429.000₫</span>
                            <span class="price-discount">-30%</span>
                        </div>
                        <p class="product-description">Áo tập luyện cao cấp với công nghệ hút ẩm tiên tiến</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-shoe-prints"></i>
                        <span class="product-badge new">Mới</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.9
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Giày</div>
                        <h3 class="product-name">Giày chạy Elite Runner</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(95)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">1.299.000₫</span>
                            <span class="price-original">1.599.000₫</span>
                            <span class="price-discount">-19%</span>
                        </div>
                        <p class="product-description">Giày chạy nhẹ với công nghệ đệm chân tối ưu</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-dumbbell"></i>
                        <span class="product-badge">Hot</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.7
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Thiết bị</div>
                        <h3 class="product-name">Tạ tay đôi 10KG</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half"></i>
                            <span class="rating-text">(78)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">499.000₫</span>
                        </div>
                        <p class="product-description">Tạ tay chắc chắn với bề mặt cao su chống trượt</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-glasses"></i>
                        <span class="product-badge sale">-15%</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.6
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Phụ kiện</div>
                        <h3 class="product-name">Kính bảo vệ UV</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span class="rating-text">(64)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">299.000₫</span>
                            <span class="price-original">349.000₫</span>
                            <span class="price-discount">-15%</span>
                        </div>
                        <p class="product-description">Kính chống UV 100% cho hoạt động ngoài trời</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 5 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-water"></i>
                        <span class="product-badge new">Mới</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.9
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Phụ kiện</div>
                        <h3 class="product-name">Bình nước thể thao 1L</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span class="rating-text">(112)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">149.000₫</span>
                        </div>
                        <p class="product-description">Bình nước giữ nhiệt tốt, không chứa BPA</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product 6 -->
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-person-biking"></i>
                        <span class="product-badge sale">-25%</span>
                        <span class="product-rating">
                            <i class="fas fa-star"></i>
                            4.8
                        </span>
                        <button class="btn-quick-view">Xem nhanh</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Quần áo</div>
                        <h3 class="product-name">Quần tập thể thao</h3>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half"></i>
                            <span class="rating-text">(88)</span>
                        </div>
                        <div class="product-price">
                            <span class="price-current">349.000₫</span>
                            <span class="price-original">499.000₫</span>
                            <span class="price-discount">-25%</span>
                        </div>
                        <p class="product-description">Quần tập vải co giãn 4 chiều thoải mái</p>
                        <span class="stock-status in-stock">Còn hàng</span>
                        <div class="product-actions">
                            <button class="product-btn btn-add-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm
                            </button>
                            <button class="product-btn btn-wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="view-all-btn" >
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
                            <p style="color: #c0c0c0; margin-bottom: 20px;">Chúng tôi cung cấp sản phẩm thể thao chất lượng cao với giá cạnh tranh tốt nhất trên thị trường.</p>
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
                                <li><a href="#"><i class="fas fa-angle-right"></i>Trang chủ</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Sản phẩm</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Danh mục</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Về chúng tôi</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Blog</a></li>
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
                        <p>Nhận ưu đãi độc quyền, cập nhật sản phẩm mới và nhiều hơn nữa trực tiếp vào hộp thư của bạn.</p>
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
                        &copy; 2024 <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
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
</body>
</html>