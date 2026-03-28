<?php

session_start();

// Lấy giỏ hàng từ session (API sử dụng session)
$items = $_SESSION['cart'] ?? [];
$isEmpty = empty($items);

// Tính toán summary
$summary = [
    'totalItems' => array_sum(array_map(fn($item) => $item['quantity'] ?? 1, $items)),
    'subtotal' => array_sum(array_map(fn($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1), $items)),
    'discount' => 0, // Mặc định không có giảm giá
];
$summary['shippingFee'] = $summary['subtotal'] >= 500000 ? 0 : 25000;
$summary['total'] = $summary['subtotal'] + $summary['shippingFee'] - $summary['discount'];

// Helper function để format giá
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . '₫';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-custom">
        <a class="navbar-brand" href="index.php"><i class="fas fa-dumbbell"></i> AthleteHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Sản phẩm</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#categories">Danh mục</a></li>
            </ul>
            <div class="navbar-right d-flex align-items-center">
                <div class="cart-icon" onclick="window.location.href='cart.php'" style="cursor:pointer;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?= $summary['totalItems'] ?></span>
                </div>
                <div class="user-action-dropdown dropdown ms-3">
                    <a href="#" class="user-icon-link text-decoration-none dropdown-toggle"
                       id="userMenu" data-bs-toggle="dropdown" style="color:white;">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <?php if (isset($_SESSION['user_name'])): ?>
                            <li><h6 class="dropdown-header"><?= htmlspecialchars($_SESSION['user_name']) ?></h6></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i>Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>Đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Đăng nhập</a></li>
                            <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i>Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- PAGE HEADER -->
<div class="cart-header">
    <div class="container-custom">
        <h1><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn</h1>
    </div>
</div>

<!-- CART SECTION -->
<section class="cart-section">
    <div class="container-custom">
        <div class="row">

            <!-- ── DANH SÁCH SẢN PHẨM ── -->
            <div class="col-lg-8">
                <div class="cart-items-container">
                    <?php if ($isEmpty): ?>
                        <div class="empty-cart text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                            <h3>Giỏ hàng của bạn trống</h3>
                            <p class="text-muted">Hãy thêm sản phẩm vào giỏ để tiếp tục mua sắm.</p>
                            <a href="products.php" class="btn btn-primary mt-2">
                                <i class="fas fa-arrow-right me-1"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    <?php else: ?>
                        <div id="cartItemsList">
                            <?php foreach ($items as $item): ?>
                            <div class="cart-item" id="item-<?= $item['id'] ?>">
                                <div class="cart-item-image">
                                    <img src="<?= htmlspecialchars($item['image']) ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         onerror="this.src='images/placeholder.jpg'">
                                </div>
                                <div class="cart-item-info">
                                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                                    <p><?= htmlspecialchars($item['danh_muc'] ?? 'Danh mục') ?></p>
                                    <div class="cart-item-price"><?= formatPrice($item['price']) ?></div>
                                </div>
                                <div class="cart-item-details">
                                    <div class="quantity-control">
                                        <button class="qty-control-btn"
                                                onclick="changeQty(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="qty-control-input"
                                               value="<?= $item['quantity'] ?>" min="1" max="100"
                                               onchange="changeQty(<?= $item['id'] ?>, this.value)">
                                        <button class="qty-control-btn"
                                                onclick="changeQty(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="cart-item-subtotal">
                                    <strong id="subtotal-<?= $item['id'] ?>">
                                        <?= formatPrice($item['price'] * $item['quantity']) ?>
                                    </strong>
                                </div>
                                <button class="btn-remove-item"
                                        onclick="removeItem(<?= $item['id'] ?>, '<?= htmlspecialchars($item['ten'], ENT_QUOTES) ?>')"
                                        title="Xóa sản phẩm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── TÓM TẮT ĐƠN HÀNG ── -->
            <div class="col-lg-4">
                <div class="cart-summary" id="cartSummaryBox" <?= $isEmpty ? 'style="display:none"' : '' ?>>
                    <h2>Tóm tắt đơn hàng</h2>

                    <div class="summary-item">
                        <span>Tổng sản phẩm:</span>
                        <span id="totalQuantity"><?= $summary['totalItems'] ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Tổng tiền hàng:</span>
                        <span id="subtotalDisplay"><?= formatPrice($summary['subtotal']) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Phí vận chuyển:</span>
                        <span id="shippingDisplay" class="<?= $summary['shippingFee'] == 0 ? 'shipping-fee' : '' ?>">
                            <?= $summary['shippingFee'] == 0 ? 'Miễn phí' : formatPrice($summary['shippingFee']) ?>
                        </span>
                    </div>

                    <!-- Mã giảm giá -->
                    <div class="summary-item" style="flex-wrap:wrap;gap:8px;">
                        <input type="text" id="promoCode" placeholder="Nhập mã giảm giá"
                               class="promo-input" style="flex:1;min-width:130px;">
                        <button class="btn-apply-promo" onclick="applyPromo()">Áp dụng</button>
                    </div>

                    <div class="summary-item" id="discountItem"
                         style="<?= $summary['discount'] > 0 ? '' : 'display:none' ?>">
                        <span>Giảm giá:</span>
                        <span id="discountDisplay" style="color:var(--success);">
                            -<?= formatPrice($summary['discount']) ?>
                        </span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>Tổng cộng:</span>
                        <span id="totalDisplay"><?= formatPrice($summary['total']) ?></span>
                    </div>

                    <button class="btn-checkout" onclick="goCheckout()">
                        <i class="fas fa-credit-card"></i> Thanh toán ngay
                    </button>

                    <a href="products.php" class="btn-continue-shopping">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>

                    <div class="order-benefits">
                        <div class="benefit-item"><i class="fas fa-truck"></i><span>Miễn phí ship trên 500.000₫</span></div>
                        <div class="benefit-item"><i class="fas fa-shield-alt"></i><span>Bảo mật thanh toán 100%</span></div>
                        <div class="benefit-item"><i class="fas fa-undo"></i><span>Hoàn hàng trong 30 ngày</span></div>
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
                        <p style="color:#c0c0c0;">Sản phẩm thể thao chất lượng cao.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4 class="footer-title">Liên kết nhanh</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Trang chủ</a></li>
                            <li><a href="products.php">Sản phẩm</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4 class="footer-title">Hỗ trợ</h4>
                        <ul class="footer-links">
                            <li><a href="#">Liên hệ</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-section">
                        <h4 class="footer-title">Liên lạc</h4>
                        <p style="color:#c0c0c0;">+84 (0) 123 456 789</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; <?= date('Y') ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/cart.js"></script>
</body>
</html>