<?php
session_start();
require_once 'Database.php';
require_once 'model/functions.php';
require_once 'auth.php'; 

$db = new Database();
$conn = $db->connect();

$update_mode = false;
$edit_user = ['id' => '', 'ten' => '', 'email' => '', 'so_dien_thoai' => ''];

// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA =================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_user = $result;
        $update_mode = true; // Bật chế độ cập nhật
    }
}

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_user'])) {
    $ten = $_POST['ten'];
    $email = $_POST['email'];
    $sdt = $_POST['so_dien_thoai'];
    $vai_tro = 'khach_hang'; // Luôn cố định là user

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // CẬP NHẬT
        $id = $_POST['id'];
        $sql = "UPDATE nguoi_dung SET ten=?, email=?, so_dien_thoai=?, vai_tro=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $email, $sdt, $vai_tro, $id]);
    } else {
        // THÊM MỚI
        $sql = "INSERT INTO nguoi_dung (ten, email, so_dien_thoai, vai_tro) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $email, $sdt, $vai_tro]);
    }
    header("Location: CRUDuser.php");
    exit;
}

// ================= 3. XỬ LÝ XÓA =================
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDuser.php");
    exit;
}

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
        <main class="main-content">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= $update_mode ? "Cập nhật người dùng" : "Thêm người dùng mới" ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 mb-4">
                        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">

                        <div class="col-md-3">
                            <input type="text" name="ten" class="form-control" placeholder="Tên" value="<?= $edit_user['ten'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="<?= $edit_user['email'] ?>" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="so_dien_thoai" class="form-control" placeholder="SĐT" value="<?= $edit_user['so_dien_thoai'] ?>" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" disabled>
                                <option>Khách Hàng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <?php if ($update_mode): ?>
                                <button name="save_user" class="btn btn-warning w-100">Cập nhật</button>
                                <a href="CRUDuser.php" class="btn btn-secondary btn-sm d-block text-center mt-1">Hủy</a>
                            <?php else: ?>
                                <button name="save_user" class="btn btn-success w-100">Thêm mới</button>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Vai trò</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listusers as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= $user['ten'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td><?= $user['so_dien_thoai'] ?></td>
                                        <td><span class="badge bg-info text-dark"><?= $user['vai_tro'] ?></span></td>
                                        <td>
                                            <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning">Sửa</a>
                                            <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa người dùng này?')">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>

</body>

</html>