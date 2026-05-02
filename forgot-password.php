<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'Database.php';
require_once 'model/functions.php';
require_once 'model/Mailer.php';

$error = "";
$success = "";

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Xử lý form quên mật khẩu
if (isset($_POST['forgot_password_btn'])) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        $error = "Vui lòng nhập email của bạn";
    } else {
        $db = new Database();
        $conn = $db->connect();

        if (!$conn) {
            $error = "Lỗi kết nối cơ sở dữ liệu!";
        } else {
            // Tạo token đặt lại mật khẩu
            $result = createPasswordResetToken($conn, $email);

            // Luôn hiển thị thông báo thành công để tránh leak thông tin user
            if ($result['success'] && isset($result['token']) && isset($result['user'])) {
                // Gửi email đặt lại mật khẩu
                try {
                    $mailer = new Mailer();
                    $mailResult = $mailer->sendPasswordResetEmail(
                        $result['email'],
                        $result['user']['ten'],
                        $result['token']
                    );

                    if ($mailResult['sent']) {
                        error_log("Password reset email sent to: " . $result['email']);
                    } else {
                        error_log("Failed to send reset email: " . ($mailResult['error'] ?? 'Unknown error'));
                        // Vẫn hiển thị thành công cho user, email sẽ được gửi lại sau
                    }
                } catch (Exception $e) {
                    error_log("Mailer error: " . $e->getMessage());
                }
            }

            $success = "Nếu email tồn tại trong hệ thống, chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn. Vui kiểm tra hộp thư (bao gồm cả thư rác)!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - AthleteHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dumbbell"></i> AthleteHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Sản phẩm</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- FORGOT PASSWORD CONTAINER -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1><i class="fas fa-key"></i> Quên Mật Khẩu</h1>
                <p>Nhập email của bạn để nhận link đặt lại mật khẩu</p>
            </div>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Forgot Password Form -->
            <form action="" method="post" id="forgotPasswordForm">
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                        placeholder="Nhập email của bạn" required 
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <!-- Submit Button -->
                <button type="submit" name="forgot_password_btn" class="btn-login">
                    <i class="fas fa-paper-plane"></i> Gửi link đặt lại mật khẩu
                </button>
            </form>

            <div class="divider">
                <span>hoặc</span>
            </div>

            <!-- Back to Login -->
            <div class="login-footer">
                Nhớ mật khẩu rồi? <a href="login.php">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container-custom">
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        &copy; <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form Validation
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();

            if (!email) {
                e.preventDefault();
                alert('Vui lòng nhập email!');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                alert('Email không hợp lệ!');
            }
        });
    </script>
</body>

</html>