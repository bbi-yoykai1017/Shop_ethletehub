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

    $order_id = $_GET['id'] ?? null;
    // CẬP NHẬT TẠI ĐÂY: Ưu tiên lấy user_id từ URL nếu là admin xem, 
    // nếu không thì lấy từ session của người dùng đang đăng nhập.
    $user_id = $_GET['user_id'] ?? ($_SESSION['user_id'] ?? null);

    $order = null;
    $items = [];
    $all_orders = [];

    try {
        if ($order_id) {
            // TRƯỜNG HỢP 1: XEM CHI TIẾT 1 ĐƠN HÀNG
            // Bỏ điều kiện lọc theo user_id trong SQL để Admin có thể xem được
            $sql_order = "SELECT dh.*, nd.ten as ten_khach_hang 
                        FROM don_hang dh 
                        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
                        WHERE dh.id = :id";
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->execute(['id' => $order_id]);
            $order = $stmt_order->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $sql_items = "SELECT ct.*, sp.ten as ten_sp, sp.hinh_anh_chinh , ms.ten as ten_mau
                            FROM chi_tiet_don_hang ct 
                            JOIN san_pham sp ON ct.san_pham_id = sp.id
                            JOIN mau_sac ms on ct.mau_sac_id = ms.id
                            WHERE ct.don_hang_id =:id";
                $stmt_items = $conn->prepare($sql_items);
                $stmt_items->execute(['id' => $order_id]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // TRƯỜNG HỢP 2: XEM DANH SÁCH ĐƠN HÀNG CỦA MỘT USER CỤ THỂ
            if ($user_id) {
                // Lấy thêm tên người dùng để hiển thị tiêu đề cho rõ ràng
                $sql_user_info = "SELECT ten FROM nguoi_dung WHERE id = :user_id";
                $stmt_user = $conn->prepare($sql_user_info);
                $stmt_user->execute(['user_id' => $user_id]);
                $customer_name = $stmt_user->fetchColumn();

                $sql_all = "SELECT dh.id, dh.ma_don_hang, dh.ngay_dat, dh.thanh_tien, dh.trang_thai, 
                            GROUP_CONCAT(DISTINCT ms.ten SEPARATOR ', ') as ten_mau
                        FROM don_hang dh 
                        LEFT JOIN chi_tiet_don_hang ct ON dh.id = ct.don_hang_id
                        LEFT JOIN mau_sac ms ON ct.mau_sac_id = ms.id
                        WHERE dh.nguoi_dung_id = :user_id 
                        GROUP BY dh.id
                        ORDER BY dh.ngay_dat DESC";

                $stmt_all = $conn->prepare($sql_all);
                $stmt_all->execute(['user_id' => $user_id]);
                $all_orders = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    } // <--- QUAN TRỌNG: Phải có dấu này để đóng khối try

    function formatPrice($price)
    {
        return number_format($price ?? 0, 0, ',', '.') . '₫';
    }
    // 1. Khởi tạo giá trị mặc định để tránh lỗi "Undefined variable"
    $co_the_huy = false;
    $diff_hours = 0;
    $la_don_moi = false;
    $trong_24h = false;
    $da_huy = false;

    // 2. CHỈ TÍNH TOÁN KHI ĐANG XEM CHI TIẾT (Biến $order có dữ liệu)
    if (isset($order) && is_array($order)) {
        // Thiết lập múi giờ để tính toán chính xác
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $ngay_dat_str = $order['ngay_dat'] ?? null;

        if ($ngay_dat_str) {
            $ngay_dat_timestamp = strtotime($ngay_dat_str);
            $bay_gio = time();
            // Tính số giờ đã trôi qua
            $diff_hours = ($bay_gio - $ngay_dat_timestamp) / 3600;

            // Xác định các trạng thái logic
            $la_don_moi = ($order['trang_thai'] == 'cho_xac_nhan');
            $trong_24h = ($diff_hours < 24);
            $da_huy = ($order['trang_thai'] == 'da_huy');

            // Điều kiện để hiện nút hủy
            if ($la_don_moi && $trong_24h) {
                $co_the_huy = true;
            }
        }
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
                        <li class="nav-item"><a class="nav-link" href="return_order.php">Hoàn đơn</a></li>

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
                                            <span>Màu sắc : <?= htmlspecialchars($item['ten_mau'] ?? 'N/A')  ?> </span>
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
                                    <?php
                                    $trang_thai_map = [
                                        'cho_xac_nhan'   => ['label' => 'Cho xac nhan',   'color' => '#7c5d00', 'bg' => '#fff3cd'],  // Vàng
                                        'cho_thanh_toan' => ['label' => 'Cho thanh toan', 'color' => '#ffffff', 'bg' => '#fd7e14'],  // Cam
                                        'dang_chuan_bi'  => ['label' => 'Dang chuan bi',  'color' => '#ffffff', 'bg' => '#17a2b8'],  // Xanh dương nhạt
                                        'dang_giao'      => ['label' => 'Dang giao',       'color' => '#ffffff', 'bg' => '#007bff'],  // Xanh dương
                                        'da_giao'        => ['label' => 'Da giao',         'color' => '#ffffff', 'bg' => '#28a745'],  // Xanh lá
                                        'da_huy'         => ['label' => 'Da huy',          'color' => '#ffffff', 'bg' => '#dc3545'],  // Đỏ
                               
                                        'da_tra_hang'    => ['label' => 'Da tra hang',    'color' => '#ffffff', 'bg' => '#20c997'],  // Xanh ngọc
                                    ];
                                    $trang_thai_clean = trim($order['trang_thai'], "' ");
                                    $tt = $trang_thai_map[$trang_thai_clean] ?? ['label' => $trang_thai_clean, 'color' => '#666', 'bg' => '#f5f5f5']; ?>
                                    <div style="display:inline-block !important; background:<?= $tt['bg'] ?> !important; color:<?= $tt['color'] ?> !important; padding:6px 14px !important; border-radius:20px !important; font-weight:600 !important; font-size:14px !important; border:1px solid <?= $tt['color'] ?> !important;">
                                        <?= $tt['label'] ?>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <span>Ngày đặt:</span>
                                    <span><?= date("d/m/Y", strtotime($order['ngay_dat'])) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>Giờ đặt:</span>
                                    <span><?= date("H:i:s", strtotime($order['ngay_dat'])) ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>Tên người nhận:</span>
                                    <span class="status-badge status-"><?= $order['ten_nguoi_nhan'] ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>SDT:</span>
                                    <span class="status-badge status-"><?= $order['so_dien_thoai_nhan'] ?></span>
                                </div>
                                <div class="summary-item">
                                    <span>Địa chỉ nhận hàng:</span>
                                    <span class="status-badge status-"><?= $order['dia_chi_giao_hang'] ?></span>
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
                                </a> <a href="return_status.php?order_id=<?= $order['id'] ?>" class="btn btn-info w-100 mt-2">
                                    <i class="fas fa-search me-1"></i> Theo dõi hoàn đơn
                                </a>

                                <?php if ($co_the_huy): ?>
                                    <button onclick="confirmCancel(<?= $order['id'] ?>)" class="btn btn-danger w-100 mt-2">
                                        <i class="fas fa-times-circle"></i> Hủy đơn hàng
                                    </button>
                                    <small class="text-muted d-block text-center mt-1">
                                        (Bạn có thể hủy đơn trong 24h kể từ khi đặt)
                                    </small>

                                <?php elseif ($order['trang_thai'] == 'da_huy'): ?>
                                    <button class="btn btn-outline-secondary w-100 mt-2" disabled>
                                        <i class="fas fa-check-circle"></i> Đơn hàng đã hủy
                                    </button>
                                    <small class="text-muted d-block text-center mt-1">
                                        Đơn hàng này đã được bạn yêu cầu hủy thành công.
                                    </small>

                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 mt-2" disabled>
                                        <i class="fas fa-ban"></i> Không thể hủy đơn
                                    </button>
                                    <small class="text-danger d-block text-center mt-1">
                                        <?php
                                        if ($diff_hours >= 24) echo "Đã quá thời hạn 24h để hủy đơn.";
                                        else echo "Đơn hàng đã được xử lý, vui lòng liên hệ hỗ trợ.";
                                        ?>
                                    </small>
                                <?php endif; ?>
                                <?php // ===== NÚT TRẢ HÀNG - Thêm vào đây ===== 
                                ?>
                                <?php if ($order['trang_thai'] == 'da_giao'): ?>
                                    <a href="return_order.php?id=<?= $order['id'] ?>" class="btn btn-warning w-100 mt-2">
                                        <i class="fas fa-undo-alt me-1"></i> Yêu cầu trả hàng
                                    </a>
                                    <small class="text-muted d-block text-center mt-1">
                                        Được trả trong vòng 7 ngày kể từ khi nhận hàng
                                    </small>

                                <?php elseif ($order['trang_thai'] == 'cho_tra_hang'): ?>
                                    <button class="btn btn-outline-warning w-100 mt-2" disabled>
                                        <i class="fas fa-clock me-1"></i> Đang xử lý trả hàng
                                    </button>
                                    <a href="return_status.php?order_id=<?= $order['id'] ?>" class="btn btn-link w-100 p-0 mt-1 small">
                                        <i class="fas fa-tasks me-1"></i> Theo dõi yêu cầu →
                                    </a>

                                <?php elseif ($order['trang_thai'] == 'da_tra_hang'): ?>
                                    <button class="btn btn-outline-success w-100 mt-2" disabled>
                                        <i class="fas fa-check-circle me-1"></i> Đã hoàn trả hàng
                                    </button>
                                    <small class="text-muted d-block text-center mt-1">
                                        Yêu cầu trả hàng đã hoàn tất.
                                    </small>
                                <?php endif; ?>
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
                                            <th>Màu sắc</th>
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
                                                <td><span class="text-primary fw-bold"><?= htmlspecialchars($o['ten_mau'] ?? 'N/A') ?></span></td>
                                                <td>
                                                    <?php
                                                    $trang_thai_map = [
                                                        'cho_xac_nhan'   => ['label' => 'Cho xac nhan',   'color' => '#7c5d00', 'bg' => '#fff3cd'],  // Vàng
                                                        'cho_thanh_toan' => ['label' => 'Cho thanh toan', 'color' => '#ffffff', 'bg' => '#fd7e14'],  // Cam
                                                        'dang_chuan_bi'  => ['label' => 'Dang chuan bi',  'color' => '#ffffff', 'bg' => '#17a2b8'],  // Xanh dương nhạt
                                                        'dang_giao'      => ['label' => 'Dang giao',       'color' => '#ffffff', 'bg' => '#007bff'],  // Xanh dương
                                                        'da_giao'        => ['label' => 'Da giao',         'color' => '#ffffff', 'bg' => '#28a745'],  // Xanh lá
                                                        'da_huy'         => ['label' => 'Da huy',          'color' => '#ffffff', 'bg' => '#dc3545'],  // Đỏ
                                                        
                                                        'da_tra_hang'    => ['label' => 'Da tra hang',    'color' => '#ffffff', 'bg' => '#20c997'],  // Xanh ngọc
                                                    ];
                                                    $trang_thai_clean = trim($o['trang_thai'], "' ");
                                                    $tt_o = $trang_thai_map[$trang_thai_clean] ?? ['label' => $trang_thai_clean, 'color' => '#666', 'bg' => '#f5f5f5'];
                                                    ?>
                                                    <span style="background:<?= $tt_o['bg'] ?>; color:<?= $tt_o['color'] ?>; padding:4px 12px; border-radius:20px; font-weight:600; font-size:13px; border:1px solid <?= $tt_o['color'] ?>;">
                                                        <?= $tt_o['label'] ?>
                                                    </span>

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
        <script>
            function confirmCancel(orderId) {
                if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.')) {
                    // Chuyển hướng đến file xử lý hủy đơn (Bạn cần tạo file này)
                    window.location.href = 'cancel_order.php?id=' + orderId;
                }
            }
        </script>
    </body>

    </html>