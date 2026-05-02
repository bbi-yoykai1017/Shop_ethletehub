<?php
session_start();
require_once 'Database.php';
require_once 'model/functions.php';

$error = "";
$success = "";
$valid_token = false;
$user = null;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Kiểm tra token
if (empty($token)) {
    $error = "Link không hợp lệ";
} else {
    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        $error = "Lỗi kết nối cơ sở dữ liệu!";
    } else {
        // Xác minh token
        $result = verifyPasswordResetToken($conn, $token);
        
        if ($result['success'] && isset($result['user'])) {
            $valid_token = true;
            $user = $result['user'];
        } else {
            $error = $result['message'] ?? 'Token đã hết hạn hoặc không hợp lệ';
        }
    }
}

// Xử lý form đặt lại mật khẩu
if (isset($_POST['reset_password_btn']) && $valid_token && $user) {
    $mat_khau_moi = isset($_POST['mat_khau_moi']) ? trim($_POST['mat_khau_moi']) : '';
    $xac_nhan_mat_khau = isset($_POST['xac_nhan_mat_khau']) ? trim($_POST['xac_nhan_mat_khau']) : '';

    if (empty($mat_khau_moi) || empty($xac_nhan_mat_khau)) {
        $error = "Vui lòng nhập đầy đủ thông tin";
    } elseif (strlen($mat_khau_moi) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự";
    } elseif ($mat_khau_moi !== $xac_nhan_mat_khau) {
        $error = "Mật khẩu không trùng khớp";
    } else {
        // Cập nhật mật khẩu
        $db = new Database();
        $conn = $db->connect();
        
        $update_result = updatePassword($conn, $user['id'], $mat_khau_moi);
        
        if ($update_result['success']) {
            $success = "Đặt lại mật khẩu thành công! Chuyển hướng đến trang đăng nhập...";
            // Xóa token khỏi session để không submit lại
            $valid_token = false;
            
            // Chuyển hướng sau 2 giây
            header("Refresh: 2; URL=login.php");
        } else {
            $error = $update_result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - AthleteHub</title>

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

    <!-- RESET PASSWORD CONTAINER -->
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1><i class="fas fa-key"></i> Đặt Lại Mật Khẩu</h1>
                <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
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
            
            <!-- Show email info if token is valid -->
            <?php if ($valid_token && $user): ?>
                <div class="alert alert-info">
                    <i class="fas fa-user"></i> Đặt lại mật khẩu cho: <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                </div>
            <?php endif; ?>

            <!-- Reset Password Form -->
            <?php if ($valid_token && $user): ?>
            <form action="" method="post" id="resetPasswordForm">
                <!-- Token hidden -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <!-- New Password -->
                <div class="form-group">
                    <label for="mat_khau_moi" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu mới
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="mat_khau_moi" name="mat_khau_moi"
                            placeholder="Mật khẩu mới (tối thiểu 6 ký tự)" required minlength="6">
                        <button class="input-group-text" type="button" id="toggleNewPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <!-- Confirm New Password -->
                <div class="form-group">
                    <label for="xac_nhan_mat_khau" class="form-label">
                        <i class="fas fa-lock"></i> Xác nhận mật khẩu mới
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau"
                            placeholder="Nhập lại mật khẩu mới" required minlength="6">
                        <button class="input-group-text" type="button" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="reset_password_btn" class="btn-login">
                    <i class="fas fa-save"></i> Lưu mật khẩu mới
                </button>
            </form>
            <?php else: ?>
            <!-- Token invalid, show link to request new -->
            <div class="text-center mt-3">
                <a href="forgot-password.php" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Yêu cầu link mới
                </a>
            </div>
            <?php endif; ?>

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
        // Toggle Password Visibility
        function setupToggle(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            
            if (!input || !button) return;
            
            button.addEventListener('click', function() {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                const icon = button.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        setupToggle('mat_khau_moi', 'toggleNewPassword');
        setupToggle('xac_nhan_mat_khau', 'toggleConfirmPassword');

        // Password Strength
        document.getElementById('mat_khau_moi').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }

            let strength = 0;
            if (password.length >= 6) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            const map = {
                0: { text: '', class: '' },
                1: { text: 'Mật khẩu yếu', class: 'weak' },
                2: { text: 'Mật khẩu trung bình', class: 'medium' },
                3: { text: 'Mật khẩu mạnh', class: 'strong' },
                4: { text: 'Mật khẩu rất mạnh', class: 'strong' }
            };

            const data = map[strength];
            strengthDiv.className = `password-strength ${data.class}`;
            strengthDiv.innerHTML = data.text
                ? `<div>${data.text}</div><div class="password-strength-bar"><div class="bar" style="width:${strength * 25}%"></div></div>`
                : '';
        });

        // Form Validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('mat_khau_moi').value;
            const confirmPassword = document.getElementById('xac_nhan_mat_khau').value;

            if (!newPassword || !confirmPassword) {
                e.preventDefault();
                alert('Vui lòng nhập đầy đủ thông tin!');
            } else if (newPassword.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
            } else if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu không trùng khớp!');
            }
        });
    </script>
</body>

</html>
