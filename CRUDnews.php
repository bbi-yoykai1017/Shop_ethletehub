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
                
                if (updateNews($conn, $id, $tieu_de, $noi_dung, $loai_tin, $hinh_anh, $trang_thai)) {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tin tức - AthleteHub Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-layout.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            background: #f5f5f5;
        }
        
        .admin-sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }
        
        .admin-sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: #e74c3c;
        }
        
        .news-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .news-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .news-table table {
            margin: 0;
        }
        
        .news-table th {
            background: #34495e;
            color: white;
            font-weight: 600;
        }
        
        .badge-publisher {
            background: #27ae60;
        }
        
        .badge-draft {
            background: #95a5a6;
        }
        
        .action-btn {
            margin: 0 3px;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .admin-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h5 class="mb-4">
                <i class="fas fa-crown"></i> Admin Panel
            </h5>
            <a href="CRUDproduct.php" class="<?= $action === 'list' && isset($_GET['page']) ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Quản lý Sản phẩm
            </a>
            <a href="CRUDuser.php">
                <i class="fas fa-users"></i> Quản lý Tài khoản
            </a>
            <a href="CRUDdonhang.php">
                <i class="fas fa-file-invoice"></i> Quản lý Đơn hàng
            </a>
            <a href="CRUDgiamgia.php">
                <i class="fas fa-percentage"></i> Quản lý Khuyến mãi
            </a>
            <a href="CRUDnews.php" class="active">
                <i class="fas fa-newspaper"></i> Quản lý Tin tức
            </a>
            <hr>
            <a href="index.php">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="logout.php" class="text-danger">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>
                        <i class="fas fa-newspaper"></i> Quản lý Tin tức
                    </h1>
                    <?php if ($action === 'list'): ?>
                        <a href="CRUDnews.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Tin tức mới
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Alert Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Thêm/Sửa Tin tức -->
                <?php if ($action === 'add' || $action === 'edit'): ?>
                    <div class="news-form">
                        <?php
                        $news = null;
                        if ($action === 'edit' && isset($_GET['id'])) {
                            $news = getNewsById($conn, $_GET['id']);
                            if (!$news) {
                                echo '<div class="alert alert-danger">Tin tức không tồn tại!</div>';
                                $action = 'list';
                            }
                        }

                        if ($action === 'add' || $action === 'edit'):
                        ?>
                            <h3 class="mb-4">
                                <?= $action === 'add' ? '➕ Thêm Tin tức mới' : '✏️ Sửa Tin tức' ?>
                            </h3>

                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?= $news['id'] ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="tieu_de" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tieu_de" name="tieu_de" 
                                        value="<?= $news['tieu_de'] ?? '' ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="noi_dung" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="noi_dung" name="noi_dung" rows="8" required><?= $news['noi_dung'] ?? '' ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="loai_tin" class="form-label">Loại tin tức <span class="text-danger">*</span></label>
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
                                            <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                            <select class="form-select" id="trang_thai" name="trang_thai" required>
                                                <option value="1" <?= ($news['trang_thai'] ?? 1) == 1 ? 'selected' : '' ?>>Công khai</option>
                                                <option value="0" <?= ($news['trang_thai'] ?? 1) == 0 ? 'selected' : '' ?>>Lưu nháp</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="hinh_anh" class="form-label">Hình ảnh <?= $action === 'edit' ? '(để trống nếu không thay đổi)' : '' ?></label>
                                    <input type="file" class="form-control" id="hinh_anh" name="hinh_anh" accept="image/*">
                                    <?php if ($action === 'edit' && $news['hinh_anh']): ?>
                                        <small class="text-muted">Hình ảnh hiện tại:</small><br>
                                        <img src="<?= htmlspecialchars($news['hinh_anh']) ?>" alt="News image" style="max-height: 150px; margin-top: 10px;">
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> <?= $action === 'add' ? 'Thêm' : 'Cập nhật' ?>
                                    </button>
                                    <a href="CRUDnews.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                <!-- Danh sách Tin tức -->
                <?php else: ?>
                    <div class="news-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Loại tin</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt xem</th>
                                    <th>Ngày đăng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $page = $_GET['page'] ?? 1;
                                $news_list = getAllNewsForAdmin($conn, $page, 10);
                                $total = countAllNews($conn);
                                $total_pages = ceil($total / 10);

                                if (empty($news_list)):
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Chưa có tin tức nào
                                        </td>
                                    </tr>
                                <?php else:
                                    foreach ($news_list as $item):
                                ?>
                                    <tr>
                                        <td>#<?= $item['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars(truncateText($item['tieu_de'], 50)) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= getNewsTypeLabel($item['loai_tin']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($item['trang_thai'] == 1): ?>
                                                <span class="badge badge-publisher">Công khai</span>
                                            <?php else: ?>
                                                <span class="badge badge-draft">Lưu nháp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><i class="fas fa-eye"></i> <?= $item['luot_xem'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($item['ngay_tao'])) ?></td>
                                        <td>
                                            <a href="CRUDnews.php?action=edit&id=<?= $item['id'] ?>" 
                                                class="btn btn-sm btn-warning action-btn" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="CRUDnews.php?action=delete&id=<?= $item['id'] ?>" 
                                                class="btn btn-sm btn-danger action-btn" 
                                                onclick="return confirm('Bạn chắc chắn muốn xóa?')" title="Xóa">
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

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=1">Đầu tiên</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $page - 1 ?>">Trước</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);

                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $page + 1 ?>">Sau</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="CRUDnews.php?page=<?= $total_pages ?>">Cuối cùng</a>
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
