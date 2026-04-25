<?php
session_start();
require_once 'Database.php';
require_once 'model/news.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$db = new Database();
$conn = $db->connect();
$admin_id = $_SESSION['user_id'];

$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $tieu_de = $_POST['tieu_de'] ?? '';
        $noi_dung = $_POST['noi_dung'] ?? '';
        $loai_tin = $_POST['loai_tin'] ?? 'san-pham-moi';
        $trang_thai = $_POST['trang_thai'] ?? 1;

        $hinh_anh = null;
        if (!empty($_FILES['hinh_anh']['name'])) {
            $file = $_FILES['hinh_anh'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = basename($file['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $file['size'] <= 5000000) {
                $new_filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = 'public/uploads/news/' . $new_filename;
                if (!is_dir('public/uploads/news')) mkdir('public/uploads/news', 0777, true);
                if (move_uploaded_file($file['tmp_name'], $upload_path)) $hinh_anh = 'public/uploads/news/' . $new_filename;
            } else {
                $error = 'Ảnh phải là JPG, PNG hoặc GIF và nhỏ hơn 5MB!';
            }
        }

        if (empty($error)) {
            if ($action === 'add') {
                if (addNews($conn, $tieu_de, $noi_dung, $loai_tin, $hinh_anh, $admin_id, $trang_thai)) {
                    $message = 'Thêm tin tức thành công!';
                    $action = 'list';
                } else { $error = 'Lỗi khi thêm tin tức!'; }
            } else {
                $id = $_POST['id'] ?? 0;
                if (empty($hinh_anh)) {
                    $old_news = getNewsById($conn, $id);
                    $hinh_anh = $old_news['hinh_anh'] ?? null;
                }
                if (updateNews($conn, $id, $tieu_de, $noi_dung, $loai_tin, $trang_thai, $hinh_anh)) {
                    $message = 'Cập nhật tin tức thành công!';
                    $action = 'list';
                } else { $error = 'Lỗi khi cập nhật tin tức!'; }
            }
        }
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (deleteNews($conn, $id)) { $message = 'Xóa tin tức thành công!'; $action = 'list'; }
    else { $error = 'Lỗi khi xóa tin tức!'; }
}

// Stats
$total_news = countAllNews($conn);
$public_count = $conn->query("SELECT COUNT(*) FROM tin_tuc WHERE trang_thai = 1")->fetchColumn();
$draft_count = $conn->query("SELECT COUNT(*) FROM tin_tuc WHERE trang_thai = 0")->fetchColumn();
$total_views = $conn->query("SELECT COALESCE(SUM(luot_xem),0) FROM tin_tuc")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Tin tức - AthleteHub Admin</title>

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
                <li><a href="CRUDnews.php" class="active"><i class="fas fa-newspaper me-2"></i> Tin tức</a></li>
                <li><a href="CRUDflashsale.php"><i class="fas fa-fire me-2"></i> Flash Sale</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <div class="main-content">
            <nav class="modern-breadcrumb animate-fade-in-up">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span class="current">Quản lý tin tức</span>
            </nav>

            <!-- Stats -->
            <div class="row stats-row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-primary delay-1">
                        <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                        <div class="stat-value"><?= number_format($total_news) ?></div>
                        <div class="stat-label">Tổng tin tức</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-success delay-2">
                        <div class="stat-icon"><i class="fas fa-globe"></i></div>
                        <div class="stat-value"><?= number_format($public_count) ?></div>
                        <div class="stat-label">Công khai</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-warning delay-3">
                        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="stat-value"><?= number_format($draft_count) ?></div>
                        <div class="stat-label">Lưu nháp</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-info delay-4">
                        <div class="stat-icon"><i class="fas fa-eye"></i></div>
                        <div class="stat-value"><?= number_format($total_views) ?></div>
                        <div class="stat-label">Tổng lượt xem</div>
                    </div>
                </div>
            </div>

            <!-- Toasts -->
            <?php if ($message): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-success" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="toast-content"><div class="toast-title">Thành công</div><div class="toast-message"><?= htmlspecialchars($message) ?></div></div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="toast-container-modern">
                    <div class="toast-modern toast-error" id="autoToast">
                        <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="toast-content"><div class="toast-title">Lỗi</div><div class="toast-message"><?= htmlspecialchars($error) ?></div></div>
                        <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Add/Edit -->
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="glass-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($action === 'add'): ?><i class="fas fa-plus-circle me-2"></i> Thêm tin tức mới
                            <?php else: ?><i class="fas fa-edit me-2"></i> Chỉnh sửa tin tức<?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $news = null;
                        if ($action === 'edit' && isset($_GET['id'])) {
                            $news = getNewsById($conn, $_GET['id']);
                            if (!$news) { echo '<div class="alert alert-danger">Tin tức không tồn tại!</div>'; $action = 'list'; }
                        }
                        ?>
                        <form method="POST" enctype="multipart/form-data" class="form-modern">
                            <?php if ($action === 'edit'): ?><input type="hidden" name="id" value="<?= $news['id'] ?>"><?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tieu_de" value="<?= $news['tieu_de'] ?? '' ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="noi_dung" rows="8" required><?= $news['noi_dung'] ?? '' ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Loại tin tức <span class="text-danger">*</span></label>
                                        <select class="form-select" name="loai_tin" required>
                                            <option value="san-pham-moi" <?= ($news['loai_tin'] ?? '') === 'san-pham-moi' ? 'selected' : '' ?>>Sản phẩm mới</option>
                                            <option value="khuyen-mai" <?= ($news['loai_tin'] ?? '') === 'khuyen-mai' ? 'selected' : '' ?>>Khuyến mãi</option>
                                            <option value="su-kien" <?= ($news['loai_tin'] ?? '') === 'su-kien' ? 'selected' : '' ?>>Sự kiện</option>
                                            <option value="other" <?= ($news['loai_tin'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <select class="form-select" name="trang_thai" required>
                                            <option value="1" <?= ($news['trang_thai'] ?? 1) == 1 ? 'selected' : '' ?>>Công khai</option>
                                            <option value="0" <?= ($news['trang_thai'] ?? 1) == 0 ? 'selected' : '' ?>>Lưu nháp</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hình ảnh <?= $action === 'edit' ? '(để trống nếu không thay đổi)' : '' ?></label>
                                <input type="file" class="form-control" name="hinh_anh" accept="image/*">
                                <?php if ($action === 'edit' && $news['hinh_anh']): ?>
                                    <small class="text-muted">Hình ảnh hiện tại:</small><br>
                                    <img src="<?= htmlspecialchars($news['hinh_anh']) ?>" alt="News image" style="max-height:150px;margin-top:10px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-gradient-success"><i class="fas fa-save me-2"></i><?= $action === 'add' ? 'Thêm tin tức' : 'Cập nhật tin tức' ?></button>
                                <a href="CRUDnews.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times me-2"></i> Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- List -->
                <div class="glass-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i> Danh sách tin tức</h5>
                        <a href="CRUDnews.php?action=add" class="btn btn-light btn-sm"><i class="fas fa-plus me-2"></i> Thêm mới</a>
                    </div>
                    <div class="card-body border-bottom">
                        <form method="GET" class="search-modern">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-gradient-primary"><i class="fas fa-search me-1"></i> Tìm</button>
                            <?php if (!empty($_GET['search'])): ?>
                                <a href="CRUDnews.php" class="btn btn-outline-gradient-primary"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="modern-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Loại tin</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt xem</th>
                                    <th>Ngày đăng</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $page = $_GET['page'] ?? 1;
                                $search = $_GET['search'] ?? '';
                                if (!empty($search)) {
                                    $news_list = searchNewsForAdmin($conn, $search, $page, 10);
                                    $total = countSearchNews($conn, $search);
                                } else {
                                    $news_list = getAllNewsForAdmin($conn, $page, 10);
                                    $total = countAllNews($conn);
                                }
                                $total_pages = ceil($total / 10);

                                if (empty($news_list)):
                                    ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="empty-icon"><i class="fas fa-newspaper"></i></div>
                                                <div class="empty-title">Chưa có tin tức nào</div>
                                                <div class="empty-text">Hãy thêm tin tức mới để bắt đầu</div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else:
                                    foreach ($news_list as $item):
                                        ?>
                                        <tr>
                                            <td class="ps-3"><strong>#<?= $item['id'] ?></strong></td>
                                            <td><?= htmlspecialchars(truncateText($item['tieu_de'], 40)) ?></td>
                                            <td><span class="badge bg-info text-white"><?= getNewsTypeLabel($item['loai_tin']) ?></span></td>
                                            <td>
                                                <?php if ($item['trang_thai'] == 1): ?>
                                                    <span class="badge-modern badge-status-public">Công khai</span>
                                                <?php else: ?>
                                                    <span class="badge-modern badge-status-draft">Lưu nháp</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><i class="fas fa-eye me-1 text-muted"></i><?= number_format($item['luot_xem']) ?></td>
                                            <td class="text-muted small"><?= date('d/m/Y', strtotime($item['ngay_tao'])) ?></td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="CRUDnews.php?action=edit&id=<?= $item['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-sm btn-gradient-warning" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                                    <a href="CRUDnews.php?action=delete&id=<?= $item['id'] ?>" class="btn btn-sm btn-gradient-danger" onclick="return confirm('Bạn chắc chắn muốn xóa tin tức này?')" title="Xóa"><i class="fas fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4 animate-fade-in delay-3">
                        <ul class="pagination pagination-modern justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="CRUDnews.php?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-double-left"></i></a></li>
                                <li class="page-item"><a class="page-link" href="CRUDnews.php?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Trước</a></li>
                            <?php endif; ?>
                            <?php $start = max(1, $page - 2); $end = min($total_pages, $page + 2); for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i === (int)$page ? 'active' : '' ?>"><a class="page-link" href="CRUDnews.php?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item"><a class="page-link" href="CRUDnews.php?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Sau</a></li>
                                <li class="page-item"><a class="page-link" href="CRUDnews.php?page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-angle-double-right"></i></a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
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

