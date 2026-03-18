<?php
session_start();
require_once 'Database.php';
require_once 'functions.php';
require_once 'auth.php'; // kiem tra da dang nhap hay chua, neu chua thi chuyen huoong ve trang login
// thong bao loi
$error = "";

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['mat_khau'];

    // kiem tra input
    if (empty($email) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin";
    } else {
        // khoi tao ket noi
        $db = new Database();
        $conn = $db->connect();

        // goi ham dang nhap tu fuction 
        $result = loginUser($conn, $email, $password);

        if ($result['success']) {
            // luu thong tin cua 
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_id'] = $result['user']['ten'];
            $_SESSION['user_id'] = $result['user']['email'];
            $_SESSION['user_id'] = $result['user']['vai_tro'];
            $_SESSION['user_id'] = $result['user']['so_dien_thoai'];
            $_SESSION['user_id'] = $result['user']['dia_chi'];
            $_SESSION['user_id'] = $result['user']['vai_tro'];
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - AthleteHub</title>

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
    <!-- LOGIN CONTAINER  -->
    <div class="login-container">
        <div class="login-card">
            <!--header-->
            <div class="login-header">
                <h1><i class="fas fa-sing-in-alt"></i>Đăng Nhập</h1>
                <p>Chào mừng bạn đã quay trở lại Shop-AthleteHub</p>
            </div>
            <!-- error/success message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- login form -->
             <form action="" method="post">
                <!-- email -->
                 <div class="form-group">
                    <label for="email" class="form-lable">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn"
                        required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                 </div>

                 <!-- password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Nhập mật khẩu của bạn" required>
                        <button class="input-group-text" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
             </form>
        </div>
    </div>
</body>

</html>