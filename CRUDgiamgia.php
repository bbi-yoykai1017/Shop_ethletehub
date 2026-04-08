<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

$db = new Database();
$conn = $db->connect();

$update_mode = false;
$edit_data = [
    'id' => '',
    'ma_code' => '',
    'mo_ta' => '',
    'phan_tram_giam' => 0,
    'so_tien_giam' => 0,
    'don_hang_toi_thieu' => 0,
    'giam_toi_da' => 0,
    'gioi_han_su_dung' => 0,
    'da_su_dung' => 0
];

// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA =================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM ma_giam_gia WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_data = $result;
        $update_mode = true;
    }
}

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_discount'])) {
    $code = $_POST['ma_code'];
    $mota = $_POST['mo_ta'];
    $phan_tram = $_POST['phan_tram_giam'];
    $so_tien = $_POST['so_tien_giam'];
    $toi_thieu = $_POST['don_hang_toi_thieu'];
    $toi_da = $_POST['giam_toi_da'];
    $gioi_han = $_POST['gioi_han_su_dung'];
    $da_su_dung = $_POST['da_su_dung'];

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

// ================= 3. XỬ LÝ XÓA =================
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM ma_giam_gia WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDgiamgia.php");
    exit;
}
// ================= 4. XỬ LÝ TÌM KIẾM & PHÂN TRANG =================

// Cấu hình phân trang
$limit = 10; // Số dòng trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Xử lý từ khóa tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];
$where_sql = "";

if (!empty($search)) {
    // Nếu nội dung tìm kiếm là số, ưu tiên tìm chính xác ID san pham
    if (is_numeric($search)) {
        $where_sql = " WHERE id = ?  ";
        $params[] = $search; // Tìm chính xác số ID
       // $params[] = "%$search%"; // Hoặc tuong doi
    } else {
        // Nếu là chữ, tìm gần đúng theo ten
        $where_sql = " WHERE ma_code LIKE ? ";
        $params[] = "%$search%";
    }
}

// Đếm tổng số dòng để tính số trang
$total_sql = "SELECT COUNT(*) FROM ma_giam_gia " . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu theo trang và tìm kiếm
$sql_list = "SELECT * FROM ma_giam_gia " . $where_sql . " ORDER BY id ASC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$list = $stmt_list->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý giảm giá- EthleteHub</title>



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
                <i class="bi bi-speedometer2"></i> AthleteHub Admin
            </span>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-house"></i> Trang chủ
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
                <li><a href="CRUDgiamgia.php" class="active"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">
            <div class="card shadow-lg border-0">

                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <?php if ($update_mode): ?>
                                <i class="fas fa-bolt text-warning me-2"></i> Cập nhật mã giảm giá <span class="badge bg-light text-primary"></span>
                            <?php else: ?>
                                <i class="fas fa-plus-square text-info me-2"></i> Thêm mã giảm giá mới
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Mã Code</label>
                                <input type="text" name="ma_code" class="form-control" value="<?= $edit_data['ma_code'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mô tả</label>
                                <input type="text" name="mo_ta" class="form-control" value="<?= $edit_data['mo_ta'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Phần trăm giảm (%)</label>
                                <input type="number" name="phan_tram_giam" class="form-control" value="<?= $edit_data['phan_tram_giam'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Số tiền giảm </label>
                                <input type="number" name="so_tien_giam" class="form-control" value="<?= $edit_data['so_tien_giam'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Đơn tối thiểu </label>
                                <input type="number" name="don_hang_toi_thieu" class="form-control" value="<?= $edit_data['don_hang_toi_thieu'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Giảm tối đa (đ)</label>
                                <input type="number" name="giam_toi_da" class="form-control" value="<?= $edit_data['giam_toi_da'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Giới hạn sử dụng</label>
                                <input type="number" name="gioi_han_su_dung" class="form-control" value="<?= $edit_data['gioi_han_su_dung'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Đã sử dụng</label>
                                <input type="number" name="da_su_dung" class="form-control" value="<?= $edit_data['da_su_dung'] ?>">
                            </div>


                            <div class="col-12 text-end">
                                <?php if ($update_mode): ?>
                                    <button name="save_discount" class="btn btn-warning">Cập nhật</button>
                                    <a href="CRUDgiamgia.php" class="btn btn-secondary">Hủy</a>
                                <?php else: ?>
                                    <button name="save_discount" class="btn btn-success">Thêm mới</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Tim kiem -->
                <div class="filter-group mb-3">
                    <h4 class="filter-title"><i class="fas fa-search"></i> Tìm kiếm</h4>
                    <form method="GET" action="CRUDgiamgia.php" class="search-box d-flex gap-2">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Nhập ID  hoặc mã giảm giá..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                        <?php if (!empty($search)): ?>
                            <a href="CRUDgiamgia.php" class="btn btn-outline-secondary">Xóa</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Mã Code</th>
                                <th>Mô tả</th>
                                <th>Phần trăm giảm</th>
                                <th>Số tiền giảm</th>
                                <th>Đơn hàng tối thiểu</th>
                                <th>Giảm tối đa</th>
                                <th>Giới hạn sử dụng</th>
                                <th>Đã sử dụng</th>
                                <th width="180">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($list as $giamgia) { ?>
                                <tr>
                                    <td><?= $giamgia['id'] ?></td>
                                    <td><strong><?= $giamgia['ma_code'] ?></strong></td>
                                    <td><?= $giamgia['mo_ta'] ?></td>
                                    <td><?= $giamgia['phan_tram_giam'] ?>%</td>
                                    <td><?= number_format($giamgia['so_tien_giam'] ?? 0) ?></td>
                                    <td><?= number_format($giamgia['don_hang_toi_thieu'] ?? 0) ?></td>
                                    <td><?= number_format($giamgia['giam_toi_da'] ?? 0) ?></td>
                                    <td><?= $giamgia['gioi_han_su_dung'] ?></td>
                                    <td><?= $giamgia['da_su_dung'] ?></td>
                                    <td>
                                        <a href="?edit=<?= $giamgia['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="?delete=<?= $giamgia['id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa mã <?= $giamgia['ma_code'] ?>?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>

                </div>
                <div class="pagination-section">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                <i class="fas fa-chevron-left"></i> Trước
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                <span>Tiếp</span> <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="text-center mt-2 small text-muted">
                    Hiển thị trang <?= $page ?> / <?= $total_pages ?> (Tổng <?= $total_rows ?> Mã giảm)
                </div>
            </div>
        </div>

    </div>

    </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>

</body>

</html>