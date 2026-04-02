<?php
require_once 'Database.php';
require_once 'model/functions.php';
session_start();

// Kiểm tra user đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = (int) $_SESSION['user_id'];

// Lấy thông tin user từ database
$stmt = $conn->prepare("SELECT id, ten, email, so_dien_thoai FROM nguoi_dung WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Lấy giỏ hàng từ database
$cart = [];
$stmt = $conn->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$giohang = $stmt->fetch(PDO::FETCH_ASSOC);

if ($giohang) {
    $stmt = $conn->prepare("
        SELECT 
            ctgh.id as chi_tiet_id,
            ctgh.san_pham_id as id,
            sp.ten as name,
            sp.gia as price,
            sp.hinh_anh_chinh as image,
            ctgh.kich_thuoc_id,
            ks.ten as size,
            ctgh.mau_sac_id,
            ms.ten as color,
            ctgh.so_luong as quantity
        FROM chi_tiet_gio_hang ctgh
        JOIN san_pham sp ON ctgh.san_pham_id = sp.id
        LEFT JOIN kich_thuoc ks ON ctgh.kich_thuoc_id = ks.id
        LEFT JOIN mau_sac ms ON ctgh.mau_sac_id = ms.id
        WHERE ctgh.gio_hang_id = ?
    ");
    $stmt->execute([$giohang['id']]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra giỏ hàng rỗng
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/hero.css">
    <style>
        body {
            background: #f5f5f5;
        }

        .checkout-container {
            min-height: calc(100vh - 200px);
        }

        #map {
            height: 350px;
            width: 100%;
            border-radius: 10px;
            margin-top: 15px;
            border: 1px solid #ddd;
        }

        .sticky-summary {
            position: sticky;
            top: 100px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .btn-checkout {
            background: #ff6b35;
            border: none;
            font-weight: 600;
            padding: 12px;
        }

        .btn-checkout:hover {
            background: #e55a24;
        }

        .cart-summary {
            background: #fff;
        }

        .coupon-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        .payment-method {
            margin: 15px 0;
        }
    </style>
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
                                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="color: white;">
                                        <i class="fas fa-user-circle fa-lg"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                        <?php if (isset($_SESSION['user_name'])): ?>
                                            <li>
                                                <h6 class="dropdown-header">
                                                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                                </h6>
                                            </li>
                                            <li><a class="dropdown-item" href="profile.php"><i
                                                        class="fas fa-user-edit me-2"></i> Hồ sơ của tôi</a></li>
                                            <li><a class="dropdown-item" href="orders.php"><i
                                                        class="fas fa-shopping-bag me-2"></i> Đơn hàng đã mua</a></li>
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
    <div class="checkout-container">
        <div class="container-custom py-5">
            <h1  class="mb-4"><i class="fas fa-credit-card"></i> Thanh Toán Đơn Hàng</h1>
            <!-- form thanh toán -->
            <form method="POST" id="checkoutForm">
                <div class="row">
                    <div class="col-lg-7">
                        <!-- Thông tin nhận hàng -->
                        <div class="card p-4 mb-4">
                            <h5 class="mb-4"><i class="fas fa-map-marker-alt"></i> Thông tin nhận hàng</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ tên</label>
                                    <input type="text" name="ten_nguoi_nhan" class="form-control" required
                                        value="<?= htmlspecialchars($user['ten'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số điện thoại</label>
                                    <input type="text" name="so_dien_thoai_nhan" class="form-control" required
                                        value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa chỉ giao hàng</label>
                                <div class="input-group">
                                    <input type="text" id="address" name="dia_chi_giao_hang" class="form-control"
                                        placeholder="Tìm địa chỉ hoặc kéo marker trên bản đồ" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="searchAddress()">
                                        <i class="fas fa-search"></i> Tìm
                                    </button>
                                </div>
                            </div>
                            <div id="map"></div>
                            <input type="hidden" name="lat" id="lat" value="10.762622">
                            <input type="hidden" name="lng" id="lng" value="106.660172">
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="card p-4">
                            <h5 class="mb-4"><i class="fas fa-wallet"></i> Phương thức thanh toán</h5>
                            <div class="payment-method">
                                <input type="radio" id="cod" name="phuong_thuc_thanh_toan" value="cod" checked>
                                <label for="cod" class="ms-2 fw-bold">Thanh toán khi nhận hàng (COD)</label>
                                <p class="text-muted ms-4 small">Thanh toán trực tiếp khi nhân viên giao hàng đến</p>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="banking" name="phuong_thuc_thanh_toan" value="banking">
                                <label for="banking" class="ms-2 fw-bold">Chuyển khoản ngân hàng</label>
                                <p class="text-muted ms-4 small">Chuyển tiền trước khi nhận hàng</p>
                            </div>
                        </div>
                    </div>
                    <!-- tóm tắt đơn hàng -->
                    <div class="col-lg-5">
                        <div class="card p-4 sticky-summary">
                            <h5 class="mb-4"><i class="fas fa-receipt"></i> Tóm tắt đơn hàng</h5>

                            <!-- Mã giảm giá -->
                            <div class="mb-4">
                                <label class="form-label fw-bold small">Mã giảm giá</label>
                                <div class="input-group">
                                    <input type="text" id="coupon_code" class="form-control"
                                        placeholder="Nhập mã giảm giá...">
                                    <button class="btn btn-dark" type="button" onclick="applyCoupon()">Áp dụng</button>
                                </div>
                                <div id="coupon_msg" class="small mt-2"></div>
                            </div>

                            <!-- Danh sách sản phẩm -->
                            <div class="cart-summary mb-4" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($cart as $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    ?>
                                    <div class="d-flex justify-content-between mb-2 small border-bottom pb-2">
                                        <div>
                                            <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                            <small class="text-muted">
                                                <?= $item['size'] ? 'Size: ' . htmlspecialchars($item['size']) : '' ?>
                                                <?= ($item['size'] && $item['color']) ? ' | ' : '' ?>
                                                <?= $item['color'] ? 'Màu: ' . htmlspecialchars($item['color']) : '' ?>
                                            </small><br>
                                            <strong class="text-muted">x<?= $item['quantity'] ?></strong>
                                        </div>
                                        <div class="text-end">
                                            <strong><?= number_format($subtotal, 0, '', '.') ?>đ</strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr>

                            <!-- Tính toán tiền -->
                            <?php $total = 0;
                            foreach ($cart as $item):
                                $total += $item['price'] * $item['quantity'];
                            endforeach; ?>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <strong><?= number_format($total, 0, '', '.') ?>đ</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span><i class="fas fa-tag"></i> Giảm giá:</span>
                                <strong id="txt_discount">-0đ</strong>
                            </div>
                            <div class="d-flex justify-content-between fs-5 fw-bold border-top pt-3">
                                <span>Tổng cộng:</span>
                                <span class="text-danger"
                                    id="txt_total"><?= number_format($total, 0, '', '.') ?>đ</span>
                            </div>

                            <input type="hidden" name="tong_tien" id="val_total" value="<?= $total ?>">
                            <input type="hidden" name="tien_giam_gia" id="val_discount" value="0">
                            <input type="hidden" name="ma_giam_gia_id" id="val_coupon_id" value="">

                            <button type="submit" name="btn_dat_hang" class="btn btn-checkout w-100 btn-lg mt-4">
                                <i class="fas fa-check-circle"></i> XÁC NHẬN ĐẶT HÀNG
                            </button>
                            <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>