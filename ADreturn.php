<?php
session_start();
require_once "Database.php";
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Xử lý duyệt/từ chối
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yc_id   = $_POST['yc_id'] ?? null;
    $action  = $_POST['action'] ?? '';
    $ghi_chu = $_POST['ghi_chu'] ?? '';

    if ($yc_id && in_array($action, ['da_duyet', 'tu_choi', 'hoan_thanh'])) {
        $stmt = $conn->prepare("UPDATE yeu_cau_tra_hang SET trang_thai = ?, ghi_chu_admin = ? WHERE id = ?");
        $stmt->execute([$action, $ghi_chu, $yc_id]);

        $stmt2 = $conn->prepare("SELECT don_hang_id FROM yeu_cau_tra_hang WHERE id = ?");
        $stmt2->execute([$yc_id]);
        $don_hang_id = $stmt2->fetchColumn();

        if ($action === 'da_duyet') {
            $conn->prepare("UPDATE don_hang SET trang_thai = 'cho_tra_hang' WHERE id = ?")->execute([$don_hang_id]);
        } elseif ($action === 'hoan_thanh') {
            $conn->prepare("UPDATE don_hang SET trang_thai = 'da_tra_hang' WHERE id = ?")->execute([$don_hang_id]);
        } elseif ($action === 'tu_choi') {
            $conn->prepare("UPDATE don_hang SET trang_thai = 'da_giao' WHERE id = ?")->execute([$don_hang_id]);
        }
        $success = "Cap nhat thanh cong!";
    }
}

$filter = $_GET['filter'] ?? 'cho_duyet';
$valid_filters = ['cho_duyet', 'da_duyet', 'tu_choi', 'hoan_thanh', 'tat_ca'];
if (!in_array($filter, $valid_filters)) $filter = 'cho_duyet';

