<?php
session_start();
require_once "Database.php";
require_once "functions.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";
$form_data = ['ten' => '', 'email' => ''];

if (isset($_POST['register_btn'])) {
    $ten = isset($_POST['ten']) ? trim($_POST['ten']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mat_khau = isset($_POST['mat_khau']) ? trim($_POST['mat_khau']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    $form_data['ten'] = htmlspecialchars($ten);
    $form_data['email'] = htmlspecialchars($email);

    if (empty($ten) || empty($email) || empty($mat_khau) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } elseif ($mat_khau !== $confirm_password) {
        $error = "Mật khẩu không trùng khớp!";
    } else {
        $db = new Database();
        $conn = $db->connect();

        if (!$conn) {
            $error = "Lỗi kết nối cơ sở dữ liệu!";
        } else {
            $result = registerUser($conn, $ten, $email, $mat_khau);

            if ($result['success']) {
                $success = $result['message'];
                $form_data['ten'] = '';
                $form_data['email'] = '';
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/register.css">
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

    <!-- Register Container -->
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <h1><i class="fas fa-user-plus"></i> Đăng Ký</h1>
                <p>Tạo tài khoản AthleteHub của bạn</p>
            </div>

            <!-- Error/Success Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    <div style="margin-top: 15px; text-align: center;">
                        <p>Chuyển hướng đến trang đăng nhập trong <span class="countdown" id="countdown">3</span> giây...</p>
                    </div>
                </div>

                <script>
                    let timeLeft = 3;
                    const countdownEl = document.getElementById('countdown');
                    
                    const timer = setInterval(() => {
                        timeLeft--;
                        if (countdownEl) {
                            countdownEl.textContent = timeLeft;
                        }
                        
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            window.location.href = 'login.php';
                        }
                    }, 1000);
                </script>
            <?php endif; ?>

            <!-- Register Form -->
            <form method="POST" action="register.php">
                <!-- Tên -->
                <div class="mb-3">
                    <label for="ten" class="form-label">
                        <i class="fas fa-user"></i> Họ và tên
                    </label>
                    <input type="text" class="form-control" id="ten" name="ten" 
                        placeholder="Nhập họ và tên" required minlength="3"
                        value="<?php echo $form_data['ten']; ?>">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                        placeholder="Nhập email" required
                        value="<?php echo $form_data['email']; ?>">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="mat_khau" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="mat_khau" name="mat_khau"
                            placeholder="Mật khẩu (tối thiểu 6 ký tự)" required minlength="6">
                        <i class="input-group-icon fas fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Xác nhận mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" 
                            name="confirm_password" placeholder="Nhập lại mật khẩu" required minlength="6">
                        <i class="input-group-icon fas fa-eye" id="toggleConfirmPassword"></i>
                    </div>
                </div>

                <!-- Checkbox -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTerms" 
                        name="agree_terms" required>
                    <label class="form-check-label" for="agreeTerms">
                        Tôi đồng ý với Điều khoản dịch vụ
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="register_btn" class="btn-register">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </form>

            <!-- Login Link -->
            <div class="register-footer">
                Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> <strong>AthleteHub</strong>. Bảo lưu mọi quyền.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== Toggle Password =====
        function setupToggle(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (!input || !icon) return;
            
            icon.addEventListener('click', function() {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        setupToggle('mat_khau', 'togglePassword');
        setupToggle('confirm_password', 'toggleConfirmPassword');

        // ===== Password Strength =====
        document.getElementById('mat_khau').addEventListener('input', function() {
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
    </script>
</body>
</html>