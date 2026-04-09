<?php
session_start();
require_once 'Database.php';
require_once 'model/news.php';

$db = new Database();
$conn = $db->connect();

// Get news details
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: news.php');
    exit;
}

$news = getNewsById($conn, $id);
if (!$news || $news['trang_thai'] != 1) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['tieu_de']) ?> - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <style>
        .news-detail-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .news-hero {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .news-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-content {
            padding: 40px;
        }

        .news-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .news-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .news-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            color: var(--gray);
            font-size: 0.95rem;
        }

        .news-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .news-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 40px;
        }

        .news-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
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

        .news-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .news-card-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }

        .news-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-card-body {
            padding: 15px;
        }

        .news-card-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .news-hero {
                height: 250px;
            }

            .news-content {
                padding: 20px;
            }

            .news-title {
                font-size: 1.8rem;
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
                            <li class="nav-item"><a class="nav-link" href="news.php">Tin tức</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php#about">Về chúng tôi</a></li>
                        </ul>
                        <div class="navbar-search-container">
                            <div class="navbar-search">
                                <input type="text" placeholder="Tìm sản phẩm..." class="search-input">
                                <button class="search-btn"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="navbar-right">
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

    <!-- News Detail -->
    <div class="container-custom py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="news.php">Tin tức</a></li>
                <li class="breadcrumb-item active">Chi tiết bài viết</li>
            </ol>
        </nav>

        <div class="news-detail-container">
            <!-- Hero Image -->
            <?php if ($news['hinh_anh']): ?>
                <div class="news-hero">
                    <img src="<?= htmlspecialchars($news['hinh_anh']) ?>" alt="<?= htmlspecialchars($news['tieu_de']) ?>">
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="news-content">
                <div class="news-header">
                    <span class="news-badge badge-<?= str_replace(' ', '-', $news['loai_tin']) ?>">
                        <i class="fas fa-tag"></i> <?= getNewsTypeLabel($news['loai_tin']) ?>
                    </span>

                    <h1 class="news-title"><?= htmlspecialchars($news['tieu_de']) ?></h1>

                    <div class="news-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($news['ngay_tao'])) ?></span>
                        <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($news['ngay_tao'])) ?></span>
                        <span><i class="fas fa-eye"></i> <?= $news['luot_xem'] ?> lượt xem</span>
                    </div>
                </div>

                <div class="news-body">
                    <?= nl2br(htmlspecialchars($news['noi_dung'])) ?>
                </div>

                <div class="d-flex gap-2 border-top pt-4">
                    <a href="news.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Quay lại tin tức
                    </a>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