$search = trim($_GET['search'] ?? '');
$limit  = 5;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$where_conditions = [];
if ($filter !== 'tat_ca') $where_conditions[] = "y.trang_thai = '$filter'";
if (!empty($search)) $where_conditions[] = "(dh.ma_don_hang LIKE '%$search%' OR nd.ten LIKE '%$search%' OR nd.email LIKE '%$search%')";
$where = count($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$total_rows = $conn->query("
    SELECT COUNT(*) FROM yeu_cau_tra_hang y
    JOIN don_hang dh ON y.don_hang_id = dh.id
    JOIN nguoi_dung nd ON y.nguoi_dung_id = nd.id
    $where
")->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$yeu_caus = $conn->query("
    SELECT y.*, dh.ma_don_hang, dh.thanh_tien, nd.ten as ten_khach, nd.email
    FROM yeu_cau_tra_hang y
    JOIN don_hang dh ON y.don_hang_id = dh.id
    JOIN nguoi_dung nd ON y.nguoi_dung_id = nd.id
    $where
    ORDER BY y.ngay_tao DESC
    LIMIT $limit OFFSET $offset
")->fetchAll(PDO::FETCH_ASSOC);

$stats = $conn->query("
    SELECT 
        SUM(trang_thai='cho_duyet') as cho_duyet,
        SUM(trang_thai='da_duyet')  as da_duyet,
        SUM(trang_thai='tu_choi')   as tu_choi,
        SUM(trang_thai='hoan_thanh') as hoan_thanh
    FROM yeu_cau_tra_hang
")->fetch(PDO::FETCH_ASSOC);

$ly_do_labels = [
    'san_pham_loi'     => 'San pham bi loi',
    'sai_hang'         => 'Giao sai hang',
    'khong_dung_mo_ta' => 'Khong dung mo ta',
    'thay_doi_y_dinh'  => 'Thay doi y dinh',
    'khac'             => 'Ly do khac',
];

$trang_thai_labels = [
    'cho_duyet'  => ['label' => 'Cho duyet',  'badge' => 'warning'],
    'da_duyet'   => ['label' => 'Da duyet',   'badge' => 'primary'],
    'tu_choi'    => ['label' => 'Tu choi',    'badge' => 'danger'],
    'hoan_thanh' => ['label' => 'Hoan thanh', 'badge' => 'success'],
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly tra hang - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/crud-modern.css">
    <link rel="stylesheet" href="css/admin-layout.css">
    <style>
        .return-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .return-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .return-card-body {
            padding: 20px;
        }

        .info-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            align-items: flex-start;
        }

        .info-row .label {
            color: #666;
            min-width: 130px;
            flex-shrink: 0;
        }

        .img-proof {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .img-proof:hover {
            transform: scale(1.05);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark shadow" style="background: var(--dark-gradient);">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold"><i class="fas fa-tachometer-alt me-2"></i> AthleteHub Admin</span>
            <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home me-1"></i> Trang chu</a>
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            <ul>
                <li><a href="CRUDproduct.php"><i class="fas fa-box me-2"></i> San pham</a></li>
                <li><a href="CRUDuser.php"><i class="fas fa-users me-2"></i> Khach hang</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Don hang</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Ma giam gia</a></li>
                <li><a href="CRUDnews.php"><i class="fas fa-newspaper me-2"></i> Tin tuc</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li><a href="ADreturn.php" class="active"><i class="fas fa-undo-alt me-2"></i> Tra Hang</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quan ly tra hang</span>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-undo-alt me-2 text-warning"></i>Quan ly yeu cau tra hang</h4>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- THONG KE -->
            <div class="row stats-row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stat-card stat-warning delay-1">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-value"><?= $stats['cho_duyet'] ?? 0 ?></div>
                        <div class="stat-label">Cho duyet</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-primary delay-2">
                        <div class="stat-icon"><i class="fas fa-check"></i></div>
                        <div class="stat-value"><?= $stats['da_duyet'] ?? 0 ?></div>
                        <div class="stat-label">Da duyet</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-danger delay-3">
                        <div class="stat-icon"><i class="fas fa-times"></i></div>
                        <div class="stat-value"><?= $stats['tu_choi'] ?? 0 ?></div>
                        <div class="stat-label">Tu choi</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-success delay-4">
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                        <div class="stat-value"><?= $stats['hoan_thanh'] ?? 0 ?></div>
                        <div class="stat-label">Hoan thanh</div>
                    </div>
                </div>
            </div>

            <!-- FILTER TABS -->
            <div class="filter-group mb-3">
                <?php
                $filters = [
                    'cho_duyet'  => ['label' => 'Cho duyet',  'class' => 'btn-warning'],
                    'da_duyet'   => ['label' => 'Da duyet',   'class' => 'btn-primary'],
                    'tu_choi'    => ['label' => 'Tu choi',    'class' => 'btn-danger'],
                    'hoan_thanh' => ['label' => 'Hoan thanh', 'class' => 'btn-success'],
                    'tat_ca'     => ['label' => 'Tat ca',     'class' => 'btn-secondary'],
                ];
                foreach ($filters as $key => $f): ?>
                    <a href="?filter=<?= $key ?>&search=<?= urlencode($search) ?>"
                        class="btn btn-sm me-2 mb-2 <?= $filter === $key ? $f['class'] : 'btn-outline-secondary' ?>"
                        style="border-radius:20px;">
                        <?= $f['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- TIM KIEM -->
            <div class="filter-group mb-4">
                <form method="GET" class="search-modern">
                    <input type="hidden" name="filter" value="<?= $filter ?>">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="form-control"
                        placeholder="Tim ma don, ten khach, email..."
                        value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="fas fa-search me-1"></i> Tim
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="?filter=<?= $filter ?>" class="btn btn-outline-gradient-primary">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- DANH SACH -->
            <?php if (empty($yeu_caus)): ?>
                <div class="return-card p-5 text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    <p>Khong co yeu cau nao.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($yeu_caus as $yc):
                $tt = $trang_thai_labels[$yc['trang_thai']] ?? ['label' => $yc['trang_thai'], 'badge' => 'secondary'];
            ?>
                <div class="return-card">
                    <div class="return-card-header">
                        <div>
                            <strong>#<?= htmlspecialchars($yc['ma_don_hang']) ?></strong>
                            <span class="text-muted ms-2 small"><?= date('H:i d/m/Y', strtotime($yc['ngay_tao'])) ?></span>
                        </div>
                        <span class="badge bg-<?= $tt['badge'] ?>"><?= $tt['label'] ?></span>
                    </div>
                    <div class="return-card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="info-row">
                                    <span class="label"><i class="fas fa-user me-1"></i>Khach hang:</span>
                                    <strong><?= htmlspecialchars($yc['ten_khach']) ?></strong>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fas fa-envelope me-1"></i>Email:</span>
                                    <?= htmlspecialchars($yc['email']) ?>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fas fa-tag me-1"></i>Ly do:</span>
                                    <span><?= $ly_do_labels[$yc['ly_do']] ?? $yc['ly_do'] ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="label"><i class="fas fa-money-bill me-1"></i>Gia tri don:</span>
                                    <span class="text-danger fw-bold"><?= number_format($yc['thanh_tien'], 0, ',', '.') ?>d</span>
                                </div>
                                <?php if ($yc['mo_ta']): ?>
                                    <div class="info-row">
                                        <span class="label"><i class="fas fa-comment me-1"></i>Mo ta:</span>
                                        <span class="text-muted"><?= htmlspecialchars($yc['mo_ta']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($yc['ghi_chu_admin']): ?>
                                    <div class="info-row">
                                        <span class="label"><i class="fas fa-reply me-1"></i>Ghi chu:</span>
                                        <span class="text-primary"><?= htmlspecialchars($yc['ghi_chu_admin']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($yc['hinh_anh']): ?>
                                <div class="col-md-2">
                                    <p class="text-muted small mb-1">Hinh minh chung:</p>
                                    <img src="public/returns/<?= htmlspecialchars($yc['hinh_anh']) ?>"
                                        class="img-proof"
                                        onclick="showImg(this.src)"
                                        onerror="this.style.display='none'">
                                </div>
                            <?php endif; ?>

                            <?php if ($yc['trang_thai'] === 'cho_duyet'): ?>
                                <div class="col-md-3">
                                    <form method="POST">
                                        <input type="hidden" name="yc_id" value="<?= $yc['id'] ?>">
                                        <textarea name="ghi_chu" class="form-control form-control-sm mb-2"
                                            placeholder="Ghi chu phan hoi..." rows="2"></textarea>
                                        <div class="action-buttons">
                                            <button name="action" value="da_duyet" class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Duyet
                                            </button>
                                            <button name="action" value="tu_choi" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tu choi yeu cau nay?')">
                                                <i class="fas fa-times me-1"></i>Tu choi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php elseif ($yc['trang_thai'] === 'da_duyet'): ?>
                                <div class="col-md-3">
                                    <form method="POST">
                                        <input type="hidden" name="yc_id" value="<?= $yc['id'] ?>">
                                        <textarea name="ghi_chu" class="form-control form-control-sm mb-2"
                                            placeholder="Ghi chu hoan tien..." rows="2"></textarea>
                                        <button name="action" value="hoan_thanh" class="btn btn-sm btn-primary w-100"
                                            onclick="return confirm('Xac nhan da nhan hang va hoan tien?')">
                                            <i class="fas fa-check-double me-1"></i>Hoan tat & Hoan tien
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- PHAN TRANG -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-4 text-center">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?filter=<?= $filter ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <small class="text-muted">Trang <?= $page ?> / <?= $total_pages ?> — Tong <?= $total_rows ?> yeu cau</small>
                </div>
            <?php endif; ?>

        </div><!-- end main-content -->
    </div><!-- end layout -->

    <footer class="text-white text-center py-3" style="background: var(--dark-gradient);">
        <small>AthleteHub Admin &copy; 2026</small>
    </footer>

    <!-- Modal xem anh -->
    <div class="modal fade" id="imgModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <img id="modalImg" src="" class="w-100 rounded">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImg(src) {
            document.getElementById('modalImg').src = src;
            new bootstrap.Modal(document.getElementById('imgModal')).show();
        }
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    </script>
</body>

</html>