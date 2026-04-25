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
$edit_data = [
    'id' => '', 'ten_chuong_trinh' => '', 'ngay_bat_dau' => '', 'ngay_ket_thuc' => '',
    'ngay_cap_nhat_truoc' => 1, 'ghi_chu' => '', 'trang_thai' => 1
];

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM flash_sale WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$id])) { header("Location: CRUDflashsale.php?success=deleted"); exit; }
}

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $sql = "SELECT * FROM flash_sale WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $update_mode = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_flash_sale'])) {
    $ten = $_POST['ten_chuong_trinh'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
    $ngay_cap_nhat_truoc = (int)$_POST['ngay_cap_nhat_truoc'];
    $ghi_chu = $_POST['ghi_chu'];
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE flash_sale SET ten_chuong_trinh=?, ngay_bat_dau=?, ngay_ket_thuc=?, ngay_cap_nhat_truoc=?, ghi_chu=?, trang_thai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $ngay_bat_dau, $ngay_ket_thuc, $ngay_cap_nhat_truoc, $ghi_chu, $trang_thai, $id]);
        header("Location: CRUDflashsale.php?success=updated");
    } else {
        $sql = "INSERT INTO flash_sale (ten_chuong_trinh, ngay_bat_dau, ngay_ket_thuc, ngay_cap_nhat_truoc, ghi_chu, trang_thai) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $ngay_bat_dau, $ngay_ket_thuc, $ngay_cap_nhat_truoc, $ghi_chu, $trang_thai]);
        header("Location: CRUDflashsale.php?success=created");
    }
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = []; $where_sql = "";

if (!empty($search)) {
    if (is_numeric($search)) { $where_sql = " WHERE id = ? "; $params[] = (int)$search; }
    else { $where_sql = " WHERE ten_chuong_trinh LIKE ? "; $params[] = "%$search%"; }
}

$total_sql = "SELECT COUNT(*) FROM flash_sale" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_list = "SELECT * FROM flash_sale" . $where_sql . " ORDER BY ngay_bat_dau DESC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$flashSales = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Stats
$now = date('Y-m-d H:i:s');
$running = $conn->query("SELECT COUNT(*) FROM flash_sale WHERE trang_thai = 1 AND ngay_bat_dau <= '$now' AND ngay_ket_thuc >= '$now'")->fetchColumn();
$upcoming = $conn->query("SELECT COUNT(*) FROM flash_sale WHERE trang_thai = 1 AND ngay_bat_dau > '$now'")->fetchColumn();
$ended = $conn->query("SELECT COUNT(*) FROM flash_sale WHERE ngay_ket_thuc < '$now'")->fetchColumn();

