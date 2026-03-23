<?php
session_start();
require_once 'Database.php';
require_once 'model/functions.php';
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
        // CẬP NHẬT
        $id = $_POST['id'];
        $sql = "UPDATE ma_giam_gia SET ma_code=?, mo_ta=?, phan_tram_giam=?, so_tien_giam=?, don_hang_toi_thieu=?, giam_toi_da=?, gioi_han_su_dung=?, da_su_dung=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$code, $mota, $phan_tram, $so_tien, $toi_thieu, $toi_da, $gioi_han, $da_su_dung, $id]);
    } else {
        // THÊM MỚI (Mặc định da_su_dung = 0)
        $sql = "INSERT INTO ma_giam_gia (ma_code, mo_ta, phan_tram_giam, so_tien_giam, don_hang_toi_thieu, giam_toi_da, gioi_han_su_dung, da_su_dung) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
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

$list = getAllDiscounts($conn);
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
    <style>
        .layout {
            display: flex;
            min-height: calc(100vh - 56px);
        }

        .sidebar {
            width: 240px;
            background: #111827;
            color: #fff;
            padding: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            color: #d1d5db;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }
    </style>
</head>

<body style="background:#f4f6f9;">


    <!-- NAVBAR ADMIN -->

    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-speedometer2"></i> EthleteHub Admin
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
            <h4 class="text-center">ADMIN</h4>
            <ul>
                <li><a href="CRUDproduct.php">📋 Quản lý sản phẩm</a></li>
                <li><a href="CRUDuser.php">👤Quản lý khách hàng </a></li>
                <li><a href="CRUDdonhang.php">👤 Quản lý đơn hàng </a></li>
                <li><a href="CRUDgiamgia.php">👤 Quản lý mã giảm giá </a></li>
                <li><a href="#">⚙️ Cài đặt</a></li>
                <li><a href="logout.php">🚪 Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">

            <div class="card shadow-lg border-0">

                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?= $update_mode ? "⚡ Cập nhật mã: " . $edit_data['ma_code'] : "➕ Thêm mã giảm giá mới" ?></h5>
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
                            <td><?= $giamgia['ma_code'] ?></td>
                            <td><?= $giamgia['mo_ta'] ?></td>
                               <td><?= $giamgia['phan_tram_giam'] ?></td>
                            <td><?= $giamgia['so_tien_giam'] ?></td>
                         
                            <td><?= $giamgia['giam_toi_da'] ?></td>
                            <td><?= $giamgia['don_hang_toi_thieu'] ?></td>
                            <td><?= $giamgia['gioi_han_su_dung'] ?></td>
                            <td><?= $giamgia['da_su_dung'] ?></td>
                            <td>
                                <a href="Update.php?id=<?= $giamgia['id'] ?>" class="btn btn-warning btn-sm">
                                    Sửa
                                </a>

                                <a onclick="return confirm('Xóa sản phẩm <?= $giamgia['id'] ?> ?')"
                                    href="Delete.php?id=<?= $giamgia['id'] ?>"
                                    class="btn btn-danger btn-sm">
                                    Xóa
                                </a>
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

</body>

</html>