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
$listproduct = getAllOrders($conn);
// ================= THÊM ĐƠN HÀNG =================
if (isset($_POST['add_order'])) {

    $sql = "INSERT INTO don_hang
            (nguoi_dung_id, ma_don_hang, tong_tien,
             tien_giam, thanh_tien, phuong_thuc_thanh_toan)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $_POST['nguoi_dung_id'],
        $_POST['ma_don_hang'],
        $_POST['tong_tien'],
        $_POST['tien_giam'],
        $_POST['thanh_tien'],
        $_POST['phuong_thuc_thanh_toan']
    ]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// ================= XÓA ĐƠN HÀNG =================
if (isset($_GET['delete'])) {

    $sql = "DELETE FROM don_hang WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// LẤY LẠI DANH SÁCH SAU CRUD
$listproduct = getAllOrders($conn);

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
            <h4 class="text-center">🏠ADMIN</h4>
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
                    <h4 class="mb-0">Quản lý đơn hàng</h4>


                </div>
                <h5 class="mb-3">➕ Thêm đơn hàng</h5>

                <form method="POST" class="row g-2 mb-4">

                    <div class="col-md-2">
                        <input type="number" name="nguoi_dung_id"
                            class="form-control"
                            placeholder="ID User" required>
                    </div>

                    <div class="col-md-2">
                        <input type="text" name="ma_don_hang"
                            class="form-control"
                            placeholder="Mã đơn" required>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="tong_tien"
                            class="form-control"
                            placeholder="Tổng tiền" required>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="tien_giam"
                            class="form-control"
                            placeholder="Tiền giảm" value="0">
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="thanh_tien"
                            class="form-control"
                            placeholder="Thành tiền" required>
                    </div>

                    <div class="col-md-2">
                        <input type="text" name="phuong_thuc_thanh_toan"
                            class="form-control"
                            placeholder="Thanh toán" required>
                    </div>

                    <div class="col-md-12">
                        <button name="add_order"
                            class="btn btn-success w-100">
                            Thêm đơn hàng
                        </button>
                    </div>

                </form>

                <div class="card-body">

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
                                    <th width="180">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($listproduct as $donhang) { ?>
                                    <tr>
                                        <td><?= $donhang['id'] ?></td>
                                        <td><?= $donhang['nguoi_dung_id'] ?></td>
                                        <td><?= $donhang['ma_don_hang'] ?></td>
                                        <td><?= $donhang['tong_tien'] ?></td>
                                        <td><?= $donhang['tien_giam'] ?></td>
                                        <td><?= $donhang['thanh_tien'] ?></td>
                                        <td><?= $donhang['phuong_thuc_thanh_toan'] ?></td>



                                        <td>
                                            <a href="Update.php?id=<?= $donhang['id'] ?>" class="btn btn-warning btn-sm">
                                                Sửa
                                            </a>

                                            <a onclick="return confirm('Xóa đơn hàng <?= $donhang['id'] ?> ?')"
                                                href="?delete=<?= $donhang['id'] ?>"
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