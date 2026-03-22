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
$listusers = getAllUsers($conn);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý người dùng - EthleteHub</title>

    

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
    <div class="container py-5">

        <!-- CARD -->
        <div class="card shadow-lg border-0">

            <!-- CARD HEADER -->
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    <i class="bi bi-people-fill"></i> Quản lý người dùng
                </h4>

                <a href="frmthem.php" class="btn btn-light fw-semibold">
                    <i class="bi bi-plus-circle"></i> Thêm người dùng
                </a>

            </div>

            <!-- TABLE -->
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle text-center">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Vai trò</th>
                                <th width="180">Hành động</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($listusers as $user) { ?>

                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= $user['ten'] ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td><?= $user['so_dien_thoai'] ?></td>

                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= $user['vai_tro'] ?>
                                        </span>
                                    </td>

                                    <td>

                                        <a href="Update.php?id=<?= $user['id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square">Sửa</i>
                                        </a>

                                        <a onclick="return confirm('Xóa user <?= $user['id'] ?> ?')"
                                            href="Delete.php?id=<?= $user['id'] ?>"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash">Xóa</i>
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

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>

</body>

</html>
