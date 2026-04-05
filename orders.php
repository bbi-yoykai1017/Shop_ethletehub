<?php
session_start();
require_once "Database.php";
require_once "auth.php";

// 1. Khởi tạo kết nối
$db = new Database();
$conn = $db->connect();

$order_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null; // Lấy ID người dùng đang đăng nhập

$order = null;
$items = [];
$all_orders = [];

try {
    if ($order_id) {
        // TRƯỜNG HỢP 1: XEM CHI TIẾT 1 ĐƠN HÀNG
        $sql_order = "SELECT dh.*, nd.ten as ten_khach_hang 
                      FROM don_hang dh 
                      JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
                      WHERE dh.id = :id";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->execute(['id' => $order_id]);
        $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $sql_items = "SELECT ct.*, sp.ten as ten_sp, sp.hinh_anh_chinh 
                          FROM chi_tiet_don_hang ct 
                          JOIN san_pham sp ON ct.san_pham_id = sp.id 
                          WHERE ct.don_hang_id = :id";
            $stmt_items = $conn->prepare($sql_items);
            $stmt_items->execute(['id' => $order_id]);
            $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        // TRƯỜNG HỢP 2: XEM DANH SÁCH TẤT CẢ ĐƠN HÀNG (Khi vào orders.php mà không có ID)
        $sql_all = "SELECT * FROM don_hang WHERE nguoi_dung_id = :user_id ORDER BY ngay_dat DESC";
        $stmt_all = $conn->prepare($sql_all);
        $stmt_all->execute(['user_id' => $user_id]);
        $all_orders = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
function formatPrice($price)
{
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
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .status-da_giao {
            background: #d4edda;
            color: #155724;
        }

        .status-dang_xu_ly {
            background: #fff3cd;
            color: #856404;
        }
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
            <h1><i class="fas fa-file-invoice me-2"></i>Tổng quan đơn hàng</h1>
        </div>
    </div>

    <section class="cart-section">
        <div class="container-custom">

            <?php if ($order_id && $order): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-items-container">
                            <div id="cartItemsList">
                                <?php foreach ($items as $item):
                                    $subtotal = ($item['gia'] ?? 0) * ($item['so_luong'] ?? 1);
                                ?>
                                    <div class="cart-item">
                                        <div class="cart-item-image">
                                            <img src="public/<?= htmlspecialchars($item['hinh_anh_chinh'] ?? 'placeholder.svg') ?>"
                                                onerror="this.src='public/placeholder.svg'">
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
                                <span>Tên người nhận:</span>
                                <span class="status-badge status-<?= $order['trang_thai'] ?>"><?= $order['ten_nguoi_nhan'] ?></span>
                            </div>
                            <div class="summary-item">
                                <span>SDT:</span>
                                <span class="status-badge status-<?= $order['trang_thai'] ?>"><?= $order['so_dien_thoai_nhan'] ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Địa chỉ nhận hàng:</span>
                                <span class="status-badge status-<?= $order['trang_thai'] ?>"><?= $order['dia_chi_giao_hang'] ?></span>
                            </div>
                            <div class="summary-divider"></div>
                             <div class="summary-total">
                                <span>Tiền giảm:</span>
                                <span style="color:var(--primary-color);"><?= formatPrice($order['tien_giam']) ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Thành tiền:</span>
                                <span style="color:var(--primary-color);"><?= formatPrice($order['thanh_tien']) ?></span>
                            </div>
                            <a href="orders.php" class="btn btn-outline-primary w-100 mt-3">
                                <i class="fas fa-arrow-left"></i> Quay lại danh sách
                            </a>
                        </div>
                    </div>
                </div>

            <?php elseif (!$order_id): ?>
                <div class="bg-white p-4 rounded shadow-sm">
                    <h2 class="mb-4"><i class="fas fa-list-ul me-2"></i>Lịch sử đơn hàng</h2>
                    <?php if (empty($all_orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                            <a href="products.php" class="btn btn-primary">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_orders as $o): ?>
                                        <tr>
                                            <td><strong>#<?= $o['ma_don_hang'] ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($o['ngay_dat'])) ?></td>
                                            <td><span class="text-primary fw-bold"><?= formatPrice($o['thanh_tien']) ?></span></td>
                                            <td><span class="status-badge status-<?= $o['trang_thai'] ?>"><?= $o['trang_thai'] ?></span></td>
                                            <td class="text-center">
                                                <a href="orders.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <h4>Không tìm thấy đơn hàng!</h4>
                    <p>Đơn hàng không tồn tại hoặc đã bị xóa.</p>
                    <a href="orders.php" class="btn btn-primary">Xem tất cả đơn hàng</a>
                </div>
            <?php endif; ?>
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