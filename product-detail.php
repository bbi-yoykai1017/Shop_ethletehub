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
        /* ================= CHAT POPUP ================= */
        .chat-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 340px;
            height: 480px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            z-index: 9999;
            animation: fadeInUp 0.3s ease;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ================= HEADER ================= */
        .chat-header {
            background: linear-gradient(135deg, #0084ff, #00c6ff);
            color: white;
            padding: 12px 15px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header button {
            background: transparent;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        /* ================= CHAT CONTENT ================= */
        .chat-content {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background: #f5f7fb;
        }

        /* Scroll đẹp */
        .chat-content::-webkit-scrollbar {
            width: 6px;
        }

        .chat-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* ================= MESSAGE ================= */
        .user-msg {
            background: #ff4d4f;
            color: #fff;
            padding: 8px 12px;
            border-radius: 15px 15px 0 15px;
            margin: 6px 0;
            max-width: 75%;
            margin-left: auto;
            font-size: 13px;
        }

        .bot-msg {
            background: #fff;
            padding: 8px 12px;
            border-radius: 15px 15px 15px 0;
            margin: 6px 0;
            max-width: 75%;
            border: 1px solid #eee;
            font-size: 13px;
        }

        /* ================= PRODUCT CARD ================= */
        .product-card-chat {
            display: flex;
            gap: 10px;
            padding: 10px;
            border-top: 1px solid #eee;
            background: #fff;
            align-items: center;
        }

        .product-card-chat img {
            width: 55px;
            height: 55px;
            border-radius: 10px;
            object-fit: cover;
        }

        .product-card-chat h6 {
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        .product-card-chat .price {
            color: #ff2d55;
            font-weight: bold;
            font-size: 13px;
        }

        /* ================= FOOTER ================= */
        .chat-footer {
            display: flex;
            gap: 8px;
            padding: 10px;
            border-top: 1px solid #eee;
            background: #fff;
        }

        .chat-footer input {
            flex: 1;
            border: none;
            background: #f1f1f1;
            border-radius: 20px;
            padding: 8px 14px;
            font-size: 13px;
            outline: none;
        }

        .chat-footer button {
            background: #ff2d55;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.2s;
        }

        .chat-footer button:hover {
            background: #e60023;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 480px) {
            .chat-popup {
                width: 90%;
                right: 5%;
                height: 70vh;
            }
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
                    <div class="nav-notification" onclick="window.location.href='news.php'">
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
                                            Đơn hàng </a></li>
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
                            <button class="btn-chat-auto" style="border-radius: 15px; background-color: white; color: #0084ff;" id="chatBtn">
                                <i class="fas fa-comments"></i>
                                <span>Chat</span>
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
                                        <a href="ThanhToan.php?id=<?php echo $related['id']; ?>" class="btn-buy-now-detail"
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
                                <li><a href="about.php">Liên hệ chúng tôi</a></li>
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
        window.userRole = '<?php echo isset($_SESSION['user_role']) ? htmlspecialchars($_SESSION['user_role']) : 'user'; ?>';
    </script>
    <script src="js/cart.js"></script>
    <script src="js/script.js"></script>
    <script src="js/product-detail.js"></script>


    <script src="js/review.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatBtn = document.getElementById("chatBtn");
            const chatPopup = document.getElementById("chatPopup");

            if (chatBtn && chatPopup) {
                chatBtn.addEventListener("click", () => {
                    chatPopup.style.display = "block";
                });
            }
        });

        function closeChat() {
            document.getElementById("chatPopup").style.display = "none";
        }

        function sendMessage() {
            const input = document.getElementById("chatInput");
            const chatContent = document.getElementById("chatContent");

            const msg = input.value.trim();
            if (!msg) return;

            // Hiện tin nhắn người dùng
            const userMsg = document.createElement("div");
            userMsg.className = "user-msg";
            userMsg.innerText = msg;
            chatContent.appendChild(userMsg);

            // Xóa input
            input.value = "";

            // Giả lập bot trả lời 1 câu duy nhất
            setTimeout(() => {
                const botMsg = document.createElement("div");
                botMsg.className = "bot-msg";
                botMsg.innerText = "Shop sẽ phản hồi bạn sớm nhất nhé! 😊";
                chatContent.appendChild(botMsg);

                chatContent.scrollTop = chatContent.scrollHeight;
            }, 500);

            // Scroll xuống
            chatContent.scrollTop = chatContent.scrollHeight;
        }
        document.getElementById("chatInput").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                sendMessage();
            }
        });
    </script>


    <div id="chatPopup" class="chat-popup">

        <!-- HEADER -->
        <div class="chat-header">
            <span style="color: #00c6ff; border-radius: 15px;">💬 Chat nhanh</span>
            <button onclick="closeChat()">×</button>
        </div>

        <!-- CHAT CONTENT -->
        <div id="chatContent" class="chat-content">
            <div class="bot-msg">👋 Xin chào! Bạn cần hỗ trợ gì?</div>
        </div>

        <!-- CARD SẢN PHẨM -->
        <div class="product-card-chat">
            <img src="public/<?php echo htmlspecialchars($mainImage); ?>">
            <div>
                <h6><?php echo htmlspecialchars($product['ten']); ?></h6>
                <span class="price"><?php echo $product['gia_formatted']; ?></span>
            </div>
        </div>

        <!-- INPUT -->
        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">Gửi</button>
        </div>

    </div>
</body>

</html>