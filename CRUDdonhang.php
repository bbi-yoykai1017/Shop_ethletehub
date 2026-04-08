<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

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

// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA =================
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

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_order'])) {
    $user_id = $_POST['nguoi_dung_id'];
    $ma_don = $_POST['ma_don_hang'];
    $tong = $_POST['tong_tien'];
    $giam = $_POST['tien_giam'];
    $thanh_tien = $_POST['thanh_tien'];
    $pttt = $_POST['phuong_thuc_thanh_toan'];
    $trang_thai = $_POST['trang_thai'];

    if ($user_id <= 0) {
        // Nếu ID <= 0, hiện thông báo và dừng thực thi, quay lại trang trước
        echo "<script>
                alert('Lỗi: ID Người dùng phải là số dương (lớn hơn 0)!');
                window.history.back();
              </script>";
        exit; // Dừng chương trình tại đây không cho lưu vào DB
    }
    // --- KẾT THÚC PHẦN KIỂM TRA ---

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // CẬP NHẬT
        $id = $_POST['id'];
        $sql = "UPDATE don_hang SET nguoi_dung_id=?, ma_don_hang=?, tong_tien=?, tien_giam=?, thanh_tien=?, phuong_thuc_thanh_toan=?, trang_thai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $ma_don, $tong, $giam, $thanh_tien, $pttt, $trang_thai, $id]);
    } else {
        // THÊM MỚI
        $sql = "INSERT INTO don_hang (nguoi_dung_id, ma_don_hang, tong_tien, tien_giam, thanh_tien, phuong_thuc_thanh_toan, trang_thai) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $ma_don, $tong, $giam, $thanh_tien, $pttt, $trang_thai]);
    }
    header("Location: CRUDdonhang.php");
    exit;
}

// ================= 3. XỬ LÝ XÓA =================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        // Bắt đầu một Transaction để đảm bảo an toàn dữ liệu
        // Nếu xóa bảng 1 lỗi thì bảng 2 sẽ không bị xóa theo
        $conn->beginTransaction();

        // 1. Xóa tất cả chi tiết của đơn hàng này trước (Bảng con)
        $sql_delete_items = "DELETE FROM chi_tiet_don_hang WHERE don_hang_id = ?";
        $stmt_items = $conn->prepare($sql_delete_items);
        $stmt_items->execute([$id]);

        // 2. Sau đó mới xóa đơn hàng (Bảng cha)
        $sql_delete_order = "DELETE FROM don_hang WHERE id = ?";
        $stmt_order = $conn->prepare($sql_delete_order);
        $stmt_order->execute([$id]);

        // Hoàn tất giao dịch
        $conn->commit();

        header("Location: CRUDdonhang.php?success=deleted");
    } catch (Exception $e) {
        // Nếu có lỗi xảy ra, hoàn tác lại toàn bộ
        $conn->rollBack();
        error_log($e->getMessage());
        header("Location: CRUDdonhang.php?error=delete_failed");
    }
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
    // Nếu nội dung tìm kiếm là số, ưu tiên tìm chính xác ID người dùng
    if (is_numeric($search)) {
        $where_sql = " WHERE nguoi_dung_id = ? OR ma_don_hang LIKE ? ";
        $params[] = $search; // Tìm chính xác số ID
        $params[] = "%$search%"; // Hoặc mã đơn hàng chứa số đó
    } else {
        // Nếu là chữ, tìm gần đúng theo mã đơn hàng
        $where_sql = " WHERE ma_don_hang LIKE ? ";
        $params[] = "%$search%";
    }
}

