<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$db = new Database();
$conn = $db->connect();

$update_mode = false;
$edit_order = [
    'id' => '',
    'nguoi_dung_id' => '',
    'ma_don_hang' => '',
    'tong_tien' => '',
    'tien_giam' => 0,
    'thanh_tien' => '',
    'phuong_thuc_thanh_toan' => '',
    'trang_thai' => ''
];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM don_hang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_order = $result;
        $update_mode = true;
    }
}

if (isset($_POST['save_order']) && !empty($_POST['id'])) {  
    $id = $_POST['id'];
    $user_id = $_POST['nguoi_dung_id'];
    $ma_don = $_POST['ma_don_hang'];
    $tong = $_POST['tong_tien'];
    $giam = $_POST['tien_giam'];
    $thanh_tien = $_POST['thanh_tien'];
    $pttt = $_POST['phuong_thuc_thanh_toan'];
    $trang_thai = $_POST['trang_thai'];

    if ($user_id <= 0) {
        echo "<script>alert('Lỗi: ID Ngườidùng phải là số dương!'); window.history.back();</script>";
        exit;
    }

    $sql = "UPDATE don_hang SET nguoi_dung_id=?, ma_don_hang=?, tong_tien=?, tien_giam=?, thanh_tien=?, phuong_thuc_thanh_toan=?, trang_thai=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $ma_don, $tong, $giam, $thanh_tien, $pttt, $trang_thai, $id]);
    header("Location: CRUDdonhang.php?success=updated");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $conn->beginTransaction();
        $sql_delete_items = "DELETE FROM chi_tiet_don_hang WHERE don_hang_id = ?";
        $stmt_items = $conn->prepare($sql_delete_items);
        $stmt_items->execute([$id]);
        $sql_delete_order = "DELETE FROM don_hang WHERE id = ?";
        $stmt_order = $conn->prepare($sql_delete_order);
        $stmt_order->execute([$id]);
        $conn->commit();
        header("Location: CRUDdonhang.php?success=deleted");
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: CRUDdonhang.php?error=delete_failed");
    }
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];
$where_sql = "";

if (!empty($search)) {
    if (is_numeric($search)) {
        $where_sql = " WHERE id = ? ";
        $params[] = $search;
    } else {
        $where_sql = " WHERE ma_don_hang LIKE ? ";
        $params[] = "%$search%";
    }
}

$total_sql = "SELECT COUNT(*) FROM don_hang" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_list = "SELECT * FROM don_hang" . $where_sql . " ORDER BY ngay_dat DESC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$listorders = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Stats
$stats = [];
$statuses = ['cho_thanh_toan','cho_xac_nhan','dang_chuan_bi','dang_giao','da_giao','da_huy','da_tra_hang'];
foreach ($statuses as $st) {
    $stats[$st] = $conn->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai = '$st'")->fetchColumn();
}

$toast = '';
$toast_msg = '';
if (isset($_GET['success'])) {
    $toast = 'success';
    $toast_msg = $_GET['success'] === 'updated' ? 'Cập nhật đơn hàng thành công!' : ($_GET['success'] === 'deleted' ? 'Xóa đơn hàng thành công!' : 'Thành công!');
} elseif (isset($_GET['error'])) {
    $toast = 'error';
    $toast_msg = 'Có lỗi xảy ra, vui lòng thử lại!';
}

