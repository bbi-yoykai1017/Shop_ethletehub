<?php
session_start();
require_once 'Database.php';
require_once 'model/functions.php';
require_once 'auth.php';

$db = new Database();
$conn = $db->connect();

$update_mode = false;
// Thêm đầy đủ các trường vào mảng rỗng để tránh lỗi "Undefined index"
$edit_product = [
    'id' => '',
    'ten' => '',
    'mo_ta' => '',
    'gia' => '',
    'gia_goc' => '',
    'trung_binh_sao' => 0,
    'so_luong_danh_gia' => 0,
    'hinh_anh_chinh' => ''
];

// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA (Sửa Link ở dưới thành ?edit=) =================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_product = $result;
        $update_mode = true;
    }
}

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_product'])) {
    $ten = $_POST['ten'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $gia_goc = $_POST['gia_goc'];
    $sao = $_POST['trung_binh_sao'];
    $danh_gia = $_POST['so_luong_danh_gia'];
    $hinh_anh = $_POST['hinh_anh_chinh'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE san_pham SET ten=?, mo_ta=?, gia=?, gia_goc=?, trung_binh_sao=?, so_luong_danh_gia=?, hinh_anh_chinh=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $mo_ta, $gia, $gia_goc, $sao, $danh_gia, $hinh_anh, $id]);
    } else {
        $sql = "INSERT INTO san_pham (ten, mo_ta, gia, gia_goc, trung_binh_sao, so_luong_danh_gia, hinh_anh_chinh) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $mo_ta, $gia, $gia_goc, $sao, $danh_gia, $hinh_anh]);
    }
    header("Location: CRUDproduct.php");
    exit;
}

// ================= 3. XỬ LÝ XÓA (Sửa Link ở dưới thành ?delete=) =================
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDproduct.php");
    exit;
}

$listproduct = getAllProducts($conn);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý sản phẩm - EthleteHub</title>



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

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= $update_mode ? "Chỉnh sửa sản phẩm" : "Thêm sản phẩm mới" ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" name="ten" class="form-control" value="<?= $edit_product['ten'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <input type="text" name="mo_ta" class="form-control" value="<?= $edit_product['mo_ta'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Giá bán</label>
                            <input type="number" name="gia" class="form-control" value="<?= $edit_product['gia'] ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Giá gốc</label>
                            <input type="number" name="gia_goc" class="form-control" value="<?= $edit_product['gia_goc'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Sao (1-5)</label>
                            <input type="number" step="0.1" name="trung_binh_sao" class="form-control" value="<?= $edit_product['trung_binh_sao'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Lượt đánh giá</label>
                            <input type="number" name="so_luong_danh_gia" class="form-control" value="<?= $edit_product['so_luong_danh_gia'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tên file ảnh</label>
                            <input type="text" name="hinh_anh_chinh" class="form-control" value="<?= $edit_product['hinh_anh_chinh'] ?>" placeholder="hinh1.jpg">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <?php if ($update_mode): ?>
                                <button name="save_product" class="btn btn-warning w-50 me-2">Cập nhật</button>
                                <a href="CRUDproduct.php" class="btn btn-secondary w-50">Hủy</a>
                            <?php else: ?>
                                <button name="save_product" class="btn btn-success w-100">Lưu sản phẩm</button>
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
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Giá bán</th>
                            <th>Giá gốc</th>
                            <th>Trung bình sao</th>
                            <th>Số lượng đánh giá</th>
                            <th>Hình ảnh</th>
                            <th width="180">Hành động</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($listproduct as $product) { ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['ten'] ?></td>
                                <td><?= $product['mo_ta'] ?></td>
                                <td><?= $product['gia'] ?></td>
                                <td><?= $product['gia_goc'] ?></td>
                                <td><?= $product['trung_binh_sao'] ?></td>
                                <td><?= $product['so_luong_danh_gia'] ?></td>
                                <td>
                                    <img src="./public/<?php echo htmlspecialchars($product['hinh_anh_chinh']); ?>"
                                        alt="<?= $product['ten'] ?>" width="80" height="80">
                                </td>



                                <td>
                                <a href="?edit=<?= $product['id'] ?>" class="btn btn-sm btn-outline-warning">Sửa</a>
                                <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
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