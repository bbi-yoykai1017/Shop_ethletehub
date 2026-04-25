<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

$db = new Database();
$conn = $db->connect();

$update_mode = false;

$edit_product = [
    'id' => '',
    'danh_muc_id' => '',
    'ten' => '',
    'mo_ta' => '',
    'gia' => '',
    'gia_goc' => '',
    'phan_tram_giam' => 0,
    'hinh_anh_chinh' => ''
];

$sql_dm = "SELECT id, ten_danh_muc FROM danh_muc ORDER BY id DESC";
$stmt_dm = $conn->prepare($sql_dm);
$stmt_dm->execute();
$list_danhmuc = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_product = $result;
        $update_mode = true;
    }
}

if (isset($_POST['save_product'])) {
    $danh_muc_id = $_POST['danh_muc_id'];
    $ten = $_POST['ten'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $gia_goc = $_POST['gia_goc'];
    $phan_tram_giam = $_POST['phan_tram_giam'];

    $hinh_anh = $_POST['hinh_anh_cu'] ?? '';

    if (isset($_FILES['hinh_anh_upload']) && $_FILES['hinh_anh_upload']['error'] == 0) {
        $target_dir = "public/";
        $file_name = basename($_FILES["hinh_anh_upload"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["hinh_anh_upload"]["tmp_name"], $target_file)) {
            $hinh_anh = $file_name;
        }
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE san_pham SET danh_muc_id=?, ten=?, mo_ta=?, gia=?, gia_goc=?, phan_tram_giam=?, hinh_anh_chinh=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$danh_muc_id, $ten, $mo_ta, $gia, $gia_goc, $phan_tram_giam, $hinh_anh, $id]);
    } else {
        $sql = "INSERT INTO san_pham (danh_muc_id, ten, mo_ta, gia, gia_goc, phan_tram_giam, hinh_anh_chinh) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$danh_muc_id, $ten, $mo_ta, $gia, $gia_goc, $phan_tram_giam, $hinh_anh]);
    }
    header("Location: CRUDproduct.php");
    exit;
}

if (isset($_GET['delete'])) {
    $sql = "DELETE FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDproduct.php");
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
        $params[] = (int)$search;
    } else {
        $where_sql = " WHERE ten LIKE ? ";
        $params[] = "%$search%";
    }
}

$total_sql = "SELECT COUNT(*) FROM san_pham" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_list = "SELECT * FROM san_pham" . $where_sql . " ORDER BY id ASC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$listproduct = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Stats
$sale_count_sql = "SELECT COUNT(*) FROM san_pham WHERE phan_tram_giam > 0";
$sale_count = $conn->query($sale_count_sql)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý sản phẩm - EthleteHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/crud-modern.css">
    <link rel="stylesheet" href="css/admin-layout.css">
</head>

