<?php
session_start();
require_once "Database.php";

// 1. Khởi tạo kết nối
$db = new Database();
$conn = $db->connect();

// 2. Lấy ID đơn hàng từ URL
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header("Location: index.php"); // Chuyển hướng nếu không có ID
    exit();
}

try {
    // 3. Truy vấn thông tin đơn hàng và người dùng
    $sql_order = "SELECT dh.*, nd.id, nd.ten 
                  FROM don_hang dh 
                  JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
                  WHERE dh.id = :id";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute(['id' => $order_id]);
    $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Đơn hàng không tồn tại!");
    }

    // 4. Truy vấn danh sách sản phẩm trong đơn hàng
    $sql_items = "SELECT ct.*, sp.ten as ten_sp, sp.hinh_anh_chinh 
                  FROM chi_tiet_don_hang ct 
                  JOIN san_pham sp ON ct.san_pham_id = sp.id 
                  WHERE ct.don_hang_id = :id";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->execute(['id' => $order_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
function formatPrice($price) {
    return number_format($price ?? 0, 0, ',', '.') . '₫';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $order_id ?> - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link rel="stylesheet" href="css/cart.css">
    <style>
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; }
        .status-da_giao { background: #d4edda; color: #155724; }
        .status-dang_xu_ly { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-custom">
        <a class="navbar-brand" href="index.php"><i class="fas fa-dumbbell"></i> AthleteHub</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Sản phẩm</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="cart-header">
    <div class="container-custom">
        <h1><i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #<?= $order['ma_don_hang'] ?></h1>
    </div>
</div>

<section class="cart-section">
    <div class="container-custom">
        <div class="row">
            
            <div class="col-lg-8">
                <div class="cart-items-container">
                    <div id="cartItemsList">
                        <?php foreach ($items as $item): 
                            $subtotal = ($item['gia'] ?? 0) * ($item['so_luong'] ?? 1);
                        ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="public/<?= htmlspecialchars($item['hinh_anh_chinh'] ?? 'public/placeholder.svg') ?>" 
                                     alt="<?= htmlspecialchars($item['ten_sp']) ?>"onerror="this.src='public/placeholder.svg'">
                            </div>
                            <div class="cart-item-info">
                                <h4><?= htmlspecialchars($item['ten_sp']) ?></h4>
                                <div class="cart-item-price"><?= formatPrice($item['gia']) ?></div>
                            </div>
                            <div class="cart-item-details text-center">
                                <span class="text-muted">Số lượng:</span>
                                <strong><?= $item['so_luong'] ?></strong>
                            </div>
                            <div class="cart-item-subtotal">
                                <strong><?= formatPrice($subtotal) ?></strong>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h2>Thông tin đơn hàng</h2>
                    
                    <div class="summary-item">
                        <span>Trạng thái:</span>
                        <span class="status-badge status-<?= $order['trang_thai'] ?>"><?= $order['trang_thai'] ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Ngày đặt:</span>
                        <span><?= date('d/m/Y', strtotime($order['ngay_dat'])) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Khách hàng:</span>
                        <span><?= htmlspecialchars($order['nguoi_dung_id']) ?></span>
                    </div>
                    <div class="summary-divider"></div>
                    
                    <div class="summary-item">
                        <span>Tổng tiền hàng:</span>
                        <span><?= formatPrice($order['tong_tien']) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Tiền giảm:</span>
                        <span class="text-success"><?= formatPrice($order['tien_giam'] ?? 0) ?></span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-total">
                        <span>Thành tiền:</span>
                        <span style="color:var(--primary-color);"><?= formatPrice($order['thanh_tien']) ?></span>
                    </div>

                    <div class="order-benefits mt-3">
                        <div class="benefit-item"><i class="fas fa-wallet"></i><span>Thanh toán: <?= str_replace('_', ' ', $order['phuong_thuc_thanh_toan']) ?></span></div>
                    </div>

                    <a href="CRUDdonhang.php" class="btn-continue-shopping w-100 mt-3 text-center">
                        <i class="fas fa-arrow-left"></i> Quay lại đơn hàng
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<footer class="footer">
    <div class="container-custom text-center">
        <p>&copy; <?= date('Y') ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.</p>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>