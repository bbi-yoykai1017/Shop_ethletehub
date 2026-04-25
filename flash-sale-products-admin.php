<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$flash_sale_id = (int) $_GET['flash_sale_id'];

// Lấy thông tin flash sale
$sql = "SELECT * FROM flash_sale WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$flash_sale_id]);
$flashSale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flashSale) {
    header("Location: CRUDflashsale.php");
    exit;
}

// Lấy sản phẩm trong flash sale này
$sql = "
    SELECT fsp.*, sp.ten, sp.gia 
    FROM flash_sale_products fsp
    JOIN san_pham sp ON fsp.san_pham_id = sp.id
    WHERE fsp.flash_sale_id = ?
    ORDER BY fsp.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute([$flash_sale_id]);
$flashSaleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm sản phẩm
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $san_pham_id = (int) $_POST['san_pham_id'];
    $gia_giam_gia = (float) $_POST['gia_giam_gia'];
    $so_luong_gioi_han = isset($_POST['so_luong_gioi_han']) && $_POST['so_luong_gioi_han'] ? (int) $_POST['so_luong_gioi_han'] : null;

    // Kiểm tra sản phẩm đã tồn tại
    $check_sql = "SELECT id FROM flash_sale_products WHERE flash_sale_id = ? AND san_pham_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$flash_sale_id, $san_pham_id]);
    
    if ($check_stmt->rowCount() > 0) {
        $error = "Sản phẩm này đã tồn tại trong flash sale";
    } else {
        $sql = "INSERT INTO flash_sale_products (flash_sale_id, san_pham_id, gia_giam_gia, so_luong_gioi_han, trang_thai) VALUES (?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$flash_sale_id, $san_pham_id, $gia_giam_gia, $so_luong_gioi_han])) {
            header("Location: flash-sale-products-admin.php?flash_sale_id=$flash_sale_id&success=created");
            exit;
        }
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $product_id = (int) $_GET['delete'];
    $sql = "DELETE FROM flash_sale_products WHERE id = ? AND flash_sale_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$product_id, $flash_sale_id])) {
        header("Location: flash-sale-products-admin.php?flash_sale_id=$flash_sale_id&success=deleted");
        exit;
    }
}

// Lấy danh sách sản phẩm
$sql = "SELECT id, ten, gia FROM san_pham WHERE trang_thai = 1 ORDER BY ten ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lọc sản phẩm chưa được thêm
$existingProductIds = array_column($flashSaleProducts, 'san_pham_id');
$availableProducts = array_filter($allProducts, function($p) use ($existingProductIds) {
    return !in_array($p['id'], $existingProductIds);
});
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý sản phẩm Flash Sale - EthleteHub</title>

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
                <i class="fas fa-fire"></i> AthleteHub Admin
            </span>
            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home"></i> Trang chủ
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
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">

            <!-- HEADER -->
            <div class="mb-4">
                <a href="CRUDflashsale.php" class="btn btn-secondary btn-sm mb-3">
                    <i class="fas fa-arrow-left me-2"></i> Quay lại
                </a>
                <h3 class="mb-2">
                    <i class="fas fa-fire me-2"></i><?= htmlspecialchars($flashSale['ten_chuong_trinh']) ?>
                </h3>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar me-2"></i>
                    <?= date('d/m/Y H:i', strtotime($flashSale['ngay_bat_dau'])) ?> - 
                    <?= date('d/m/Y H:i', strtotime($flashSale['ngay_ket_thuc'])) ?>
                </p>
            </div>

            <!-- SUCCESS/ERROR MESSAGES -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php 
                        $messages = [
                            'created' => 'Thêm sản phẩm thành công!',
                            'deleted' => 'Xóa sản phẩm thành công!'
                        ];
                        echo $messages[$_GET['success']] ?? 'Thành công!';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM THÊM SẢN PHẨM -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i> Thêm Sản Phẩm Flash Sale
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sản Phẩm</label>
                            <select class="form-control" name="san_pham_id" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($availableProducts as $product): ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>">
                                        <?= htmlspecialchars($product['ten']) ?> 
                                        <small>(<?= number_format($product['gia'], 0, ',', '.') ?>đ)</small>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($availableProducts)): ?>
                                <small class="text-danger">Tất cả sản phẩm đã được thêm vào flash sale này</small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Giá Flash Sale</label>
                            <input type="number" class="form-control" name="gia_giam_gia" step="1000" min="0" required>
                            <small class="text-muted">Giá mới cho flash sale</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Số Lượng Giới Hạn</label>
                            <input type="number" class="form-control" name="so_luong_gioi_han" min="0">
                            <small class="text-muted">Để trống = không giới hạn</small>
                        </div>

                        <div class="col-12">
                            <button type="submit" name="add_product" class="btn btn-success" <?= empty($availableProducts) ? 'disabled' : '' ?>>
                                <i class="fas fa-plus me-2"></i> Thêm Sản Phẩm
                            </button>
                        </div>
                    </form>
                </div>

            <!-- DANH SÁCH SẢN PHẨM -->
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i> Sản Phẩm Trong Flash Sale 
                        <span class="badge bg-light text-dark ms-2"><?= count($flashSaleProducts) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($flashSaleProducts)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-3">Chưa có sản phẩm nào trong flash sale này</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 10%">ID</th>
                                        <th style="width: 25%">Tên Sản Phẩm</th>
                                        <th style="width: 15%">Giá Gốc</th>
                                        <th style="width: 15%">Giá Flash Sale</th>
                                        <th style="width: 10%">Giảm %</th>
                                        <th style="width: 10%">Giới Hạn</th>
                                        <th style="width: 10%">Đã Bán</th>
                                        <th style="width: 10%">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($flashSaleProducts as $product): ?>
                                        <?php 
                                            $discount = round((1 - $product['gia_giam_gia'] / $product['gia']) * 100);
                                            $stock_percent = ($product['so_luong_gioi_han'] > 0) ? ($product['so_luong_da_ban'] / $product['so_luong_gioi_han'] * 100) : 0;
                                        ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($product['id']) ?></strong></td>
                                            <td><?= htmlspecialchars($product['ten']) ?></td>
                                            <td><?= number_format($product['gia'], 0, ',', '.') ?>đ</td>
                                            <td><strong class="text-danger"><?= number_format($product['gia_giam_gia'], 0, ',', '.') ?>đ</strong></td>
                                            <td>
                                                <span class="badge bg-danger">-<?= htmlspecialchars($discount) ?>%</span>
                                            </td>
                                            <td>
                                                <?php if ($product['so_luong_gioi_han']): ?>
                                                    <small><?= htmlspecialchars($product['so_luong_gioi_han']) ?> cái</small>
                                                <?php else: ?>
                                                    <small class="text-muted">Không giới hạn</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($product['so_luong_gioi_han']): ?>
                                                    <small><?= htmlspecialchars($product['so_luong_da_ban']) ?>/<?= htmlspecialchars($product['so_luong_gioi_han']) ?> cái</small>
                                                    <div class="progress" style="height: 6px; margin-top: 4px;">
                                                        <div class="progress-bar bg-danger" style="width: <?= $stock_percent ?>%"></div>
                                                <?php else: ?>
                                                    <small><?= htmlspecialchars($product['so_luong_da_ban']) ?> cái</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="?flash_sale_id=<?= htmlspecialchars($flash_sale_id) ?>&delete=<?= htmlspecialchars($product['id']) ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')" 
                                                   title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

        </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