<body style="background: linear-gradient(135deg, #f5f7fa 0%, #e4ecfb 100%); min-height: 100vh;">

    <!-- NAVBAR ADMIN -->
    <nav class="navbar navbar-dark shadow" style="background: var(--dark-gradient);">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold">
                <i class="fas fa-tachometer-alt me-2"></i> AthleteHub Admin
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home me-1"></i> Trang chủ
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <ul>
                <li><a href="CRUDproduct.php" class="active"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">
            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quản lý sản phẩm</span>
            </nav>

            <!-- Stats Cards -->
            <div class="row stats-row g-3">
                <div class="col-md-4">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-box"></i></div>
                        <div class="stat-value"><?= number_format($total_rows) ?></div>
                        <div class="stat-label">Tổng sản phẩm</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-warning delay-2">
                        <div class="stat-icon"><i class="fas fa-percent"></i></div>
                        <div class="stat-value"><?= number_format($sale_count) ?></div>
                        <div class="stat-label">Sản phẩm giảm giá</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-success delay-3">
                        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                        <div class="stat-value"><?= count($list_danhmuc) ?></div>
                        <div class="stat-label">Danh mục</div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php if ($update_mode): ?>
                            <i class="fas fa-edit me-2"></i> Chỉnh sửa sản phẩm
                        <?php else: ?>
                            <i class="fas fa-plus-circle me-2"></i> Thêm sản phẩm mới
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 form-modern" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">

                        <div class="col-md-2">
                            <label class="form-label"><i class="fas fa-folder me-1 text-primary"></i> Danh mục</label>
                            <select name="danh_muc_id" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php foreach ($list_danhmuc as $dm): ?>
                                    <option value="<?= $dm['id'] ?>" <?= ($update_mode && $edit_product['danh_muc_id'] == $dm['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-tag me-1 text-primary"></i> Tên sản phẩm</label>
                            <input type="text" name="ten" class="form-control" value="<?= $edit_product['ten'] ?>" required placeholder="Nhập tên sản phẩm...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-align-left me-1 text-primary"></i> Mô tả ngắn</label>
                            <input type="text" name="mo_ta" class="form-control" value="<?= $edit_product['mo_ta'] ?>" placeholder="Mô tả ngắn...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="fas fa-dollar-sign me-1 text-primary"></i> Giá gốc</label>
                            <input type="number" id="gia_goc" name="gia_goc" class="form-control" value="<?= $edit_product['gia_goc'] ?>" min="0" placeholder="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="fas fa-tags me-1 text-primary"></i> Giá bán</label>
                            <input type="number" id="gia_ban" name="gia" class="form-control" value="<?= $edit_product['gia'] ?>" min="0" required placeholder="0">
                            <div class="invalid-feedback">Giá bán không được cao hơn giá gốc!</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="fas fa-percentage me-1 text-primary"></i> Giảm (%)</label>
                            <input type="number" id="phan_tram_giam" step="1" name="phan_tram_giam" class="form-control" value="<?= $edit_product['phan_tram_giam'] ?>" required placeholder="0-100">
                            <small class="text-muted">0-100</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-image me-1 text-primary"></i> Hình ảnh</label>
                            <input type="file" name="hinh_anh_upload" class="form-control" accept="image/*">
                            <?php if ($update_mode && !empty($edit_product['hinh_anh_chinh'])): ?>
                                <small class="text-muted">Ảnh hiện tại: <?= $edit_product['hinh_anh_chinh'] ?></small>
                                <input type="hidden" name="hinh_anh_cu" value="<?= $edit_product['hinh_anh_chinh'] ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <?php if ($update_mode): ?>
                                <button name="save_product" class="btn btn-gradient-warning flex-fill"><i class="fas fa-save me-1"></i> Cập nhật</button>
                                <a href="CRUDproduct.php" class="btn btn-outline-gradient-primary flex-fill">Hủy</a>
                            <?php else: ?>
                                <button name="save_product" class="btn btn-gradient-success w-100"><i class="fas fa-plus me-1"></i> Lưu sản phẩm</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="filter-group">
                <div class="filter-title"><i class="fas fa-search"></i> Tìm kiếm sản phẩm</div>
                <form method="GET" action="CRUDproduct.php" class="search-modern">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Nhập ID hoặc tên sản phẩm..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDproduct.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive animate-fade-in-up delay-2">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Danh mục</th>
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Giá bán</th>
                            <th>Giá gốc</th>
                            <th>Giảm</th>
                            <th>Hình ảnh</th>
                            <th width="160">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listproduct)): ?>
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                        <div class="empty-title">Không tìm thấy sản phẩm nào</div>
                                        <div class="empty-text">Vui lòng thử tìm kiếm khác hoặc thêm sản phẩm mới</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($listproduct as $product) { ?>
                                <tr>
                                    <td><strong>#<?= $product['id'] ?></strong></td>
                                    <td><span class="badge bg-light text-dark border"><?= $product['danh_muc_id'] ?></span></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($product['ten']) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($product['mo_ta']) ?></td>
                                    <td class="fw-bold text-primary"><?= number_format($product['gia'], 0, ',', '.') ?>đ</td>
                                    <td class="text-muted"><s><?= number_format($product['gia_goc'], 0, ',', '.') ?>đ</s></td>
                                    <td>
                                        <?php if ($product['phan_tram_giam'] > 0): ?>
                                            <span class="badge bg-danger">-<?= $product['phan_tram_giam'] ?>%</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <img src="./public/<?= htmlspecialchars($product['hinh_anh_chinh']) ?>" alt="<?= htmlspecialchars($product['ten']) ?>" class="table-product-img">
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="?edit=<?= $product['id'] ?>" class="btn btn-sm btn-gradient-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                                            <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Xóa sản phẩm này?')" title="Xóa"><i class="fas fa-trash"></i></a>
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
                        Hiển thị trang <?= $page ?> / <?= $total_pages ?> — Tổng <?= number_format($total_rows) ?> sản phẩm
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>EthleteHub Admin © 2026</small>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const giaGocInput = document.getElementById('gia_goc');
            const giaBanInput = document.getElementById('gia_ban');
            const phanTramInput = document.getElementById('phan_tram_giam');
            const btnSave = document.querySelector('button[name="save_product"]');

            function validateForm(e) {
                let giaGoc = parseFloat(giaGocInput.value) || 0;
                let giaBan = parseFloat(giaBanInput.value) || 0;
                let phanTram = parseFloat(phanTramInput.value) || 0;
                let isValid = true;
                const activeId = e ? e.target.id : "";

                if (giaGoc > 0) {
                    if (activeId === 'gia_ban') {
                        if (giaBanInput.value === "") { phanTramInput.value = ""; }
                        else if (giaBan <= giaGoc) { phanTramInput.value = (((giaGoc - giaBan) / giaGoc) * 100).toFixed(1); }
                    } else if (activeId === 'phan_tram_giam') {
                        if (phanTramInput.value === "") { giaBanInput.value = giaGoc; }
                        else if (phanTram >= 0 && phanTram <= 100) {
                            giaBanInput.value = Math.round(giaGoc * (1 - phanTram / 100));
                            giaBan = parseFloat(giaBanInput.value);
                        }
                    } else if (activeId === 'gia_goc' && phanTramInput.value !== "") {
                        giaBanInput.value = Math.round(giaGoc * (1 - phanTram / 100));
                        giaBan = parseFloat(giaBanInput.value);
                    }
                }
                if (giaGoc > 0 && giaBan > giaGoc) { giaBanInput.classList.add('is-invalid'); isValid = false; }
                else { giaBanInput.classList.remove('is-invalid'); if (giaGoc > 0 && giaBan > 0) giaBanInput.classList.add('is-valid'); }

                if (phanTramInput.value !== "" && (phanTram < 0 || phanTram > 100)) {
                    phanTramInput.classList.add('is-invalid'); phanTramInput.classList.remove('is-valid'); isValid = false;
                } else {
                    phanTramInput.classList.remove('is-invalid');
                    if (phanTramInput.value !== "") phanTramInput.classList.add('is-valid');
                }
                if (btnSave) btnSave.disabled = !isValid;
            }
            [giaGocInput, giaBanInput, phanTramInput].forEach(input => {
                input.addEventListener('input', (e) => validateForm(e));
            });
        });
    </script>
</body>
</html>

