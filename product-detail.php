<?php
require_once 'functions.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();
// lay id san pham tu url 
$id = (int) isset($_GET['id']) ? (int) $_GET['id'] : 0;

$product = getProductById($conn, $id);

// neu khong tim thay sam pham, chuyen huong ve trang chu 
if (!$product) {
    header("Location: index.html");
    exit();
}

// goi ham danh gia
$reviews = getReviewsByProductId($conn, $id);

// xu ly cac bien hien thi
$gia = $product['gia'];
$gia_goc = $product['gia_goc'];
$tiet_kiem = $gia_goc - $gia;
$phan_tram_giam = $gia_goc > 0 ? round((($gia_goc - $gia) / $gia_goc) * 100) : 0;
$danh_muc = function_exists('getCategoryKey') ? getCategoryKey($product['danh_muc_id']) : 'Danh mục';

// lay san pham co lien quan 
$all_products = function_exists('getallproduct') ? getallproduct($conn) : [];
$related_products = array_filter($all_products, function ($p) use ($id, $danh_muc) {
    return isset($p['category']) && $p['category'] === $danh_muc && $p['id'] != $id;
});
$related_products = array_slice($related_products, 0, 4); // Chỉ lấy 4 sản phẩm

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - AthleteHub</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/product-detail.css">
    <style>
        .product-tabs-section .nav-tabs {
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        .product-tabs-section .nav-link {
            color: #555;
            font-weight: 600;
            border: none;
            padding: 12px 20px;
        }

        .product-tabs-section .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background: none;
        }

        .tab-content-body {
            padding: 20px 0;
            line-height: 1.8;
            color: #444;
        }

        .review-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .review-author {
            font-weight: bold;
            color: #333;
        }

        .review-date {
            font-size: 0.85em;
            color: #888;
        }

        .review-stars {
            color: #ffc107;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
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
                        <a class="nav-link" href="#about">Về chúng tôi</a>
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

    <!-- BREADCRUMB -->
    <div class="breadcrumb-section">
        <div class="container-custom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="products.html">Sản phẩm</a></li>
                    <li class="breadcrumb-item"><a href="#">Quần áo</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Áo tập Pro Performance</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- PRODUCT DETAIL SECTION -->
    <section class="product-detail-section">
        <div class="container-custom">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img id="mainImage" src="https://via.placeholder.com/500x600?text=Áo+Tập+Pro"
                                alt="Áo tập Pro Performance">
                            <span class="product-badge-detail sale">-30%</span>
                        </div>
                        <div class="thumbnail-images">
                            <div class="thumbnail-item active" onclick="changeImage(this)">
                                <img src="https://via.placeholder.com/80x100?text=View+1" alt="View 1">
                            </div>
                            <div class="thumbnail-item" onclick="changeImage(this)">
                                <img src="https://via.placeholder.com/80x100?text=View+2" alt="View 2">
                            </div>
                            <div class="thumbnail-item" onclick="changeImage(this)">
                                <img src="https://via.placeholder.com/80x100?text=View+3" alt="View 3">
                            </div>
                            <div class="thumbnail-item" onclick="changeImage(this)">
                                <img src="https://via.placeholder.com/80x100?text=View+4" alt="View 4">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-detail-info">
                        <!-- Category & Rating -->
                        <div class="detail-header">
                            <span class="product-category">Quần áo</span>
                            <div class="rating-group">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half"></i>
                                </div>
                                <span class="rating-count">(120 đánh giá)</span>
                            </div>
                        </div>

                        <!-- Title & Price -->
                        <h1 class="detail-title">Áo tập Pro Performance</h1>
                        <p class="detail-description">Áo tập luyện cao cấp với công nghệ hút ẩm tiên tiến, giúp bạn
                            thoải mái trong mọi bài tập thể dục.</p>

                        <!-- Price Section -->
                        <div class="detail-price-section">
                            <div class="price-group">
                                <span class="price-current">299.000₫</span>
                                <span class="price-original">429.000₫</span>
                                <span class="price-discount">-30%</span>
                            </div>
                            <div class="price-info">
                                <p>Tiết kiệm: <strong>130.000₫</strong></p>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="stock-section">
                            <span class="stock-status-badge in-stock">
                                <i class="fas fa-check-circle"></i> Còn hàng (450 sản phẩm)
                            </span>
                            <span class="stock-warning">Chỉ còn 15 sản phẩm với giá này!</span>
                        </div>

                        <!-- Product Options -->
                        <div class="product-options">
                            <!-- Size -->
                            <div class="option-group">
                                <label class="option-label">Size:</label>
                                <div class="size-options">
                                    <button class="size-btn">XS</button>
                                    <button class="size-btn active">S</button>
                                    <button class="size-btn">M</button>
                                    <button class="size-btn">L</button>
                                    <button class="size-btn">XL</button>
                                    <button class="size-btn">XXL</button>
                                </div>
                            </div>

                            <!-- Color -->
                            <div class="option-group">
                                <label class="option-label">Màu sắc:</label>
                                <div class="color-options">
                                    <div class="color-btn active" style="background-color: #000;" title="Đen"></div>
                                    <div class="color-btn" style="background-color: #1a73e8;" title="Xanh"></div>
                                    <div class="color-btn" style="background-color: #ff0000;" title="Đỏ"></div>
                                    <div class="color-btn" style="background-color: #ffd700;" title="Vàng"></div>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="option-group">
                                <label class="option-label">Số lượng:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" onclick="decreaseQty()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" class="qty-input" value="1" min="1" max="100">
                                    <button class="qty-btn" onclick="increaseQty()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="detail-actions">
                            <button class="btn-add-to-cart-detail">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm vào giỏ hàng
                            </button>
                            <button class="btn-buy-now-detail">
                                <i class="fas fa-bolt"></i>
                                Mua ngay
                            </button>
                            <button class="btn-wishlist-detail" id="wishlistBtn">
                                <i class="far fa-heart"></i>
                                <span>Yêu thích</span>
                            </button>
                        </div>

                        <!-- Shipping Info -->
                        <div class="shipping-info">
                            <div class="shipping-item">
                                <i class="fas fa-truck"></i>
                                <div>
                                    <h4>Giao hàng miễn phí</h4>
                                    <p>Đơn hàng trên 500.000₫</p>
                                </div>
                            </div>
                            <div class="shipping-item">
                                <i class="fas fa-undo"></i>
                                <div>
                                    <h4>Trả hàng 30 ngày</h4>
                                    <p>Không yêu cầu câu hỏi</p>
                                </div>
                            </div>
                            <div class="shipping-item">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <h4>Bảo mật thanh toán</h4>
                                    <p>Bảo vệ mọi giao dịch</p>
                                </div>
                            </div>
                        </div>

                        <!-- Share -->
                        <div class="detail-share">
                            <span>Chia sẻ:</span>
                            <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="share-btn"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="share-btn"><i class="fab fa-pinterest"></i></a>
                            <a href="#" class="share-btn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="product-tabs-section">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button">
                            Mô tả sản phẩm
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab"
                            data-bs-target="#specifications" type="button">
                            Thông số kỹ thuật
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                            type="button">
                            Đánh giá (120)
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Description Tab -->
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <div class="tab-content-body">
                            <h3>Mô tả sản phẩm</h3>
                            <p>Áo tập Pro Performance được thiết kế dành cho những người yêu thích hoạt động thể thao.
                                Với công nghệ hút ẩm tiên tiến, sản phẩm giúp bạn luôn thoải mái ngay cả trong những bài
                                tập cơ bắp.</p>

                            <h4>Đặc điểm nổi bật:</h4>
                            <ul class="feature-list">
                                <li>Vải co giãn 4 chiều, thoáng khí</li>
                                <li>Công nghệ hút ẩm nhanh khô</li>
                                <li>Thiết kế ergonomic, thoải mái vận động</li>
                                <li>Đường may chắc chắn, bền lâu</li>
                                <li>In ấn chất lượng cao, không phai</li>
                                <li>Phù hợp mọi mùa, mọi điều kiện thời tiết</li>
                            </ul>

                            <h4>Hướng dẫn chăm sóc:</h4>
                            <ul class="care-list">
                                <li>Giặt tay bằng nước mát, không sử dụng nước nóng</li>
                                <li>Không giặt máy, không tẩy</li>
                                <li>Không là với nhiệt độ cao</li>
                                <li>Phơi trong bóng mát</li>
                                <li>Không ủi khi còn ẩm</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specifications" role="tabpanel">
                        <div class="tab-content-body">
                            <h3>Thông số kỹ thuật</h3>
                            <table class="specs-table">
                                <tr>
                                    <td>Chất liệu</td>
                                    <td>85% Polyester, 15% Spandex</td>
                                </tr>
                                <tr>
                                    <td>Màu sắc</td>
                                    <td>Đen, Xanh, Đỏ, Vàng</td>
                                </tr>
                                <tr>
                                    <td>Cân nặng</td>
                                    <td>180g ± 10g</td>
                                </tr>
                                <tr>
                                    <td>Kích thước có sẵn</td>
                                    <td>XS, S, M, L, XL, XXL</td>
                                </tr>
                                <tr>
                                    <td>Độ dành cho</td>
                                    <td>Nam & Nữ</td>
                                </tr>
                                <tr>
                                    <td>Xuất xứ</td>
                                    <td>Việt Nam</td>
                                </tr>
                                <tr>
                                    <td>Bảo hành</td>
                                    <td>12 tháng</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="tab-content-body">
                            <div class="reviews-container">
                                <div class="reviews-summary">
                                    <div class="rating-box">
                                        <div class="rating-number">4.8</div>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half"></i>
                                        </div>
                                        <p>Dựa trên 120 đánh giá</p>
                                    </div>

                                    <div class="rating-breakdown">
                                        <div class="rating-bar">
                                            <span class="rating-label">5 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="fill" style="width: 70%;"></div>
                                            </div>
                                            <span class="rating-count">84</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">4 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="fill" style="width: 20%;"></div>
                                            </div>
                                            <span class="rating-count">24</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">3 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="fill" style="width: 7%;"></div>
                                            </div>
                                            <span class="rating-count">8</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">2 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="fill" style="width: 2%;"></div>
                                            </div>
                                            <span class="rating-count">2</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">1 <i class="fas fa-star"></i></span>
                                            <div class="bar">
                                                <div class="fill" style="width: 1%;"></div>
                                            </div>
                                            <span class="rating-count">2</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="write-review">
                                    <h4>Viết đánh giá của bạn</h4>
                                    <form class="review-form">
                                        <div class="form-group">
                                            <label>Đánh giá:</label>
                                            <div class="rating-input">
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Tiêu đề:</label>
                                            <input type="text" class="form-control"
                                                placeholder="Viết tiêu đề đánh giá...">
                                        </div>
                                        <div class="form-group">
                                            <label>Nội dung:</label>
                                            <textarea class="form-control" rows="4"
                                                placeholder="Chia sẻ kinh nghiệm của bạn..."></textarea>
                                        </div>
                                        <button type="submit" class="btn-submit-review">Gửi đánh giá</button>
                                    </form>
                                </div>

                                <div class="reviews-list">
                                    <h4>Đánh giá từ khách hàng</h4>

                                    <!-- Review Item 1 -->
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <img src="https://via.placeholder.com/40?text=Avatar" alt="Avatar"
                                                    class="reviewer-avatar">
                                                <div>
                                                    <h5>Nguyễn Văn A</h5>
                                                    <p class="review-date">5 sao - 3 tuần trước</p>
                                                </div>
                                            </div>
                                            <span class="verified-badge">
                                                <i class="fas fa-check-circle"></i> Đã xác minh
                                            </span>
                                        </div>
                                        <h5 class="review-title">Sản phẩm rất tốt, thoái mái khi mặc</h5>
                                        <p class="review-content">Áo rất chất lượng, vải co giãn tốt, thoáng khí. Tôi đã
                                            mặc để chạy bộ và cảm thấy rất thoải mái. Chắc chắn sẽ mua lại!</p>
                                        <div class="review-actions">
                                            <button class="helpful-btn">
                                                <i class="fas fa-thumbs-up"></i> Hữu ích (12)
                                            </button>
                                            <button class="unhelpful-btn">
                                                <i class="fas fa-thumbs-down"></i> Không hữu ích (1)
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Review Item 2 -->
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-info">
                                                <img src="https://via.placeholder.com/40?text=Avatar" alt="Avatar"
                                                    class="reviewer-avatar">
                                                <div>
                                                    <h5>Trần Thị B</h5>
                                                    <p class="review-date">4 sao - 1 tháng trước</p>
                                                </div>
                                            </div>
                                            <span class="verified-badge">
                                                <i class="fas fa-check-circle"></i> Đã xác minh
                                            </span>
                                        </div>
                                        <h5 class="review-title">Tốt nhưng hơi rộng</h5>
                                        <p class="review-content">Áo chất lượng tốt, nhưng hơi rộng so với mong đợi. Bạn
                                            nên mua size nhỏ hơn 1 size. Nhưng nhìn chung sản phẩm rất xứng đáng.</p>
                                        <div class="review-actions">
                                            <button class="helpful-btn">
                                                <i class="fas fa-thumbs-up"></i> Hữu ích (8)
                                            </button>
                                            <button class="unhelpful-btn">
                                                <i class="fas fa-thumbs-down"></i> Không hữu ích (0)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="related-products-section">
                <h2 class="section-title">
                    <span>Sản phẩm liên quan</span>
                </h2>

                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product-card-small">
                            <div class="product-image-small">
                                <i class="fas fa-shirt"></i>
                            </div>
                            <div class="product-info-small">
                                <h4>Áo tập Casual</h4>
                                <div class="price-small">
                                    <span class="price">199.000₫</span>
                                </div>
                                <div class="rating-small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half"></i>
                                    <span>(85)</span>
                                </div>
                                <button class="btn-add-quick">Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product-card-small">
                            <div class="product-image-small">
                                <i class="fas fa-shirt"></i>
                            </div>
                            <div class="product-info-small">
                                <h4>Áo tập Marathon</h4>
                                <div class="price-small">
                                    <span class="price">249.000₫</span>
                                </div>
                                <div class="rating-small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <span>(56)</span>
                                </div>
                                <button class="btn-add-quick">Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product-card-small">
                            <div class="product-image-small">
                                <i class="fas fa-shirt"></i>
                            </div>
                            <div class="product-info-small">
                                <h4>Áo tập Mesh</h4>
                                <div class="price-small">
                                    <span class="price">279.000₫</span>
                                </div>
                                <div class="rating-small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span>(102)</span>
                                </div>
                                <button class="btn-add-quick">Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="product-card-small">
                            <div class="product-image-small">
                                <i class="fas fa-shirt"></i>
                            </div>
                            <div class="product-info-small">
                                <h4>Áo tập Premium</h4>
                                <div class="price-small">
                                    <span class="price">349.000₫</span>
                                </div>
                                <div class="rating-small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half"></i>
                                    <span>(78)</span>
                                </div>
                                <button class="btn-add-quick">Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <p style="color: #c0c0c0;">Chúng tôi cung cấp sản phẩm thể thao chất lượng cao với giá cạnh
                                tranh tốt nhất.</p>
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
                                <li><a href="#">Trang chủ</a></li>
                                <li><a href="#">Sản phẩm</a></li>
                                <li><a href="#">Danh mục</a></li>
                                <li><a href="#">Về chúng tôi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Hỗ trợ</h4>
                            <ul class="footer-links">
                                <li><a href="#">Liên hệ chúng tôi</a></li>
                                <li><a href="#">Chính sách giao hàng</a></li>
                                <li><a href="#">Chính sách hoàn trả</a></li>
                                <li><a href="#">FAQ</a></li>
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
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <p>support@athletehub.vn</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">&copy; 2024 <strong>AthleteHub</strong>. Bảo lưu mọi quyền.</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/product-detail.js"></script>
</body>

</html>