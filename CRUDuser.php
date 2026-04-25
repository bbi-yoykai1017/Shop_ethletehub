<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

$db = new Database();
$conn = $db->connect();

$update_mode = false;
$edit_user = ['id' => '', 'ten' => '', 'email' => '', 'so_dien_thoai' => '', 'vai_tro' => '', 'trang_thai' => 'hoat_dong'];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_user = $result;
        $update_mode = true;
    }
}

if (isset($_POST['save_user']) && isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];
    $ten = $_POST['ten'];
    $email = $_POST['email'];
    $sdt = $_POST['so_dien_thoai'];
    $vai_tro = $_POST['vai_tro'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "UPDATE nguoi_dung SET ten=?, email=?, so_dien_thoai=?, vai_tro=?, trang_thai=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$ten, $email, $sdt, $vai_tro, $trang_thai, $id]);
    header("Location: CRUDuser.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "UPDATE nguoi_dung SET trang_thai = 'bi_khoa' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$id])) {
        header("Location: CRUDuser.php?msg=locked");
    } else {
        header("Location: CRUDuser.php?msg=error");
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
        $where_sql = " WHERE ten LIKE ? ";
        $params[] = "%$search%";
    }
}

$total_sql = "SELECT COUNT(*) FROM nguoi_dung" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_list = "SELECT * FROM nguoi_dung" . $where_sql . " ORDER BY id ASC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$listusers = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Stats
$admin_count = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'admin'")->fetchColumn();
$active_count = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE trang_thai = 'hoat_dong'")->fetchColumn();
$locked_count = $conn->query("SELECT COUNT(*) FROM nguoi_dung WHERE trang_thai = 'bi_khoa'")->fetchColumn();

