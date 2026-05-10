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
    'id' => '', 'ma_code' => '', 'mo_ta' => '', 'phan_tram_giam' => 0,
    'so_tien_giam' => 0, 'don_hang_toi_thieu' => 0, 'giam_toi_da' => 0,
    'gioi_han_su_dung' => 0, 'da_su_dung' => 0
];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM ma_giam_gia WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) { $edit_data = $result; $update_mode = true; }
}

if (isset($_POST['save_discount'])) {
    $code = $_POST['ma_code']; $mota = $_POST['mo_ta']; $phan_tram = $_POST['phan_tram_giam'];
    $so_tien = $_POST['so_tien_giam']; $toi_thieu = $_POST['don_hang_toi_thieu'];
    $toi_da = $_POST['giam_toi_da']; $gioi_han = $_POST['gioi_han_su_dung']; $da_su_dung = $_POST['da_su_dung'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE ma_giam_gia SET ma_code=?, mo_ta=?, phan_tram_giam=?, so_tien_giam=?, don_hang_toi_thieu=?, giam_toi_da=?, gioi_han_su_dung=?, da_su_dung=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$code, $mota, $phan_tram, $so_tien, $toi_thieu, $toi_da, $gioi_han, $da_su_dung, $id]);
    } else {
        $sql = "INSERT INTO ma_giam_gia (ma_code, mo_ta, phan_tram_giam, so_tien_giam, don_hang_toi_thieu, giam_toi_da, gioi_han_su_dung, da_su_dung) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$code, $mota, $phan_tram, $so_tien, $toi_thieu, $toi_da, $gioi_han, $da_su_dung]);
    }
    header("Location: CRUDgiamgia.php");
    exit;
}

if (isset($_GET['delete'])) {
    $sql = "DELETE FROM ma_giam_gia WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDgiamgia.php");
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = []; $where_sql = "";

if (!empty($search)) {
    if (is_numeric($search)) { $where_sql = " WHERE id = ?  "; $params[] = $search; }
    else { $where_sql = " WHERE ma_code LIKE ? "; $params[] = "%$search%"; }
}

