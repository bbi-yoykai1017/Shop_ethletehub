<?php
require_once 'Database.php';
require_once 'cart.php';

$db= new Database();
$conn = $db->connect();

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
    <nav class="navbar navbar-expand-lg">
        <div class="container-custom">
            <a class="navbar-brand" href="index.html">
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
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#categories">Danh mục</a>
                    </li>
                </ul>
                
                <div class="navbar-right d-flex align-items-center">
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
    <div class="cart-header">
        <div class="container-custom">
            <h1>Giỏ hàng của bạn</h1>
        </div>
    </div>

    CART SECTION
    <section class="cart-section">
        <div class="container-custom">
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="cart-items-container">
                        <div id="cartItemsList" class="cart-items">
                            <!-- Cart items will be inserted here -->
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h2>Tóm tắt đơn hàng</h2>
                        
                        <div class="summary-item">
                            <span>Tổng sản phẩm:</span>
                            <span id="totalItems">0</span>
                        </div>
                        
                        <div class="summary-item">
                            <span>Tổng tiền hàng:</span>
                            <span id="subtotal">0₫</span>
                        </div>
                        
                        <div class="summary-item">
                            <span>Phí vận chuyển:</span>
                            <span id="shipping" class="shipping-fee">Miễn phí</span>
                        </div>
                        
                        <div class="summary-item">
                            <input type="text" id="promoCode" placeholder="Nhập mã giảm giá" class="promo-input">
                            <button id="applyPromo" class="btn-apply-promo">Áp dụng</button>
                        </div>
                        
                        <div class="summary-item discount-item" id="discountItem" style="display: none;">
                            <span>Giảm giá:</span>
                            <span id="discount" style="color: var(--success);">0₫</span>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-total">
                            <span>Tổng cộng:</span>
                            <span id="total">0₫</span>
                        </div>
                        
                        <button id="checkoutBtn" class="btn-checkout">
                            <i class="fas fa-credit-card"></i>
                            Thanh toán ngay
                        </button>
                        
                        <a href="products.html" class="btn-continue-shopping">
                            <i class="fas fa-arrow-left"></i>
                            Tiếp tục mua sắm
                        </a>

                        <!-- Order Benefits -->
                        <div class="order-benefits">
                            <div class="benefit-item">
                                <i class="fas fa-truck"></i>
                                <span>Giao hàng miễn phí trên 500.000₫</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Bảo mật thanh toán 100%</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-undo"></i>
                                <span>Hoàn lại trong 30 ngày</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RELATED PRODUCTS -->
    <section class="related-products-cart">
        <div class="container-custom">
            <h2>Sản phẩm có thể bạn quan tâm</h2>
            <div class="row" id="relatedProducts">
                <!-- Related products will be inserted here -->
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
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên kết nhanh</h4>
                            <ul class="footer-links">
                                <li><a href="#">Trang chủ</a></li>
                                <li><a href="#">Sản phẩm</a></li>
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
                            <p style="color: #c0c0c0;">+84 (0) 123 456 789</p>
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
    <script src="js/cart.js"></script>
</body>
</html>
