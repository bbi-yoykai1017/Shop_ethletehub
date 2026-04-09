<?php
session_start();
require_once 'Database.php';
require_once 'model/news.php';

$db = new Database();
$conn = $db->connect();

$page = $_GET['page'] ?? 1;
$loai_tin = $_GET['loai_tin'] ?? null;

// Get news list
$news_list = getAllNews($conn, $page, 12, $loai_tin, 1);
$total = countNews($conn, $loai_tin, 1);
$total_pages = ceil($total / 12);
$newsCount = countNews($conn, null, 1);

// Validate page
if ($page > $total_pages && $total_pages > 0) {
    header("Location: news.php?page=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <style>
        .news-page-header {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .news-page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .news-filter {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .filter-btn {
            margin: 5px;
            border-radius: 20px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .news-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .news-card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            overflow: hidden;
            position: relative;
        }

        .news-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-card-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .news-card-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .news-card-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .news-card-excerpt {
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
            flex: 1;
        }

        .news-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .badge-san-pham-moi {
            background: #d4edda;
            color: #155724;
        }

        .badge-khuyen-mai {
            background: #fff3cd;
            color: #856404;
        }

        .badge-su-kien {
            background: #cfe2ff;
            color: #084298;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ddd;
        }

        @media (max-width: 768px) {
            .news-page-header h1 {
                font-size: 1.8rem;
            }

            .news-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container px-4">
            <div class="row">
                <div class="col-12">
                    <a class="navbar-brand" href="index.php">
                        <i class="fas fa-dumbbell"></i> AthleteHub
                    </a>
                </div>
                <div class="col-12">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                            <li class="nav-item"><a class="nav-link" href="products.php">Sản phẩm</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php#categories">Danh mục</a></li>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <li class="nav-item"><a class="nav-link" href="CRUDproduct.php">Quản trị</a></li>
                            <?php endif; ?>
                            <li class="nav-item"><a class="nav-link active" href="news.php">Tin tức</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php#about">Về chúng tôi</a></li>
                        </ul>
                        <div class="navbar-search-container">
                            <div class="navbar-search">
                                <input type="text" placeholder="Tìm sản phẩm..." class="search-input">
                                <button class="search-btn"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="navbar-right">
                            <div class="nav-notification-wrapper">
                                <div class="nav-notification" id="notificationBell">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge" id="notificationCount"><?= $newsCount ?></span>
                                </div>
                                <div class="notification-dropdown" id="notificationDropdown">
                                    <div class="notification-header">
                                        <h5><i class="fas fa-newspaper"></i> Tin tức mới</h5>
                                        <a href="news.php" class="notification-view-all">Xem tất cả</a>
                                    </div>
                                    <div class="notification-list" id="notificationList">
                                        <div class="notification-loading"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-icon" onclick="window.location.href='cart.php'">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count"></span>
                            </div>
                            <div class="user-account-wrapper d-flex align-items-center">
                                <div class="user-action-dropdown dropdown">
                                    <a href="#" class="user-icon-link me-2 text-decoration-none dropdown-toggle"
                                        id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                                        <i class="fas fa-user-circle fa-lg"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <li><h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6></li>
                                            <li><a class="dropdown-item" href="profile.php?id=<?= $_SESSION['user_id'] ?>"><i class="fas fa-user-edit me-2"></i> Hồ sơ của tôi</a></li>
                                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i> Đơn hàng</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                                        <?php else: ?>
                                            <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i> Đăng nhập</a></li>
                                            <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i> Đăng ký</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="news-page-header">
        <div class="container-custom">
            <h1>💬 Tin tức & Thông báo</h1>
            <p>Cập nhật những sản phẩm mới, khuyến mãi, và sự kiện hấp dẫn từ AthleteHub</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-custom" style="padding: 40px 0;">
        <!-- Filter -->
        <div class="news-filter">
            <strong class="d-block mb-3"><i class="fas fa-filter"></i> Lọc theo loại tin:</strong>
            <div class="d-flex flex-wrap gap-2">
                <a href="news.php" class="btn btn-sm <?= !$loai_tin ? 'btn-primary' : 'btn-outline-primary' ?> filter-btn">
                    <i class="fas fa-list"></i> Tất cả
                </a>
                <a href="news.php?loai_tin=san-pham-moi" class="btn btn-sm <?= $loai_tin === 'san-pham-moi' ? 'btn-success' : 'btn-outline-success' ?> filter-btn">
                    <i class="fas fa-star"></i> Sản phẩm mới
                </a>
                <a href="news.php?loai_tin=khuyen-mai" class="btn btn-sm <?= $loai_tin === 'khuyen-mai' ? 'btn-warning' : 'btn-outline-warning' ?> filter-btn">
                    <i class="fas fa-tag"></i> Khuyến mãi
                </a>
                <a href="news.php?loai_tin=su-kien" class="btn btn-sm <?= $loai_tin === 'su-kien' ? 'btn-info' : 'btn-outline-info' ?> filter-btn">
                    <i class="fas fa-calendar"></i> Sự kiện
                </a>
            </div>
        </div>

        <!-- News Grid -->
        <?php if (!empty($news_list)): ?>
            <div class="news-grid">
                <?php foreach ($news_list as $item): ?>
                    <div class="news-card" onclick="window.location.href='news-detail.php?id=<?= $item['id'] ?>'">
                        <div class="news-card-image">
                            <?php if ($item['hinh_anh']): ?>
                                <img src="<?= htmlspecialchars($item['hinh_anh']) ?>" alt="<?= htmlspecialchars($item['tieu_de']) ?>">
                            <?php endif; ?>
                            <span class="news-card-badge badge-<?= str_replace(' ', '-', $item['loai_tin']) ?>">
                                <?= getNewsTypeLabel($item['loai_tin']) ?>
                            </span>
                        </div>
                        <div class="news-card-body">
                            <h3 class="news-card-title"><?= htmlspecialchars($item['tieu_de']) ?></h3>
                            <p class="news-card-excerpt"><?= htmlspecialchars(truncateText($item['noi_dung'], 80)) ?></p>
                            <div class="news-card-footer">
                                <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($item['ngay_tao'])) ?></span>
                                <span><i class="fas fa-eye"></i> <?= $item['luot_xem'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="d-flex justify-content-center mb-5">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=1<?= $loai_tin ? '&loai_tin=' . $loai_tin : '' ?>">
                                    <i class="fas fa-chevron-left"></i> Đầu tiên
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?= $page - 1 ?><?= $loai_tin ? '&loai_tin=' . $loai_tin : '' ?>">Trước</a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i === (int)$page ? 'active' : '' ?>">
                                <a class="page-link" href="news.php?page=<?= $i ?><?= $loai_tin ? '&loai_tin=' . $loai_tin : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?= $page + 1 ?><?= $loai_tin ? '&loai_tin=' . $loai_tin : '' ?>">Sau</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="news.php?page=<?= $total_pages ?><?= $loai_tin ? '&loai_tin=' . $loai_tin : '' ?>">
                                    Cuối cùng <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Không có tin tức nào</h3>
                <p>Vui lòng kiểm tra lại sau hoặc chọn bộ lọc khác.</p>
            </div>
        <?php endif; ?>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    
    <!-- Notification Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        
        if (!notificationBell) return;
        
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            
            if (notificationDropdown.classList.contains('active')) {
                loadNotifications();
            }
        });
        
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.parentElement.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
        
        function loadNotifications() {
            fetch('api/news.php?action=get_latest_news&limit=5')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(news => {
                            const date = new Date(news.ngay_tao);
                            const formattedDate = date.toLocaleDateString('vi-VN');
                            const typeLabel = getNewsTypeLabel(news.loai_tin);
                            
                            html += `
                                <a href="news-detail.php?id=${news.id}" class="notification-item">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                                        <div style="flex: 1;">
                                            <div class="notification-item-title">${escapeHtml(news.tieu_de)}</div>
                                            <span class="notification-item-type">${typeLabel}</span>
                                        </div>
                                    </div>
                                    <div class="notification-item-date">
                                        <i class="fas fa-calendar"></i> ${formattedDate} 
                                        <i class="fas fa-eye"></i> ${news.luot_xem}
                                    </div>
                                </a>
                            `;
                        });
                        notificationList.innerHTML = html;
                    } else {
                        notificationList.innerHTML = '<div class="notification-loading">Chưa có tin tức nào</div>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi tải tin tức:', error);
                    notificationList.innerHTML = '<div class="notification-loading">Lỗi tải dữ liệu</div>';
                });
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        function getNewsTypeLabel(loai_tin) {
            const labels = {
                'san-pham-moi': '🎉 Sản phẩm mới',
                'khuyen-mai': '💰 Khuyến mãi',
                'su-kien': '🏃 Sự kiện',
                'other': '⭐ Khác'
            };
            return labels[loai_tin] || 'Tin tức';
        }
    });
    </script>
    <!-- FOOTER -->
    <footer class="footer">
        <div class="container-custom">
            <div class="footer-top">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">AthleteHub</h4>
                            <p style="color: #c0c0c0;">Chúng tôi cung cấp sản phẩm thể thao chất lượng cao.</p>
                            <div class="footer-socials">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên kết nhanh</h4>
                            <ul class="footer-links">
                                <li><a href="index.php"><i class="fas fa-angle-right"></i>Trang chủ</a></li>
                                <li><a href="products.php"><i class="fas fa-angle-right"></i>Sản phẩm</a></li>
                                <li><a href="#categories"><i class="fas fa-angle-right"></i>Danh mục</a></li>
                                <li><a href="#"><i class="fas fa-angle-right"></i>Về chúng tôi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Hỗ trợ</h4>
                            <ul class="footer-links">
                                <li><a href="#">Liên hệ</a></li>
                                <li><a href="#">FAQ</a></li>
                                <li><a href="#">Chính sách</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Liên lạc</h4>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <p>+84 (0) 123 456 789</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy;
                    <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
