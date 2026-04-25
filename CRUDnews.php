<?php
session_start();
require_once 'Database.php';
require_once 'model/news.php';

// Kiểm tra quyền admin
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

// Xử lý thêm tin tức
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $tieu_de = $_POST['tieu_de'] ?? '';
        $noi_dung = $_POST['noi_dung'] ?? '';
        $loai_tin = $_POST['loai_tin'] ?? 'san-pham-moi';
        $trang_thai = $_POST['trang_thai'] ?? 1;

        // Xử lý upload ảnh
        $hinh_anh = null;
        if (!empty($_FILES['hinh_anh']['name'])) {
            $file = $_FILES['hinh_anh'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = basename($file['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $file['size'] <= 5000000) {
                $new_filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = 'public/uploads/news/' . $new_filename;

                if (!is_dir('public/uploads/news')) {
                    mkdir('public/uploads/news', 0777, true);
                }

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $hinh_anh = 'public/uploads/news/' . $new_filename;
                }
            } else {
                $error = 'Ảnh phải là JPG, PNG hoặc GIF và nhỏ hơn 5MB!';
            }
        }

        if (empty($error)) {
            if ($action === 'add') {
                if (addNews($conn, $tieu_de, $noi_dung, $loai_tin, $hinh_anh, $admin_id, $trang_thai)) {
                    $message = 'Thêm tin tức thành công!';
                    $action = 'list';
                } else {
                    $error = 'Lỗi khi thêm tin tức!';
                }
            } else {
                // Edit
                $id = $_POST['id'] ?? 0;
                // Nếu không upload ảnh mới, giữ ảnh cũ
                if (empty($hinh_anh)) {
                    $old_news = getNewsById($conn, $id);
                    $hinh_anh = $old_news['hinh_anh'] ?? null;
                }

                if (updateNews($conn, $id, $tieu_de, $noi_dung, $loai_tin, $trang_thai, $hinh_anh)) {
                    $message = 'Cập nhật tin tức thành công!';
                    $action = 'list';
                } else {
                    $error = 'Lỗi khi cập nhật tin tức!';
                }
            }
        }
    }
}

// Xử lý xóa tin tức
if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (deleteNews($conn, $id)) {
        $message = 'Xóa tin tức thành công!';
        $action = 'list';
    } else {
        $error = 'Lỗi khi xóa tin tức!';
    }
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý Tin tức - AthleteHub Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
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
                <i class="bi bi-speedometer2"></i> AthleteHub Admin
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
            <div class="container-fluid">
                <!-- Alert Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Thêm/Sửa Tin tức -->
                <?php if ($action === 'add' || $action === 'edit'): ?>
                    <div class="card shadow border-0 mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">
                                <?php if ($action === 'add'): ?>
                                    <i class="fas fa-plus-circle me-2"></i> Thêm tin tức mới
                                <?php else: ?>
                                    <i class="fas fa-edit me-2"></i> Chỉnh sửa tin tức
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $news = null;
                            if ($action === 'edit' && isset($_GET['id'])) {
                                $news = getNewsById($conn, $_GET['id']);
                                if (!$news) {
                                    echo '<div class="alert alert-danger">Tin tức không tồn tại!</div>';
                                    $action = 'list';
                                }
                            }
                            ?>

                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?= $news['id'] ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="tieu_de" class="form-label">Tiêu đề <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tieu_de" name="tieu_de"
                                        value="<?= $news['tieu_de'] ?? '' ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="noi_dung" class="form-label">Nội dung <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" id="noi_dung" name="noi_dung" rows="8"
                                        required><?= $news['noi_dung'] ?? '' ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="loai_tin" class="form-label">Loại tin tức <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="loai_tin" name="loai_tin" required>
                                                <option value="san-pham-moi" <?= ($news['loai_tin'] ?? '') === 'san-pham-moi' ? 'selected' : '' ?>>Sản phẩm mới</option>
                                                <option value="khuyen-mai" <?= ($news['loai_tin'] ?? '') === 'khuyen-mai' ? 'selected' : '' ?>>Khuyến mãi</option>
                                                <option value="su-kien" <?= ($news['loai_tin'] ?? '') === 'su-kien' ? 'selected' : '' ?>>Sự kiện</option>
                                                <option value="other" <?= ($news['loai_tin'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="trang_thai" class="form-label">Trạng thái <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="trang_thai" name="trang_thai" required>
                                                <option value="1" <?= ($news['trang_thai'] ?? 1) == 1 ? 'selected' : '' ?>>Công
                                                    khai</option>
                                                <option value="0" <?= ($news['trang_thai'] ?? 1) == 0 ? 'selected' : '' ?>>Lưu
                                                    nháp</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="hinh_anh" class="form-label">Hình ảnh
                                        <?= $action === 'edit' ? '(để trống nếu không thay đổi)' : '' ?></label>
                                    <input type="file" class="form-control" id="hinh_anh" name="hinh_anh" accept="image/*">
                                    <?php if ($action === 'edit' && $news['hinh_anh']): ?>
                                        <small class="text-muted">Hình ảnh hiện tại:</small><br>
                                        <img src="<?= htmlspecialchars($news['hinh_anh']) ?>" alt="News image"
                                            style="max-height: 150px; margin-top: 10px;">
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i
                                            class="fas fa-save me-2"></i><?= $action === 'add' ? 'Thêm tin tức' : 'Cập nhật tin tức' ?>
                                    </button>
                                    <a href="CRUDnews.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danh sách Tin tức -->
                <?php else: ?>
                    <div class="card shadow border-0 mb-4">
                        <div
                            class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i> Danh sách tin tức
                            </h5>
                            <a href="CRUDnews.php?action=add" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-2"></i> Thêm mới
                            </a>
                        </div>
                        <!-- Form tìm kiếm -->
                        <div class="card-body border-bottom">
                            <form method="GET" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tiêu đề hoặc nội dung..." 
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i> Tìm kiếm
                                </button>
                                <?php if (!empty($_GET['search'])): ?>
                                    <a href="CRUDnews.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i> Xóa bộ lọc
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
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
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox me-2"></i> Chưa có tin tức nào
                                            </td>
                                        </tr>
                                    <?php else:
                                        foreach ($news_list as $item):
                                            ?>
                                            <tr>
                                                <td class="ps-3"><strong>#<?= $item['id'] ?></strong></td>
                                                <td><?= htmlspecialchars(truncateText($item['tieu_de'], 40)) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= getNewsTypeLabel($item['loai_tin']) ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($item['trang_thai'] == 1): ?>
                                                        <span class="badge bg-success">Công khai</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Lưu nháp</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-eye me-1"></i><?= $item['luot_xem'] ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($item['ngay_tao'])) ?></td>
                                                <td class="text-center">
                                                    <a href="CRUDnews.php?action=edit&id=<?= $item['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                                                        class="btn btn-sm btn-warning me-1" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="CRUDnews.php?action=delete&id=<?= $item['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn chắc chắn muốn xóa tin tức này?')" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><i class="fas fa-chevron-left"></i> Đầu
                                            tiên</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Trước</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);

                                for ($i = $start; $i <= $end; $i++):
                                    ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Sau</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Cuối cùng <i
                                                class="fas fa-chevron-right"></i></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>