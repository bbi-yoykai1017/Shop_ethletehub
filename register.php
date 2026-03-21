<?php
session_start();
require_once "Database.php";
require_once "functions.php";

// Nếu đã đăng nhập rồi thì chuyển hướng về trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
 
$error = "";
$success = "";
$form_data = [
    'ten' => '',
    'email' => '',
];
 
// Xử lý form đăng ký
if (isset($_POST['register_btn'])) {
    $ten = isset($_POST['ten']) ? trim($_POST['ten']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mat_khau = isset($_POST['mat_khau']) ? trim($_POST['mat_khau']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Lưu lại form data
    $form_data['ten'] = htmlspecialchars($ten);
    $form_data['email'] = htmlspecialchars($email);
    
    // Validation client-side (nhưng cũng kiểm tra server)
    if (empty($ten) || empty($email) || empty($mat_khau) || empty($confirm_password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } elseif ($mat_khau !== $confirm_password) {
        $error = "Mật khẩu không trùng khớp!";
    } else {
        // Khởi tạo kết nối database
        $db = new Database();
        $conn = $db->connect();
        
        if (!$conn) {
            $error = "Lỗi kết nối cơ sở dữ liệu!";
        } else {
            // Gọi hàm registerUser từ functions.php (đã cải tiến)
            $result = registerUser($conn, $ten, $email, $mat_khau);
            
            if ($result['success']) {
                $success = $result['message'];
                // Clear form
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
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

    <!-- register container -->
    <div class="register-container">
        <div class="register-card">
            <!-- header -->
            <div class="register-header">
                <h1> Đăng ký</h1>
                <p>Tạo tài khoản AthleteHub của bạn</p>
            </div>
            <!-- error/success message -->
            <?php if (!empty($error_message)): ?>
                <div class="alter alter-danger" role="alter">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    <div style="margin-top: 10px;">
                        <a href="login.php" class="btn btn-sm btn-primary">Đăng nhập ngay</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- register form -->
            <form method="POST" action="register.php" id="registerForm">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="ten" class="form-label">
                        <i class="fas fa-user"></i> Họ và tên
                    </label>
                    <input type="text" class="form-control" id="ten" name="ten" placeholder="Nhập họ và tên của bạn"
                        required minlength="3" value="<?php echo $form_data['ten']; ?>">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn"
                        required value="<?php echo $form_data['email']; ?>">
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="mat_khau" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="mat_khau" name="mat_khau"
                            placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required minlength="6">
                        <i class="input-group-icon fa fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Xác nhận mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Nhập lại mật khẩu" required minlength="6">
                        <i class="input-group-icon fa fa-eye" id="toggleConfirmPassword"></i>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="terms-checkbox">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" name="agree_terms" required>
                        <span class="ms-2">Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách
                                bảo mật</a></span>
                    </label>
                </div>

                <!-- Register Button -->
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
     <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // hien thi mat mat_khau
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passInput = document.getElementById('mat_khau');
            const icon = this;

            if (passInput.type === 'password') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passInput.type === 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        // mat khau nhap lai
        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const passInput = document.getElementById('mat_khau');
            const icon = this;

            if (passInput.type === 'password') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passInput.type === 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>