$toast_type = '';
$toast_message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'locked') { $toast_type = 'success'; $toast_message = 'Đã khóa tài khoản ngườidùng!'; }
    elseif ($_GET['msg'] === 'error') { $toast_type = 'error'; $toast_message = 'Có lỗi xảy ra khi khóa tài khoản!'; }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý ngưởi dùng - EthleteHub</title>

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
                <li><a href="CRUDuser.php" class="active"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quản lý ngưởi dùng</span>
            </nav>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-value"><?= number_format($total_rows) ?></div>
                        <div class="stat-label">Tổng ngưởi dùng</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-info delay-2">
                        <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="stat-value"><?= number_format($admin_count) ?></div>
                        <div class="stat-label">Admin</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-success delay-3">
                        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                        <div class="stat-value"><?= number_format($active_count) ?></div>
                        <div class="stat-label">Hoạt động</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-danger delay-4">
                        <div class="stat-icon"><i class="fas fa-user-lock"></i></div>
                        <div class="stat-value"><?= number_format($locked_count) ?></div>
                        <div class="stat-label">Bị khóa</div>
                    </div>
                </div>
            </div>

            <!-- Toast -->
            <?php if ($toast_type): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-<?= $toast_type ?>" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-<?= $toast_type === 'success' ? 'check' : 'exclamation' ?>-circle"></i></div>
                        <div class="toast-content">
                            <div class="toast-title"><?= $toast_type === 'success' ? 'Thành công' : 'Lỗi' ?></div>
                            <div class="toast-message"><?= htmlspecialchars($toast_message) ?></div>
                        </div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <?php if ($update_mode): ?>
                <div class="glass-card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Cập nhật thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3 form-modern">
                            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label"><i class="fas fa-user me-1 text-warning"></i> Tên khách hàng</label>
                                <input type="text" name="ten" class="form-control" placeholder="Nhập tên..." value="<?= $edit_user['ten'] ?>" minlength="2" maxlength="100" required>
                                <div class="invalid-feedback">Tên khách hàng không được để trống!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label"><i class="fas fa-envelope me-1 text-warning"></i> Email</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= $edit_user['email'] ?>" required>
                                <div class="invalid-feedback">Email không hợp lệ!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label"><i class="fas fa-phone me-1 text-warning"></i> Số điện thoại</label>
                                <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control" placeholder="SĐT" value="<?= $edit_user['so_dien_thoai'] ?>" minlength="10" maxlength="10" required>
                                <div class="invalid-feedback">SĐT không hợp lệ!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label"><i class="fas fa-id-badge me-1 text-warning"></i> Vai trò</label>
                                <select class="form-select" name="vai_tro" required>
                                    <option value="khach_hang" <?= ($edit_user['vai_tro'] === 'khach_hang') ? 'selected' : '' ?>>Khách Hàng</option>
                                    <option value="admin" <?= ($edit_user['vai_tro'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label"><i class="fas fa-toggle-on me-1 text-warning"></i> Trạng thái</label>
                                <select class="form-select" name="trang_thai" required>
                                    <option value="hoat_dong" <?= ($edit_user['trang_thai'] === 'hoat_dong') ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="bi_khoa" <?= ($edit_user['trang_thai'] === 'bi_khoa') ? 'selected' : '' ?>>Bị khóa</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                <a href="CRUDuser.php" class="btn btn-outline-gradient-primary">Hủy bỏ</a>
                                <button type="submit" name="save_user" class="btn btn-gradient-warning px-4"><i class="fas fa-save me-1"></i> Xác nhận cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search -->
            <div class="filter-group">
                <div class="filter-title"><i class="fas fa-search"></i> Tìm kiếm khách hàng</div>
                <form method="GET" action="CRUDuser.php" class="search-modern">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nhập ID hoặc tên khách..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDuser.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive animate-fade-in-up delay-2">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th>Thông tin khách hàng</th>
                            <th class="text-center d-none d-md-table-cell">SĐT</th>
                            <th class="text-center">Vai trò</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listusers)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-users-slash"></i></div>
                                        <div class="empty-title">Không tìm thấy ngưởi dùng nào</div>
                                        <div class="empty-text">Vui lòng thử tìm kiếm khác</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listusers as $user): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted">#<?= $user['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle" style="width:36px;height:36px;background:var(--primary-gradient);color:white;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;font-weight:700;font-size:0.9rem;">
                                                <?= strtoupper(mb_substr($user['ten'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($user['ten'] ?? '') ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?></td>
                                    <td class="text-center">
                                        <?php if ($user['vai_tro'] === 'admin'): ?>
                                            <span class="badge-modern badge-status-processing"><i class="fas fa-shield-alt me-1"></i>Admin</span>
                                        <?php else: ?>
                                            <span class="badge-modern badge-status-draft">Khách hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($user['trang_thai'] === 'hoat_dong'): ?>
                                            <span class="badge-modern badge-status-active">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge-modern badge-status-inactive">Bị khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-gradient-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                            <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Bạn có chắc chắn muốn KHÓA ngưởi dùng này không?')" title="Khóa tài khoản"><i class="fas fa-user-slash"></i></a>
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
                        Hiển thị trang <?= $page ?> / <?= $total_pages ?> — Tổng <?= number_format($total_rows) ?> ngưởi dùng
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
            const tenInput = document.querySelector('input[name="ten"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.getElementById('so_dien_thoai');
            const btnSave = document.querySelector('button[name="save_user"]');

            function validateForm() {
                let isValid = true;
                let tenRaw = tenInput ? tenInput.value : '';
                let tenClean = tenRaw.trim().replace(/\s\s+/g, ' ');
                const vietnameseRegex = /^[\p{L}\s]+$/u;
                const isDoubleSpace = /\s\s+/.test(tenRaw);
                const isInvalidChar = !vietnameseRegex.test(tenClean);
                const isTooShort = tenClean.length < 2;
                const tenHasError = isDoubleSpace || isInvalidChar || isTooShort;

                if (tenInput) {
                    if (!tenHasError) { tenInput.classList.remove('is-invalid'); tenInput.classList.add('is-valid'); }
                    else { tenInput.classList.add('is-invalid'); tenInput.classList.remove('is-valid'); isValid = false; }
                }

                let emailRaw = emailInput ? emailInput.value : '';
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const hasSpaceEmail = /\s/.test(emailRaw);
                if (emailInput) {
                    if (emailRegex.test(emailRaw) && !hasSpaceEmail) { emailInput.classList.remove('is-invalid'); emailInput.classList.add('is-valid'); }
                    else { emailInput.classList.add('is-invalid'); emailInput.classList.remove('is-valid'); isValid = false; }
                }

                let phoneRaw = phoneInput ? phoneInput.value : '';
                const phoneRegex = /^0\d{9}$/;
                const hasSpacePhone = /\s/.test(phoneRaw);
                if (phoneInput) {
                    if (phoneRegex.test(phoneRaw) && !hasSpacePhone) { phoneInput.classList.remove('is-invalid'); phoneInput.classList.add('is-valid'); }
                    else { phoneInput.classList.add('is-invalid'); phoneInput.classList.remove('is-valid'); isValid = false; }
                }
                if (btnSave) btnSave.disabled = !isValid;
            }

            if (tenInput) tenInput.addEventListener('input', validateForm);
            if (emailInput) emailInput.addEventListener('input', validateForm);
            if (phoneInput) phoneInput.addEventListener('input', validateForm);
            validateForm();

            // Auto hide toast
            const toast = document.getElementById('autoToast');
            if (toast) {
                setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(() => toast.remove(), 400); }, 4000);
            }
        });
    </script>
</body>
</html>

