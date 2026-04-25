<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$flash_sale_id = (int) $_GET['flash_sale_id'];

$sql = "SELECT * FROM flash_sale WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$flash_sale_id]);
$flashSale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flashSale) {
    header("Location: CRUDflashsale.php");
    exit;
}

$sql = "SELECT fsp.*, sp.ten, sp.gia FROM flash_sale_products fsp JOIN san_pham sp ON fsp.san_pham_id = sp.id WHERE fsp.flash_sale_id = ? ORDER BY fsp.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$flash_sale_id]);
$flashSaleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $san_pham_id = (int) $_POST['san_pham_id'];
    $gia_giam_gia = (float) $_POST['gia_giam_gia'];
    $so_luong_gioi_han = isset($_POST['so_luong_gioi_han']) && $_POST['so_luong_gioi_han'] ? (int) $_POST['so_luong_gioi_han'] : null;

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

if (isset($_GET['delete'])) {
    $product_id = (int) $_GET['delete'];
    $sql = "DELETE FROM flash_sale_products WHERE id = ? AND flash_sale_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$product_id, $flash_sale_id])) {
        header("Location: flash-sale-products-admin.php?flash_sale_id=$flash_sale_id&success=deleted");
        exit;
    }
}

$sql = "SELECT id, ten, gia FROM san_pham WHERE trang_thai = 1 ORDER BY ten ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$existingProductIds = array_column($flashSaleProducts, 'san_pham_id');
$availableProducts = array_filter($allProducts, function($p) use ($existingProductIds) {
    return !in_array($p['id'], $existingProductIds);
});

// Stats
$total_products = count($flashSaleProducts);
$total_sold = array_sum(array_column($flashSaleProducts, 'so_luong_da_ban'));
$total_limit = array_sum(array_column(array_filter($flashSaleProducts, fn($p)=>$p['so_luong_gioi_han']>0), 'so_luong_gioi_han'));

