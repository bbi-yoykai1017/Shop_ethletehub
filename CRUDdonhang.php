<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
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

    // 1. Kiểm tra xem đơn hàng này có chi tiết sản phẩm không
    $check_sql = "SELECT COUNT(*) FROM chi_tiet_don_hang WHERE don_hang_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$id]);
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
        // Nếu có dữ liệu liên quan, không cho xóa
        header("Location: CRUDdonhang.php?error=cannot_delete");
    } else {
        // Nếu trống trải, tiến hành xóa
        $sql = "DELETE FROM don_hang WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        header("Location: CRUDdonhang.php?success=deleted");
    }
    exit;
}
$listorders = getAllOrders($conn);
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
                        <?php if ($update_mode): ?>
                            <i class="fas fa-file-invoice me-2"></i> Chỉnh sửa đơn hàng <span
                                class="badge bg-light text-primary"></span>
                        <?php else: ?>
                            <i class="fas fa-cart-plus me-2"></i> Thêm đơn hàng mới
                        <?php endif; ?>
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
                            <label class="form-label fw-bold">Thanh toán</label>
                            <select name="phuong_thuc_thanh_toan" class="form-select">
                                <option value="tien_mat" <?= $edit_order['phuong_thuc_thanh_toan'] == 'tien_mat' ? 'selected' : '' ?>>COD (Tiền mặt)</option>
                                <option value="credit_card" <?= $edit_order['phuong_thuc_thanh_toan'] == 'credit_card' ? 'selected' : '' ?>>Thẻ tín dụng</option>
                                <option value="bank_transfer" <?= $edit_order['phuong_thuc_thanh_toan'] == 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                                <option value="e_wallet" <?= $edit_order['phuong_thuc_thanh_toan'] == 'e_wallet' ? 'selected' : '' ?>>Ví điện tử</option>

                            </select>
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
                            <?php if ($update_mode): ?>
                                <button name="save_order" class="btn btn-warning me-2">Cập nhật đơn hàng</button>
                                <a href="CRUDdonhang.php" class="btn btn-secondary">Hủy</a>
                            <?php else: ?>
                                <button name="save_order" class="btn btn-success">Thêm đơn hàng</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
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