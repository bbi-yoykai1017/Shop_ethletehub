<?php
session_start();
require_once 'Database.php';
require_once 'model/functions.php';
require_once 'auth.php'; // File này sẽ kiểm tra xem người dùng đã đăng nhập chưa, nếu chưa sẽ chuyển hướng về login.php

/*
// e kiem tra phan quyen vai tro o day nha a
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: index.php");
    exit;
}*/
//code cua a cu viet binh thuong nha 
$db = new Database();
$conn = $db->connect();
$listproduct = getAllVariants($conn);
// ================= THÊM BIẾN THỂ =================
if (isset($_POST['add_variant'])) {

    $sql = "INSERT INTO bien_the_san_pham
            (san_pham_id, kich_thuoc_id, mau_sac_id, so_luong_ton)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $_POST['san_pham_id'],
        $_POST['kich_thuoc_id'],
        $_POST['mau_sac_id'],
        $_POST['so_luong_ton']
    ]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// ================= XÓA BIẾN THỂ =================
if (isset($_GET['delete'])) {

    $sql = "DELETE FROM bien_the_san_pham WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// ================= LẤY DANH SÁCH =================
$sql = "SELECT * FROM bien_the_san_pham";
$stmt = $conn->query($sql);

$listproduct = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý biến thể sản phẩm - EthleteHub</title>



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
                <li><a href="CRUDbienthesp.php">👤 Quản lý biến thể sản phẩm </a></li>
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

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Quản lý biến thể sản phẩm</h4>


                </div>
                <h5>➕ Thêm biến thể sản phẩm</h5>

                <form method="POST" class="row g-2 mb-4">

                    <div class="col-md-2">
                        <input type="number" name="san_pham_id"
                            class="form-control"
                            placeholder="ID SP" required>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="kich_thuoc_id"
                            class="form-control"
                            placeholder="ID Size" required>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="mau_sac_id"
                            class="form-control"
                            placeholder="ID Màu" required>
                    </div>

                    <div class="col-md-3">
                        <input type="number" name="so_luong_ton"
                            class="form-control"
                            placeholder="Số lượng tồn" required>
                    </div>

                    <div class="col-md-3">
                        <button name="add_variant"
                            class="btn btn-success w-100">
                            Thêm
                        </button>
                    </div>

                </form>
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle text-center">

                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>ID sản phẩm</th>
                                    <th>ID kích thước</th>
                                    <th>ID màu săc</th>
                                    <th>Số lượng tồn kho</th>

                                    <th width="180">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($listproduct as $bienthe) { ?>
                                    <tr>
                                        <td><?= $bienthe['id'] ?></td>
                                        <td><?= $bienthe['san_pham_id'] ?></td>
                                        <td><?= $bienthe['kich_thuoc_id'] ?></td>
                                        <td><?= $bienthe['mau_sac_id'] ?></td>
                                        <td><?= $bienthe['so_luong_ton'] ?></td>




                                        <td>
                                            <a href="Update.php?id=<?= $bienthe['id'] ?>" class="btn btn-warning btn-sm">
                                                Sửa
                                            </a>

                                            <a onclick="return confirm('Xóa biến thể sản phẩm <?= $bienthe['id'] ?> ?')"
                                                href="?delete=<?= $bienthe['id'] ?>"
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