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

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
        }

        .register-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-size: 26px;
            font-weight: 700;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .input-group {
            position: relative;
        }

        .input-group-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #667eea;
            font-size: 14px;
        }

        .input-group .form-control {
            padding-right: 45px;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }

        .password-strength-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 5px;
        }

        .password-strength-bar .bar {
            height: 100%;
            transition: all 0.3s;
        }

        .password-strength.weak .bar { width: 33%; background: #ff4757; }
        .password-strength.medium .bar { width: 66%; background: #ffa502; }
        .password-strength.strong .bar { width: 100%; background: #2ed573; }

        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #d63031;
            border-left: 4px solid #d63031;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #22863a;
            border-left: 4px solid #22863a;
        }

        .countdown {
            color: #667eea;
            font-weight: 600;
            font-size: 18px;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .footer {
            background: rgba(0, 0, 0, 0.3);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }
    </style>
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