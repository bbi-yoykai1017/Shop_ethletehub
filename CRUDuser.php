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
        :root {
            --sidebar-width: 240px;
        }

        .layout {
            display: flex;
            min-height: calc(100vh - 56px);
            transition: all 0.3s;
        }

        /* Sidebar mặc định trên Desktop */
        .sidebar {
            width: var(--sidebar-width);
            background: #111827;
            color: #fff;
            padding: 20px;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #374151;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            width: 100%;
            overflow-x: hidden;
        }

        /* RESPONSIVE CHO MOBILE & TABLET */
        @media (max-width: 991.98px) {
            .layout {
                flex-direction: column;
                /* Chuyển thành hàng dọc trên mobile */
            }

            .sidebar {
                width: 100%;
                padding: 10px 20px;
            }

            .sidebar ul {
                display: flex;
                overflow-x: auto;
                /* Cho phép vuốt ngang menu trên mobile */
                white-space: nowrap;
                padding-bottom: 10px;
            }

            .sidebar ul li {
                margin-right: 10px;
            }

            .sidebar h4 {
                display: none;
                /* Ẩn chữ ADMIN để tiết kiệm diện tích */
            }

            .main-content {
                padding: 15px;
            }

            /* Chỉnh lại form input trên mobile */
            .card-body form .col-md-3,
            .card-body form .col-md-2 {
                margin-bottom: 10px;
            }
        }

        /* Hiệu ứng cho Table trên màn hình nhỏ */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
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

        <aside class="sidebar">
            <h4 class="text-center mb-4 d-none d-lg-block">DASHBOARD</h4>
            <ul>
                <li><a href="CRUDproduct.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php" class="active"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="container-fluid p-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas <?= $update_mode ? 'fa-user-edit' : 'fa-user-plus' ?> me-2"></i>
                            <?= $update_mode ? "Cập nhật người dùng" : "Thêm người dùng mới" ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" class="row g-3 mb-4">
                            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label small fw-bold">Tên khách hàng</label>
                                <input type="text" name="ten" class="form-control" placeholder="Nhập tên..." value="<?= $edit_user['ten'] ?>" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= $edit_user['email'] ?>" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" class="form-control" placeholder="SĐT" value="<?= $edit_user['so_dien_thoai'] ?>" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label small fw-bold">Vai trò</label>
                                <select class="form-select" disabled>
                                    <option>Khách Hàng</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-2 d-flex align-items-end">
                                <?php if ($update_mode): ?>
                                    <div class="w-100">
                                        <button name="save_user" class="btn btn-warning w-100 mb-1">Cập nhật</button>
                                        <a href="CRUDuser.php" class="btn btn-light btn-sm w-100 border">Hủy</a>
                                    </div>
                                <?php else: ?>
                                    <button name="save_user" class="btn btn-success w-100"><i class="fas fa-plus me-1"></i> Thêm mới</button>
                                <?php endif; ?>
                            </div>
                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Thông tin khách hàng</th>
                                        <th class="text-center d-none d-md-table-cell">SĐT</th>
                                        <th class="text-center">Vai trò</th>
                                        <th class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listusers as $user): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $user['id'] ?></td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($user['ten']) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
                                            </td>
                                            <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($user['so_dien_thoai']) ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-light text-dark border"><?= $user['vai_tro'] ?></span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Xóa người dùng này?')" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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