<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'model/detail.php';
require_once 'model/functions.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->connect();

// Lay id san pham tu URL
$id = (int) isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Su dung ham lay chi tiet san pham
$product = getProductDetail($conn, $id);

// Neu khong tim thay san pham, chuyen huong ve trang chu
if (!$product) {
    header("Location: index.php");
    exit();
}

// Lay danh gia
$reviews = getReviewsByProductId($conn, $id);

// Lay san pham lien quan
$relatedProducts = getRelatedProducts($conn, $product['danh_muc_id'], $id, 4);

// Xu ly hinh anh cho gallery
$thumbnails = $product['images'] ?? []; // Nếu không có ảnh thì trả về mảng rỗng
$mainImage = !empty($thumbnails) ? $thumbnails[0]['duong_dan'] : ($product['hinh_anh_chinh'] ?? '');

// Xu ly size va mau
$sizes = function_exists('getProductSizes') ? getProductSizes($conn, $id) : [];
$colors = function_exists('getProductColors') ? getProductColors($conn, $id) : [];

// Xu ly gia tri mac dinh (Chống lỗi văng trang)
$gia = $product['gia'] ?? 0;
$gia_goc = $product['gia_goc'] ?? 0;
$discountPercent = $product['discount_percent'] ?? 0;
$savings = $product['savings'] ?? 0;
$danh_muc = $product['ten_danh_muc'] ?? 'Danh mục';
$categoryKey = $product['category_key'] ?? '';