$total_sql = "SELECT COUNT(*) FROM ma_giam_gia " . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_list = "SELECT * FROM ma_giam_gia " . $where_sql . " ORDER BY id ASC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$list = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Stats
$used_up = $conn->query("SELECT COUNT(*) FROM ma_giam_gia WHERE gioi_han_su_dung > 0 AND da_su_dung >= gioi_han_su_dung")->fetchColumn();
$active_codes = $conn->query("SELECT COUNT(*) FROM ma_giam_gia WHERE gioi_han_su_dung = 0 OR da_su_dung < gioi_han_su_dung")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý giảm giá - EthleteHub</title>

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
                <li><a href="CRUDgiamgia.php" class="active"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
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
                <span class="current">Quản lý mã giảm giá</span>
            </nav>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-tags"></i></div>
                        <div class="stat-value"><?= number_format($total_rows) ?></div>
                        <div class="stat-label">Tổng mã giảm giá</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-success delay-2">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-value"><?= number_format($active_codes) ?></div>
                        <div class="stat-label">Còn hiệu lực</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-danger delay-3">
                        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                        <div class="stat-value"><?= number_format($used_up) ?></div>
                        <div class="stat-label">Đã dùng hết</div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php if ($update_mode): ?><i class="fas fa-bolt text-warning me-2"></i> Cập nhật mã giảm giá
                        <?php else: ?><i class="fas fa-plus-square text-info me-2"></i> Thêm mã giảm giá mới<?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 form-modern">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <div class="col-md-3">
                            <label class="form-label">Mã Code</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-barcode input-icon"></i>
                                <input type="text" name="ma_code" class="form-control" value="<?= $edit_data['ma_code'] ?>" required placeholder="VD: SALE20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mô tả</label>
                            <input type="text" name="mo_ta" class="form-control" value="<?= $edit_data['mo_ta'] ?>" placeholder="Mô tả ngắn...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phần trăm giảm (%)</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-percent input-icon"></i>
                                <input type="number" name="phan_tram_giam" class="form-control" value="<?= $edit_data['phan_tram_giam'] ?>" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Số tiền giảm</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-coins input-icon"></i>
                                <input type="number" name="so_tien_giam" class="form-control" value="<?= $edit_data['so_tien_giam'] ?>" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đơn tối thiểu</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-shopping-cart input-icon"></i>
                                <input type="number" name="don_hang_toi_thieu" class="form-control" value="<?= $edit_data['don_hang_toi_thieu'] ?>" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Giảm tối đa (đ)</label>
                            <input type="number" name="giam_toi_da" class="form-control" value="<?= $edit_data['giam_toi_da'] ?>" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Giới hạn sử dụng</label>
                            <input type="number" name="gioi_han_su_dung" class="form-control" value="<?= $edit_data['gioi_han_su_dung'] ?>" placeholder="0 = không giới hạn">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đã sử dụng</label>
                            <input type="number" name="da_su_dung" class="form-control" value="<?= $edit_data['da_su_dung'] ?>" placeholder="0">
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <?php if ($update_mode): ?>
                                <button name="save_discount" class="btn btn-gradient-warning"><i class="fas fa-save me-1"></i> Cập nhật</button>
                                <a href="CRUDgiamgia.php" class="btn btn-outline-gradient-primary">Hủy</a>
                            <?php else: ?>
                                <button name="save_discount" class="btn btn-gradient-success"><i class="fas fa-plus me-1"></i> Thêm mới</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="filter-group">
                <div class="filter-title"><i class="fas fa-search"></i> Tìm kiếm mã giảm giá</div>
                <form method="GET" action="CRUDgiamgia.php" class="search-modern">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nhập ID hoặc mã giảm giá..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDgiamgia.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive animate-fade-in-up delay-2">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Code</th>
                            <th>Mô tả</th>
                            <th>Phần trăm</th>
                            <th>Số tiền</th>
                            <th>Đơn tối thiểu</th>
                            <th>Giảm tối đa</th>
                            <th>Giới hạn</th>
                            <th>Đã dùng</th>
                            <th width="140">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-tags"></i></div>
                                        <div class="empty-title">Không có mã giảm giá nào</div>
                                        <div class="empty-text">Hãy thêm mã giảm giá mới</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $giamgia) { ?>
                                <tr>
                                    <td><strong>#<?= $giamgia['id'] ?></strong></td>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($giamgia['ma_code']) ?></span></td>
                                    <td class="text-muted"><?= htmlspecialchars($giamgia['mo_ta']) ?></td>
                                    <td><?= $giamgia['phan_tram_giam'] ?>%</td>
                                    <td><?= number_format($giamgia['so_tien_giam'] ?? 0, 0, ',', '.') ?>đ</td>
                                    <td><?= number_format($giamgia['don_hang_toi_thieu'] ?? 0, 0, ',', '.') ?>đ</td>
                                    <td><?= number_format($giamgia['giam_toi_da'] ?? 0, 0, ',', '.') ?>đ</td>
                                    <td><?= $giamgia['gioi_han_su_dung'] ?: '∞' ?></td>
                                    <td>
                                        <?php if ($giamgia['gioi_han_su_dung'] > 0 && $giamgia['da_su_dung'] >= $giamgia['gioi_han_su_dung']): ?>
                                            <span class="badge-modern badge-status-inactive"><?= $giamgia['da_su_dung'] ?></span>
                                        <?php else: ?>
                                            <span class="badge-modern badge-status-active"><?= $giamgia['da_su_dung'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="?edit=<?= $giamgia['id'] ?>" class="btn btn-sm btn-gradient-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                            <a href="?delete=<?= $giamgia['id'] ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Xóa mã <?= htmlspecialchars($giamgia['ma_code']) ?>?')" title="Xóa"><i class="fas fa-trash"></i></a>
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
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-left"></i></a></li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"><i class="fas fa-chevron-right"></i></a></li>
                        </ul>
                    </nav>
                    <div class="text-center mt-2 small text-muted">Hiển thị trang <?= $page ?> / <?= $total_pages ?> — Tổng <?= number_format($total_rows) ?> mã giảm</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>EthleteHub Admin © 2026</small>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