// Đếm tổng số dòng để tính số trang
$total_sql = "SELECT COUNT(*) FROM don_hang" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu theo trang và tìm kiếm
$sql_list = "SELECT * FROM don_hang" . $where_sql . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$listorders = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
//$listorders = getAllOrders($conn);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý đơn hàng - EthleteHub</title>



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
    <style>
        .page-link {
            display: flex;
            align-items: center;
            /* Căn giữa chữ và icon theo chiều dọc */
            gap: 5px;
            /* Tạo khoảng cách nhỏ giữa chữ và icon */
            font-size: 0.9rem;
            /* Ép kích thước chữ nhỏ lại cho đồng bộ */
        }

        .page-link i {
            font-size: 0.8rem;
            /* Cho icon nhỏ hơn chữ một chút sẽ đẹp hơn */
        }
    </style>
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
                <li><a href="CRUDdonhang.php" class="active"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>
                        Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <?php ($update_mode) ?>
                        <i class="fas fa-file-invoice me-2"></i> Chỉnh sửa đơn hàng <span
                            class="badge bg-light text-primary"></span>
                        <?php ?>

                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <input type="hidden" name="id" value="<?= $edit_order['id'] ?>">

                        <div class="col-md-2">
                            <label class="form-label fw-bold">ID Người dùng</label>
                            <input type="number" id="nguoi_dung_id" name="nguoi_dung_id" class="form-control"
                                value="<?= $edit_order['nguoi_dung_id'] ?>" required min="1">
                            <div class="invalid-feedback">ID người dùng phải là số dương!</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mã đơn hàng</label>
                            <input type="text" name="ma_don_hang" class="form-control"
                                value="<?= $edit_order['ma_don_hang'] ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tổng tiền</label>
                            <input type="number" id="tong_tien" name="tong_tien" class="form-control"
                                value="<?= $edit_order['tong_tien'] ?>" required min="1">
                            <div class="invalid-feedback">Tổng tiền phải lớn hơn 0!</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tiền giảm</label>
                            <input type="number" id="tien_giam" name="tien_giam" class="form-control"
                                value="<?= $edit_order['tien_giam'] ?>" min="0">
                            <div class="invalid-feedback">Tiền giảm không được lớn hơn tổng tiền!</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Thành tiền</label>
                            <input type="number" id="thanh_tien" name="thanh_tien" class="form-control"
                                value="<?= $edit_order['thanh_tien'] ?>" required readonly style="background-color: #e9ecef;">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="cho_xu_ly" <?= $edit_order['trang_thai'] == 'cho_xu_ly' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="dang_giao" <?= $edit_order['trang_thai'] == 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                                <option value="da_thanh_toan" <?= $edit_order['trang_thai'] == 'da_thanh_toan' ? 'selected' : '' ?>>Đã thanh toán</option>
                                <option value="da_giao" <?= $edit_order['trang_thai'] == 'da_giao' ? 'selected' : '' ?>>Đã
                                    giao</option>
                                <option value="hoan_thanh" <?= $edit_order['trang_thai'] == 'hoan_thanh' ? 'selected' : '' ?>>Đã hoàn thành</option>
                                <option value="da_huy" <?= $edit_order['trang_thai'] == 'da_huy' ? 'selected' : '' ?>>Đã
                                    hủy</option>

                            </select>
                        </div>

                        <div class="col-md-10 d-flex align-items-end justify-content-end">
                            <?php ($update_mode) ?>
                            <button name="save_order" class="btn btn-warning me-2">Cập nhật đơn hàng</button>
                            <a href="CRUDdonhang.php" class="btn btn-secondary">Hủy</a>
                            <?php ?>
                        </div>
                    </form>
                </div>
            </div>
            <div class="filter-group mb-3">
                <h4 class="filter-title"><i class="fas fa-search"></i> Tìm kiếm</h4>
                <form method="GET" action="CRUDdonhang.php" class="search-box d-flex gap-2">
                    <input type="text" name="search" id="searchInput" class="form-control"
                        placeholder="Nhập mã đơn hoặc ID khách..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Tìm</button>
                    <?php if (!empty($search)): ?>
                        <a href="CRUDdonhang.php" class="btn btn-outline-secondary">Xóa</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">

                <table class="table table-hover align-middle text-center">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th> ID người dùng</th>
                            <th>Mã đơn hàng</th>
                            <th>Tổng tiền </th>
                            <th>Tiền giảm</th>
                            <th>Thành tiền</th>
                            <th>Phương thức thanh toán</th>
                            <th>Trạng thái</th>
                            <th width="180">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listorders as $donhang) { ?>
                            <tr>
                                <td><a href="orders.php?id=<?= $donhang['id'] ?>" style="color: #0d6efd; text-decoration: underline;"><?= $donhang['id'] ?></a></td>
                                <td><?= $donhang['nguoi_dung_id'] ?></td>
                                <td><?= $donhang['ma_don_hang'] ?></td>
                                <td><?= $donhang['tong_tien'] ?></td>
                                <td><?= $donhang['tien_giam'] ?></td>
                                <td><?= $donhang['thanh_tien'] ?></td>
                                <td><?= $donhang['phuong_thuc_thanh_toan'] ?></td>
                                <td>
                                    <?php
                                    $status_label = '';
                                    $status_class = '';

                                    switch ($donhang['trang_thai']) {
                                        case 'cho_xu_ly':
                                            $status_label = 'Chờ xử lý';
                                            $status_class = 'bg-secondary';
                                            break;
                                        case 'dang_giao':
                                            $status_label = 'Đang giao';
                                            $status_class = 'bg-info';
                                            break;
                                        case 'da_thanh_toan':
                                            $status_label = 'Đã thanh toán';
                                            $status_class = 'bg-primary';
                                            break;
                                        case 'da_hoan_thanh':
                                            $status_label = 'Hoàn thành';
                                            $status_class = 'bg-success';
                                            break;
                                        case 'da_huy':
                                            $status_label = 'Đã hủy';
                                            $status_class = 'bg-danger';
                                            break;
                                        default:
                                            $status_label = $donhang['trang_thai'];
                                            $status_class = 'bg-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= $status_label ?></span>
                                </td>


                                <td>
                                    <a href="?edit=<?= $donhang['id'] ?>" class="btn btn-sm btn-outline-warning">Sửa</a>
                                    <a href="?delete=<?= $donhang['id'] ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Xóa đơn hàng này?')">Xóa</a>
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
                    Hiển thị trang <?= $page ?> / <?= $total_pages ?> (Tổng <?= $total_rows ?> đơn hàng)
                </div>
            </div>

        </div>


    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userIdInput = document.getElementById('nguoi_dung_id');
            const tongTienInput = document.getElementById('tong_tien');
            const tienGiamInput = document.getElementById('tien_giam');
            const thanhTienInput = document.getElementById('thanh_tien');
            const btnSave = document.querySelector('button[name="save_order"]');
            const form = document.querySelector('form');

            function calculateAndValidate() {
                // LẤY GIÁ TRỊ MỚI NHẤT TẠI ĐÂY (Phải nằm trong hàm)
                let userId = parseInt(userIdInput.value);
                let tongTien = parseFloat(tongTienInput.value) || 0;
                let tienGiam = parseFloat(tienGiamInput.value) || 0;

                // 1. Tự động tính Thành tiền
                let thanhTien = tongTien - tienGiam;
                thanhTienInput.value = thanhTien > 0 ? thanhTien : 0;

                // 2. Kiểm tra logic ràng buộc
                let isValid = true;

                // Ràng buộc ID Người dùng (Phải là số và > 0)
                if (isNaN(userId) || userId <= 0) {
                    userIdInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    userIdInput.classList.remove('is-invalid');
                }

                // Ràng buộc Tiền giảm
                if (tienGiam > tongTien) {
                    tienGiamInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    tienGiamInput.classList.remove('is-invalid');
                }

                // Ràng buộc Tổng tiền
                if (tongTien <= 0) {
                    tongTienInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    tongTienInput.classList.remove('is-invalid');
                }

                if (btnSave) btnSave.disabled = !isValid;

                return isValid;
            }
            tongTienInput.addEventListener('input', calculateAndValidate);
            tienGiamInput.addEventListener('input', calculateAndValidate);
            userIdInput.addEventListener('input', calculateAndValidate);

            form.addEventListener('submit', function(e) {
                if (!calculateAndValidate()) {
                    e.preventDefault();
                    alert('Vui lòng kiểm tra lại dữ liệu nhập vào!');
                }
            });
            calculateAndValidate();
        });
    </script>
</body>

</html>