$toast = ''; $toast_msg = '';
if (isset($_GET['success'])) {
    $toast = 'success';
    $map = ['created'=>'Tạo Flash Sale thành công!','updated'=>'Cập nhật Flash Sale thành công!','deleted'=>'Xóa Flash Sale thành công!'];
    $toast_msg = $map[$_GET['success']] ?? 'Thành công!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Flash Sale - EthleteHub</title>

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
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php" class="active"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quản lý Flash Sale</span>
            </nav>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-fire"></i></div>
                        <div class="stat-value"><?= number_format($total_rows) ?></div>
                        <div class="stat-label">Tổng chương trình</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-success delay-2">
                        <div class="stat-icon"><i class="fas fa-play-circle"></i></div>
                        <div class="stat-value"><?= number_format($running) ?></div>
                        <div class="stat-label">Đang chạy</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-warning delay-3">
                        <div class="stat-icon"><i class="fas fa-hourglass-start"></i></div>
                        <div class="stat-value"><?= number_format($upcoming) ?></div>
                        <div class="stat-label">Sắp diễn ra</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-danger delay-4">
                        <div class="stat-icon"><i class="fas fa-flag-checkered"></i></div>
                        <div class="stat-value"><?= number_format($ended) ?></div>
                        <div class="stat-label">Đã kết thúc</div>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <?php if ($toast): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-<?= $toast ?>" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="toast-content"><div class="toast-title">Thành công</div><div class="toast-message"><?= htmlspecialchars($toast_msg) ?></div></div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php if ($update_mode): ?><i class="fas fa-edit me-2"></i> Chỉnh sửa Flash Sale
                        <?php else: ?><i class="fas fa-plus-circle me-2"></i> Thêm Flash Sale mới<?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 form-modern">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">
                        <div class="col-md-6">
                            <label class="form-label">Tên Chương Trình</label>
                            <input type="text" name="ten_chuong_trinh" class="form-control" value="<?= htmlspecialchars($edit_data['ten_chuong_trinh']) ?>" required placeholder="VD: Khuyến mãi Tết">
                            <small class="text-muted">VD: Black Friday, 8/3, Tết...</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cập nhật trước (ngày)</label>
                            <input type="number" name="ngay_cap_nhat_truoc" class="form-control" min="1" max="30" value="<?= htmlspecialchars($edit_data['ngay_cap_nhat_truoc']) ?>">
                            <small class="text-muted">Hiển thị trước bao nhiêu ngày</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Trạng Thái</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="trang_thai" <?= $edit_data['trang_thai'] ? 'checked' : '' ?> id="trang_thai">
                                <label class="form-check-label" for="trang_thai">Kích hoạt</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày Bắt Đầu</label>
                            <input type="datetime-local" name="ngay_bat_dau" class="form-control" value="<?= $edit_data['ngay_bat_dau'] ? substr($edit_data['ngay_bat_dau'], 0, 16) : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày Kết Thúc</label>
                            <input type="datetime-local" name="ngay_ket_thuc" class="form-control" value="<?= $edit_data['ngay_ket_thuc'] ? substr($edit_data['ngay_ket_thuc'], 0, 16) : '' ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi Chú</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Mô tả chi tiết..."><?= htmlspecialchars($edit_data['ghi_chu']) ?></textarea>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <?php if ($update_mode): ?>
                                <button name="save_flash_sale" type="submit" class="btn btn-gradient-warning"><i class="fas fa-save me-1"></i> Cập nhật</button>
                                <a href="CRUDflashsale.php" class="btn btn-outline-gradient-primary">Hủy</a>
                            <?php else: ?>
                                <button name="save_flash_sale" type="submit" class="btn btn-gradient-success"><i class="fas fa-check me-1"></i> Lưu Flash Sale</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="filter-group">
                <div class="filter-title"><i class="fas fa-search"></i> Tìm kiếm Flash Sale</div>
                <form method="GET" action="CRUDflashsale.php" class="search-modern">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nhập ID hoặc tên chương trình..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDflashsale.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive animate-fade-in-up delay-2">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Chương Trình</th>
                            <th>Ngày Bắt Đầu</th>
                            <th>Ngày Kết Thúc</th>
                            <th>Cập Nhật Trước</th>
                            <th>Trạng Thái</th>
                            <th width="200">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($flashSales)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-fire-extinguisher"></i></div>
                                        <div class="empty-title">Không có Flash Sale nào</div>
                                        <div class="empty-text">Hãy tạo chương trình Flash Sale mới</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($flashSales as $sale): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($sale['id']) ?></strong></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($sale['ten_chuong_trinh']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['ngay_bat_dau'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['ngay_ket_thuc'])) ?></td>
                                    <td><?= htmlspecialchars($sale['ngay_cap_nhat_truoc']) ?> ngày</td>
                                    <td>
                                        <?php if ($sale['trang_thai']): ?>
                                            <span class="badge-modern badge-status-active"><i class="fas fa-check me-1"></i>Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge-modern badge-status-inactive"><i class="fas fa-ban me-1"></i>Tắt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="?edit=<?= htmlspecialchars($sale['id']) ?>" class="btn btn-sm btn-gradient-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                            <a href="flash-sale-products-admin.php?flash_sale_id=<?= htmlspecialchars($sale['id']) ?>" class="btn btn-sm btn-gradient-info" title="Sản phẩm"><i class="fas fa-box"></i></a>
                                            <a href="?delete=<?= htmlspecialchars($sale['id']) ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')" title="Xóa"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4 animate-fade-in delay-3">
                    <ul class="pagination pagination-modern justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-double-left"></i></a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-left"></i></a></li>
                        <?php endif; ?>
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-right"></i></a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-double-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>EthleteHub Admin © 2026</small>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('autoToast');
            if (toast) {
                setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateX(100%)'; setTimeout(()=>toast.remove(),400); }, 4000);
            }
        });
    </script>
</body>
</html>