// Rating summary
$ratingSummary = $product['rating_summary'];

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
        .bar {
            width: 200px;
            height: 10px;
            background: #eee;
            border-radius: 5px;
            display: inline-block;
        }

        .fill {
            height: 100%;
            background: #ffc107;
            /* Màu vàng sao */
            border-radius: 5px;
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

                    <div class="cart-icon" onclick="window.location.href='cart.php'">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"></span>
                    </div>

                    <div class="user-account-wrapper d-flex align-items-center">
                        <div class="user-action-dropdown dropdown">
                            <a href="#" class="user-icon-link me-2 text-decoration-none dropdown-toggle" id="userMenu"
                                data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                                <i class="fas fa-user-circle fa-lg"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                <?php if (isset($_SESSION['user_name'])): ?>
                                    <li>
                                        <h6 class="dropdown-header"> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                        </h6>
                                    </li>
                                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i> Hồ
                                            sơ của tôi</a></li>
                                    <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>
                                            Đơn hàng đã mua</a></li>
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
    </nav>

    <!-- BREADCRUMB -->
    <div class="breadcrumb-section">
        <div class="container-custom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Sản phẩm</a></li>
                    <li class="breadcrumb-item"><a
                            href="products.php?category=<?php echo $categoryKey; ?>"><?php echo htmlspecialchars($danh_muc); ?></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php echo htmlspecialchars($product['ten']); ?>
                    </li>
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
                            <img id="mainImage" src="public/<?php echo htmlspecialchars($mainImage); ?>"
                                alt="<?php echo htmlspecialchars($product['ten']); ?>">
                            <?php if ($discountPercent > 0): ?>
                                <span class="product-badge-detail sale">-<?php echo $discountPercent; ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="thumbnail-images">
                            <?php if (!empty($thumbnails)): ?>
                                <?php foreach ($thumbnails as $index => $img): ?>
                                    <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>"
                                        onclick="changeImage(this)"
                                        data-src="public/<?php echo htmlspecialchars($img['duong_dan']); ?>">
                                        <img src="public/<?php echo htmlspecialchars($img['duong_dan']); ?>"
                                            alt="View <?php echo $index + 1; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="thumbnail-item active" onclick="changeImage(this)">
                                    <img src="public/<?php echo htmlspecialchars($product['hinh_anh_chinh']); ?>"
                                        alt="View 1">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-detail-info">
                        <!-- Category & Rating -->
                        <div class="detail-header">
                            <span class="product-category"><?php echo htmlspecialchars($danh_muc); ?></span>
                            <div class="rating-group">
                                <div class="stars">
                                    <?php echo getStarRating($ratingSummary['average_rating']); ?>
                                </div>
                                <span class="rating-count">(<?php echo $ratingSummary['total_reviews']; ?> đánh
                                    giá)</span>
                            </div>
                        </div>

                        <!-- Title & Price -->
                        <h1 class="detail-title"><?php echo htmlspecialchars($product['ten']); ?></h1>
                        <p class="detail-description"><?php echo htmlspecialchars($product['mo_ta']); ?></p>

                        <!-- Price Section -->
                        <div class="detail-price-section">
                            <div class="price-group">
                                <span class="price-current"><?php echo $product['gia_formatted']; ?></span>
                                <span class="price-original"><?php echo $product['gia_goc_formatted']; ?></span>
                                <?php if ($discountPercent > 0): ?>
                                    <span class="price-discount">-<?php echo $discountPercent; ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="price-info">
                                <p>Tiết kiệm: <strong><?php echo $product['savings_formatted']; ?></strong></p>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="stock-section">
                            <?php if ($product['total_stock'] > 0): ?>
                                <span class="stock-status-badge in-stock">
                                    <i class="fas fa-check-circle"></i> Còn hàng (<?php echo $product['total_stock']; ?> sản
                                    phẩm)
                                </span>
                                <?php if ($product['total_stock'] < 20): ?>
                                    <span class="stock-warning">Chỉ còn <?php echo $product['total_stock']; ?> sản phẩm với giá
                                        này!</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="stock-status-badge out-of-stock">
                                    <i class="fas fa-times-circle"></i> Hết hàng
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Product Options -->
                        <div class="product-options">
                            <?php if (!empty($sizes) || !empty($colors)): ?>
                                <!-- Size -->
                                <?php if (!empty($sizes)): ?>
                                    <div class="option-group">
                                        <label class="option-label">Size:</label>
                                        <div class="size-options" id="sizeOptions">
                                            <?php foreach ($sizes as $size): ?>
                                                <button type="button" class="size-btn" data-size-id="<?php echo $size['id']; ?>"
                                                    data-size-name="<?php echo htmlspecialchars($size['ten']); ?>">
                                                    <?php echo htmlspecialchars($size['ten']); ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Color -->
                                <?php if (!empty($colors)): ?>
                                    <div class="option-group">
                                        <label class="option-label">Màu sắc:</label>
                                        <div class="color-options" id="colorOptions">
                                            <?php foreach ($colors as $color): ?>
                                                <button type="button" class="color-btn" data-color-id="<?php echo $color['id']; ?>"
                                                    data-color-name="<?php echo htmlspecialchars($color['ten']); ?>"
                                                    style="background-color: <?php echo htmlspecialchars($color['ma_hex']); ?>;"
                                                    title="<?php echo htmlspecialchars($color['ten']); ?>">
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Quantity -->
                            <div class="option-group">
                                <label class="option-label">Số lượng:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" onclick="decreaseQty()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" class="qty-input" value="1" min="1"
                                        max="<?php echo $product['total_stock']; ?>">
                                    <button class="qty-btn" onclick="increaseQty()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="detail-actions">
                            <button class="btn-add-to-cart-detail" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                Thêm vào giỏ hàng
                            </button>
                            <button class="btn-buy-now-detail">
                                <i class="fas fa-bolt"></i>
                                Mua ngay
                            </button>
                            <button class="btn-wishlist-detail" id="wishlistBtn"
                                data-product-id="<?php echo $product['id']; ?>">
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
                            Đánh giá (<?php echo $ratingSummary['total_reviews']; ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Description Tab -->
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <div class="tab-content-body">
                            <h3>Mô tả sản phẩm</h3>
                            <?php if (!empty($product['mo_ta_chi_tiet'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($product['mo_ta_chi_tiet'])); ?></p>
                            <?php elseif (!empty($product['mo_ta'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($product['mo_ta'])); ?></p>
                            <?php else: ?>
                                <p>Chưa có mô tả chi tiết cho sản phẩm này.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Specifications Tab -->
                    <div class="tab-pane fade" id="specifications" role="tabpanel">
                        <div class="tab-content-body">
                            <h3>Thông số kỹ thuật</h3>
                            <?php if (!empty($product['specifications'])): ?>
                                <table class="specs-table">
                                    <?php foreach ($product['specifications'] as $spec): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($spec['ten_thong_so']); ?></td>
                                            <td><?php echo htmlspecialchars($spec['gia_tri']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php else: ?>
                                <p>Chưa có thông số kỹ thuật cho sản phẩm này.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Reviews Tab -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="tab-content-body">
                            <div class="reviews-container">
                                <div class="reviews-summary">
                                    <div class="rating-box">
                                        <div class="rating-number">
                                            <?php echo number_format($ratingSummary['average_rating'] ?? 0, 1); ?>
                                        </div>
                                        <div class="rating-stars">
                                            <?php echo getStarRating($ratingSummary['average_rating']); ?>
                                        </div>
                                        <p>Dựa trên <?php echo $ratingSummary['total_reviews']; ?> đánh giá</p>
                                    </div>

                                    <div class="rating-breakdown">
                                        <?php for ($i = 5; $i >= 1; $i--):
                                            $dist = $ratingSummary['rating_distribution'][$i] ?? ['count' => 0, 'percentage' => 0];
                                            ?>
                                            <div class="rating-bar">
                                                <span class="rating-label"><?php echo $i; ?> <i
                                                        class="fas fa-star"></i></span>
                                                <div class="bar">
                                                    <div class="fill" style="width: <?php echo $dist['percentage']; ?>%;">
                                                    </div>
                                                </div>
                                                <span class="rating-count"><?php echo $dist['count']; ?></span>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="write-review">
                                    <h4>Viết đánh giá của bạn</h4>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form class="review-form">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <div class="form-group">
                                                <label>Đánh giá:</label>
                                                <div class="rating-input"
                                                    style="font-size: 28px; cursor: pointer; gap: 8px; display: flex;">
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Tiêu đề:</label>
                                                <input type="text" class="form-control" name="title"
                                                    placeholder="Viết tiêu đề đánh giá..." required minlength="5">
                                            </div>
                                            <div class="form-group">
                                                <label>Nội dung:</label>
                                                <textarea class="form-control" name="content" rows="4"
                                                    placeholder="Chia sẻ kinh nghiệm của bạn..." required
                                                    minlength="10"></textarea>
                                            </div>
                                            <button type="submit" class="btn-submit-review">Gửi đánh giá</button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Vui lòng <a href="login.php">đăng nhập</a> để
                                            viết đánh giá
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="reviews-list">
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
                    <?php if (!empty($relatedProducts)): ?>
                        <?php foreach ($relatedProducts as $related): ?>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="product-card-small">
                                    <div class="product-image-small">
                                        <?php if (!empty($related['hinh_anh_chinh'])): ?>
                                            <img src="public/<?php echo htmlspecialchars($related['hinh_anh_chinh']); ?>"
                                                alt="<?php echo htmlspecialchars($related['ten']); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-shirt"></i>
                                        <?php endif; ?>
                                        <?php if ($related['discount_percent'] > 0): ?>
                                            <span class="product-badge">-<?php echo $related['discount_percent']; ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info-small">
                                        <h4><?php echo htmlspecialchars($related['ten']); ?></h4>
                                        <div class="price-small">
                                            <span class="price"><?php echo $related['gia_formatted']; ?></span>
                                            <?php if ($related['discount_percent'] > 0): ?>
                                                <span class="price-old"><?php echo $related['gia_goc_formatted']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="rating-small">
                                            <?php echo $related['star_rating']; ?>
                                            <span>(<?php echo $related['so_luong_danh_gia']; ?>)</span>
                                        </div>
                                        <a href="product-detail.php?id=<?php echo $related['id']; ?>"
                                            class="btn-add-to-cart-detail" role="button">
                                            <i class="fas fa-shopping-cart"></i>
                                            Xem chi tiết
                                        </a>
                                        <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn-buy-now-detail"
                                            role="button">
                                            <i class="fas fa-bolt"></i>
                                            Mua ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center">Không có sản phẩm liên quan.</p>
                        </div>
                    <?php endif; ?>
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
                <div class="footer-copyright">
                    &copy;
                    <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set current user info for JavaScript
        window.currentUserId = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'null'; ?>;
        window.userRole = '<?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'user'; ?>';
    </script>
    <script src="js/cart.js"></script>
    <script src="js/script.js"></script>
    <script src="js/product-detail.js"></script>
    <script src="js/review.js"></script>
</body>

</html>