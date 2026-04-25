<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Xử lý thêm/sửa/xóa
$update_mode = false;
$edit_data = [
    'id' => '',
    'ten_chuong_trinh' => '',
    'ngay_bat_dau' => '',
    'ngay_ket_thuc' => '',
    'ngay_cap_nhat_truoc' => 1,
    'ghi_chu' => '',
    'trang_thai' => 1
];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $sql = "DELETE FROM flash_sale WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$id])) {
        header("Location: CRUDflashsale.php?success=deleted");
        exit;
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
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
    $ngay_cap_nhat_truoc = (int) $_POST['ngay_cap_nhat_truoc'];
    $ghi_chu = $_POST['ghi_chu'];
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int) $_POST['id'];
        $sql = "UPDATE flash_sale SET ten_chuong_trinh=?, ngay_bat_dau=?, ngay_ket_thuc=?, ngay_cap_nhat_truoc=?, ghi_chu=?, trang_thai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $ngay_bat_dau, $ngay_ket_thuc, $ngay_cap_nhat_truoc, $ghi_chu, $trang_thai, $id]);
        header("Location: CRUDflashsale.php?success=updated");
    } else {
        // Insert
        $sql = "INSERT INTO flash_sale (ten_chuong_trinh, ngay_bat_dau, ngay_ket_thuc, ngay_cap_nhat_truoc, ghi_chu, trang_thai) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $ngay_bat_dau, $ngay_ket_thuc, $ngay_cap_nhat_truoc, $ghi_chu, $trang_thai]);
        header("Location: CRUDflashsale.php?success=created");
    }
    exit;
}

// Tìm kiếm & phân trang
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
        $where_sql = " WHERE ten_chuong_trinh LIKE ? ";
        $params[] = "%$search%";
    }
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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Flash Sale - EthleteHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link href="css/crud.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/admin-layout.css">
    <link rel="stylesheet" href="css/page-link.css">
</head>

<body style="background:#f4f6f9;">

    <!-- NAVBAR ADMIN -->
    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold">
                <i class="fas fa-fire"></i> AthleteHub Admin
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home"></i> Trang chủ
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="layout">

        <!-- SIDEBAR -->
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

        <!-- NỘI DUNG -->
        <div class="main-content">

            <!-- SUCCESS/ERROR MESSAGES -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php 
                        $messages = [
                            'created' => 'Tạo Flash Sale thành công!',
                            'updated' => 'Cập nhật Flash Sale thành công!',
                            'deleted' => 'Xóa Flash Sale thành công!'
                        ];
                        echo $messages[$_GET['success']] ?? 'Thành công!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM THÊM/SỬA -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <?php if ($update_mode): ?>
                            <i class="fas fa-edit me-2"></i> Chỉnh sửa Flash Sale
                        <?php else: ?>
                            <i class="fas fa-plus-circle me-2"></i> Thêm Flash Sale mới
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên Chương Trình</label>
                            <input type="text" name="ten_chuong_trinh" class="form-control" 
                                   value="<?= htmlspecialchars($edit_data['ten_chuong_trinh']) ?>" required>
                            <small class="text-muted">VD: Khuyến mãi Tết, Black Friday, 8/3, etc...</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Cập nhật trước (ngày)</label>
                            <input type="number" name="ngay_cap_nhat_truoc" class="form-control" 
                                   min="1" max="30" value="<?= htmlspecialchars($edit_data['ngay_cap_nhat_truoc']) ?>">
                            <small class="text-muted">Hiển thị bao nhiêu ngày trước</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Trạng Thái</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="trang_thai" 
                                       <?= $edit_data['trang_thai'] ? 'checked' : '' ?> id="trang_thai">
                                <label class="form-check-label" for="trang_thai">Kích hoạt</label>
                            </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày Bắt Đầu</label>
                            <input type="datetime-local" name="ngay_bat_dau" class="form-control" 
                                   value="<?= $edit_data['ngay_bat_dau'] ? substr($edit_data['ngay_bat_dau'], 0, 16) : '' ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày Kết Thúc</label>
                            <input type="datetime-local" name="ngay_ket_thuc" class="form-control" 
                                   value="<?= $edit_data['ngay_ket_thuc'] ? substr($edit_data['ngay_ket_thuc'], 0, 16) : '' ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Ghi Chú</label>
                            <textarea name="ghi_chu" class="form-control" rows="2"><?= htmlspecialchars($edit_data['ghi_chu']) ?></textarea>
                            <small class="text-muted">Mô tả chi tiết về chương trình khuyến mãi</small>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <?php if ($update_mode): ?>
                                <button name="save_flash_sale" type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i> Cập nhật
                                </button>
                                <a href="CRUDflashsale.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Hủy
                                </a>
                            <?php else: ?>
                                <button name="save_flash_sale" type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Lưu Flash Sale
                                </button>
                            <?php endif; ?>
                            <span class="badge bg-secondary ms-auto align-self-center">
                                Tổng cộng: <?= $total_rows ?> Flash Sale
                            </span>
                        </div>
                    </form>
                </div>

            <!-- TÌM KIẾM -->
            <div class="filter-group mb-3">
                <h4 class="filter-title"><i class="fas fa-search"></i> Tìm kiếm</h4>
                <form method="GET" action="CRUDflashsale.php" class="search-box d-flex gap-2">
                    <input type="text" name="search" class="form-control"
                        placeholder="Nhập ID hoặc tên chương trình..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDflashsale.php" class="btn btn-outline-secondary">Xóa</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- DANH SÁCH FLASH SALE -->
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên Chương Trình</th>
                            <th>Ngày Bắt Đầu</th>
                            <th>Ngày Kết Thúc</th>
                            <th>Cập Nhật Trước</th>
                            <th>Trạng Thái</th>
                            <th width="220">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($flashSales)): ?>
                            <tr>
                                <td colspan="7" class="text-muted py-4">
                                    <i class="fas fa-inbox"></i> Không có Flash Sale nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($flashSales as $sale): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($sale['id']) ?></strong></td>
                                    <td><?= htmlspecialchars($sale['ten_chuong_trinh']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['ngay_bat_dau'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['ngay_ket_thuc'])) ?></td>
                                    <td><small><?= htmlspecialchars($sale['ngay_cap_nhat_truoc']) ?> ngày</small></td>
                                    <td>
                                        <?php if ($sale['trang_thai']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fas fa-ban"></i> Tắt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?edit=<?= htmlspecialchars($sale['id']) ?>" class="btn btn-primary btn-sm me-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="flash-sale-products-admin.php?flash_sale_id=<?= htmlspecialchars($sale['id']) ?>" 
                                           class="btn btn-info btn-sm me-1" title="Sản phẩm">
                                            <i class="fas fa-box"></i>
                                        </a>
                                        <a href="?delete=<?= htmlspecialchars($sale['id']) ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PHÂN TRANG -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