$toast = ''; $toast_msg = '';
if (isset($_GET['success'])) {
    $toast = 'success';
    $map = ['created'=>'Thêm sản phẩm thành công!','deleted'=>'Xóa sản phẩm thành công!'];
    $toast_msg = $map[$_GET['success']] ?? 'Thành công!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý sản phẩm Flash Sale - EthleteHub</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/crud-modern.css">
    <link rel="stylesheet" href="css/admin-layout.css">
</head>

<body style="background: linear-gradient(135deg, #f5f7fa 0%, #e4ecfb 100%); min-height: 100vh;">

    <nav class="navbar navbar-dark shadow" style="background: var(--dark-gradient);">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold"><i class="fas fa-tachometer-alt me-2"></i> AthleteHub Admin</span>
            <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home me-1"></i> Trang chủ</a>
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            <ul>
                <li><a href="CRUDproduct.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php" class="active"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <!-- Breadcrumb -->
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <a href="CRUDflashsale.php">Flash Sale</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current"><?= htmlspecialchars($flashSale['ten_chuong_trinh']) ?></span>
            </nav>

            <!-- Header Info -->
            <div class="glass-card mb-4 p-4 d-flex flex-wrap align-items-center justify-content-between gap-3 animate-fade-in-up delay-1">
                <div>
                    <h3 class="mb-1"><i class="fas fa-fire text-danger me-2"></i><?= htmlspecialchars($flashSale['ten_chuong_trinh']) ?></h3>
                    <p class="text-muted mb-0"><i class="fas fa-calendar me-2"></i><?= date('d/m/Y H:i', strtotime($flashSale['ngay_bat_dau'])) ?> — <?= date('d/m/Y H:i', strtotime($flashSale['ngay_ket_thuc'])) ?></p>
                </div>
                <a href="CRUDflashsale.php" class="btn btn-outline-gradient-primary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
            </div>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-box"></i></div>
                        <div class="stat-value"><?= number_format($total_products) ?></div>
                        <div class="stat-label">Sản phẩm trong FS</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-success delay-2">
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                        <div class="stat-value"><?= number_format($total_sold) ?></div>
                        <div class="stat-label">Đã bán</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card stat-info delay-3">
                        <div class="stat-icon"><i class="fas fa-warehouse"></i></div>
                        <div class="stat-value"><?= number_format($total_limit) ?></div>
                        <div class="stat-label">Tổng giới hạn</div>
                    </div>
                </div>
            </div>

            <!-- Toast / Error -->
            <?php if ($toast): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-<?= $toast ?>" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="toast-content"><div class="toast-title">Thành công</div><div class="toast-message"><?= htmlspecialchars($toast_msg) ?></div></div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-error" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="toast-content"><div class="toast-title">Lỗi</div><div class="toast-message"><?= htmlspecialchars($error) ?></div></div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add Product Form -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Thêm Sản Phẩm Flash Sale</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3 form-modern">
                        <div class="col-md-6">
                            <label class="form-label">Sản Phẩm</label>
                            <select class="form-select" name="san_pham_id" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($availableProducts as $product): ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['ten']) ?> (<?= number_format($product['gia'], 0, ',', '.') ?>đ)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($availableProducts)): ?>
                                <small class="text-danger">Tất cả sản phẩm đã được thêm vào flash sale này</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Giá Flash Sale</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="number" class="form-control" name="gia_giam_gia" step="1000" min="0" required placeholder="Giá mới">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Số Lượng Giới Hạn</label>
                            <input type="number" class="form-control" name="so_luong_gioi_han" min="0" placeholder="Không giới hạn">
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_product" class="btn btn-gradient-success" <?= empty($availableProducts) ? 'disabled' : '' ?>><i class="fas fa-plus me-1"></i> Thêm Sản Phẩm</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product List -->
            <div class="glass-card">
                <div class="card-header bg-dark">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i> Sản Phẩm Trong Flash Sale <span class="badge bg-light text-dark ms-2"><?= count($flashSaleProducts) ?></span></h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($flashSaleProducts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                            <div class="empty-title">Chưa có sản phẩm nào</div>
                            <div class="empty-text">Hãy thêm sản phẩm vào flash sale này</div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="modern-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:8%">ID</th>
                                        <th style="width:22%">Tên Sản Phẩm</th>
                                        <th style="width:12%">Giá Gốc</th>
                                        <th style="width:12%">Giá FS</th>
                                        <th style="width:10%">Giảm %</th>
                                        <th style="width:12%">Giới Hạn</th>
                                        <th style="width:14%">Đã Bán</th>
                                        <th style="width:10%">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($flashSaleProducts as $product): ?>
                                        <?php
                                            $discount = $product['gia'] > 0 ? round((1 - $product['gia_giam_gia'] / $product['gia']) * 100) : 0;
                                            $stock_percent = ($product['so_luong_gioi_han'] > 0) ? ($product['so_luong_da_ban'] / $product['so_luong_gioi_han'] * 100) : 0;
                                        ?>
                                        <tr>
                                            <td><strong>#<?= htmlspecialchars($product['id']) ?></strong></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($product['ten']) ?></td>
                                            <td class="text-muted"><s><?= number_format($product['gia'], 0, ',', '.') ?>đ</s></td>
                                            <td class="fw-bold text-danger"><?= number_format($product['gia_giam_gia'], 0, ',', '.') ?>đ</td>
                                            <td><span class="badge bg-danger">-<?= htmlspecialchars($discount) ?>%</span></td>
                                            <td><?= $product['so_luong_gioi_han'] ? htmlspecialchars($product['so_luong_gioi_han']) . ' cái' : '<span class="text-muted">Không giới hạn</span>' ?></td>
                                            <td>
                                                <?php if ($product['so_luong_gioi_han']): ?>
                                                    <small><?= htmlspecialchars($product['so_luong_da_ban']) ?>/<?= htmlspecialchars($product['so_luong_gioi_han']) ?></small>
                                                    <div class="progress-modern mt-1"><div class="progress-bar bg-danger" style="width:<?= $stock_percent ?>%"></div></div>
                                                <?php else: ?>
                                                    <small><?= htmlspecialchars($product['so_luong_da_ban']) ?> cái</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="?flash_sale_id=<?= htmlspecialchars($flash_sale_id) ?>&delete=<?= htmlspecialchars($product['id']) ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')" title="Xóa"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>EthleteHub Admin © 2026</small>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('autoToast');
            if (toast) {
                setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateX(100%)'; setTimeout(()=>toast.remove(),400); }, 4000);
            }
        });
    </script>
</body>
</html>