function getStatusBadge($status) {
    switch ($status) {
        case 'cho_thanh_toan': return '<span class="badge-modern badge-status-pending"><i class="fas fa-clock me-1"></i>Chờ thanh toán</span>';
        case 'cho_xac_nhan': return '<span class="badge-modern badge-status-pending"><i class="fas fa-hourglass-half me-1"></i>Chờ xác nhận</span>';
        case 'dang_chuan_bi': return '<span class="badge-modern badge-status-processing"><i class="fas fa-box me-1"></i>Chuẩn bị</span>';
        case 'dang_giao': return '<span class="badge-modern badge-status-shipping"><i class="fas fa-truck me-1"></i>Đang giao</span>';
        case 'da_giao': return '<span class="badge-modern badge-status-active"><i class="fas fa-check-circle me-1"></i>Đã giao</span>';
        case 'da_huy': return '<span class="badge-modern badge-status-cancelled"><i class="fas fa-times-circle me-1"></i>Đã hủy</span>';
        case 'cho_tra_hang': return '<span class="badge-modern badge-status-refunded"><i class="fas fa-undo me-1"></i>Chờ trả hàng</span>';
        case 'da_tra_hang': return '<span class="badge-modern badge-status-refunded"><i class="fas fa-undo me-1"></i>Đã trả hàng thành công</span>';
        default: return '<span class="badge-modern badge-status-draft">' . htmlspecialchars($status) . '</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý đơn hàng - EthleteHub</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/crud-modern.css">
    <link rel="stylesheet" href="css/admin-layout.css">
</head>

<body style="background: linear-gradient(135deg, #f5f7fa 0%, #e4ecfb 100%); min-height: 100vh;">

    <nav class="navbar navbar-dark shadow" style="background: var(--dark-gradient);">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold"><i class="fas fa-tachometer-alt me-2"></i> AthleteHub Admin</span>
            <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home me-1"></i> Trang chủ</a>
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            <ul>
                <li><a href="CRUDproduct.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php" class="active"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                    <li><a href="ADreturn.php"><i class="fas fa-undo-alt me-2"></i> Trả Hàng</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quản lý đơn hàng</span>
            </nav>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                        <div class="stat-value"><?= number_format($total_rows) ?></div>
                        <div class="stat-label">Tổng đơn hàng</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-warning delay-2">
                        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                        <div class="stat-value"><?= number_format($stats['cho_xac_nhan']) ?></div>
                        <div class="stat-label">Chờ xác nhận</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-success delay-3">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-value"><?= number_format($stats['da_giao']) ?></div>
                        <div class="stat-label">Đã giao</div>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <?php if ($toast): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-<?= $toast ?>" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-<?= $toast === 'success' ? 'check' : 'exclamation' ?>-circle"></i></div>
                        <div class="toast-content">
                            <div class="toast-title"><?= $toast === 'success' ? 'Thành công' : 'Lỗi' ?></div>
                            <div class="toast-message"><?= htmlspecialchars($toast_msg) ?></div>
                        </div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <?php if ($update_mode): ?>
                <div class="glass-card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Chỉnh sửa đơn hàng: <span class="badge bg-dark"><?= htmlspecialchars($edit_order['ma_don_hang']) ?></span></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3 form-modern">
                            <input type="hidden" name="id" value="<?= $edit_order['id'] ?>">
                            <div class="col-md-2">
                                <label class="form-label">ID Ngườidùng</label>
                                <input type="number" id="nguoi_dung_id" name="nguoi_dung_id" class="form-control" value="<?= $edit_order['nguoi_dung_id'] ?>" required min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mã đơn hàng</label>
                                <input type="text" name="ma_don_hang" class="form-control" value="<?= $edit_order['ma_don_hang'] ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tổng tiền</label>
                                <input type="number" id="tong_tien" name="tong_tien" class="form-control" value="<?= $edit_order['tong_tien'] ?>" required min="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tiền giảm</label>
                                <input type="number" id="tien_giam" name="tien_giam" class="form-control" value="<?= $edit_order['tien_giam'] ?>" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Thành tiền</label>
                                <input type="number" id="thanh_tien" name="thanh_tien" class="form-control" value="<?= $edit_order['thanh_tien'] ?>" required readonly style="background:#e9ecef;">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Phương thức TT</label>
                                <select name="phuong_thuc_thanh_toan" class="form-select">
                                    <option value="tien_mat" <?= $edit_order['phuong_thuc_thanh_toan'] == 'tien_mat' ? 'selected' : '' ?>>Tiền mặt</option>
                                    <option value="bank_transfer" <?= $edit_order['phuong_thuc_thanh_toan'] == 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="cho_thanh_toan" <?= $edit_order['trang_thai'] == 'cho_thanh_toan' ? 'selected' : '' ?>>Chờ thanh toán</option>
                                    <option value="cho_xac_nhan" <?= $edit_order['trang_thai'] == 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                    <option value="dang_chuan_bi" <?= $edit_order['trang_thai'] == 'dang_chuan_bi' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                    <option value="dang_giao" <?= $edit_order['trang_thai'] == 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="da_giao" <?= $edit_order['trang_thai'] == 'da_giao' ? 'selected' : '' ?>>Đã giao</option>
                                    <option value="da_huy" <?= $edit_order['trang_thai'] == 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                                    <option value="cho_tra_hang" <?= $edit_order['trang_thai'] == 'cho_tra_hang' ? 'selected' : '' ?>>Cho trả hàng</option>
                                    <option value="da_tra_hang" <?= $edit_order['trang_thai'] == 'da_tra_hang' ? 'selected' : '' ?>>Đã trả hàng</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                                <button name="save_order" class="btn btn-gradient-primary"><i class="fas fa-save me-1"></i> Lưu cập nhật</button>
                                <a href="CRUDdonhang.php" class="btn btn-outline-gradient-primary">Hủy bỏ</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light border shadow-sm mb-4 animate-fade-in-up">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Vui lòng chọn <strong>"Sửa"</strong> trong danh sách đơn hàng bên dưới để thực hiện thay đổi thông tin.
                </div>
            <?php endif; ?>

            <!-- Search -->
            <div class="filter-group">
                <div class="filter-title"><i class="fas fa-search"></i> Tìm kiếm đơn hàng</div>
                <form method="GET" action="CRUDdonhang.php" class="search-modern">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nhập mã đơn hoặc ID khách..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDdonhang.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive animate-fade-in-up delay-2">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Mã đơn</th>
                            <th>Tổng tiền</th>
                            <th>Giảm</th>
                            <th>Thành tiền</th>
                            <th>PTTT</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th width="140">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listorders)): ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-shopping-basket"></i></div>
                                        <div class="empty-title">Không có đơn hàng nào</div>
                                        <div class="empty-text">Vui lòng thử tìm kiếm khác</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listorders as $donhang) { ?>
                                <tr>
                                    <td><a href="orders.php?id=<?= $donhang['id'] ?>" class="fw-bold text-decoration-underline" style="color:#667eea;">#<?= $donhang['id'] ?></a></td>
                                    <td><span class="badge bg-light text-dark border"><?= $donhang['nguoi_dung_id'] ?></span></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($donhang['ma_don_hang']) ?></td>
                                    <td><?= number_format($donhang['tong_tien'], 0, ',', '.') ?>đ</td>
                                    <td class="text-danger">-<?= number_format($donhang['tien_giam'], 0, ',', '.') ?>đ</td>
                                    <td class="fw-bold text-primary"><?= number_format($donhang['thanh_tien'], 0, ',', '.') ?>đ</td>
                                    <td>
                                        <?php if ($donhang['phuong_thuc_thanh_toan'] === 'tien_mat'): ?>
                                            <span class="badge bg-light text-dark border"><i class="fas fa-money-bill-wave me-1 text-success"></i>Tiền mặt</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border"><i class="fas fa-university me-1 text-info"></i>Chuyển khoản</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= getStatusBadge($donhang['trang_thai']) ?></td>
                                    <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($donhang['ngay_dat'])) ?></td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="?edit=<?= $donhang['id'] ?>" class="btn btn-sm btn-gradient-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                            <a href="?delete=<?= $donhang['id'] ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Xóa đơn hàng này?')" title="Xóa"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-section mt-4 animate-fade-in delay-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-modern justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-left"></i></a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                    <div class="text-center mt-2 small text-muted">
                        Hiển thị trang <?= $page ?> / <?= $total_pages ?> — Tổng <?= number_format($total_rows) ?> đơn hàng
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>EthleteHub Admin © 2026</small>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userIdInput = document.getElementById('nguoi_dung_id');
            const tongTienInput = document.getElementById('tong_tien');
            const tienGiamInput = document.getElementById('tien_giam');
            const thanhTienInput = document.getElementById('thanh_tien');
            const btnSave = document.querySelector('button[name="save_order"]');
            const form = document.querySelector('form');

            function calculateAndValidate() {
                let userId = parseInt(userIdInput ? userIdInput.value : 0);
                let tongTien = parseFloat(tongTienInput ? tongTienInput.value : 0) || 0;
                let tienGiam = parseFloat(tienGiamInput ? tienGiamInput.value : 0) || 0;
                let thanhTien = tongTien - tienGiam;
                if (thanhTienInput) thanhTienInput.value = thanhTien > 0 ? thanhTien : 0;
                let isValid = true;
                if (userIdInput) {
                    if (isNaN(userId) || userId <= 0) { userIdInput.classList.add('is-invalid'); isValid = false; }
                    else { userIdInput.classList.remove('is-invalid'); }
                }
                if (tienGiamInput) {
                    if (tienGiam > tongTien) { tienGiamInput.classList.add('is-invalid'); isValid = false; }
                    else { tienGiamInput.classList.remove('is-invalid'); }
                }
                if (tongTienInput) {
                    if (tongTien <= 0) { tongTienInput.classList.add('is-invalid'); isValid = false; }
                    else { tongTienInput.classList.remove('is-invalid'); }
                }
                if (btnSave) btnSave.disabled = !isValid;
                return isValid;
            }
            if (tongTienInput) tongTienInput.addEventListener('input', calculateAndValidate);
            if (tienGiamInput) tienGiamInput.addEventListener('input', calculateAndValidate);
            if (userIdInput) userIdInput.addEventListener('input', calculateAndValidate);
            if (form) form.addEventListener('submit', function(e) {
                if (!calculateAndValidate()) { e.preventDefault(); alert('Vui lòng kiểm tra lại dữ liệu nhập vào!'); }
            });
            calculateAndValidate();

            const toast = document.getElementById('autoToast');
            if (toast) {
                setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(() => toast.remove(), 400); }, 4000);
            }
        });
    </script>
</body>
</html>

