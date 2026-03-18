<?php
session_start();

require_once 'Database.php'; 
require_once 'functions.php';

$error = "";

// BƯỚC 1: Khởi tạo kết nối CSDL
$db = new Database();
$conn = $db->connect(); // Đây là nơi biến $conn được tạo ra

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['mat_khau'];

    // BƯỚC 2: Truyền biến $conn đã khởi tạo vào hàm
    $result = loginUser($conn, $email, $password);

    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['ten'];
        $_SESSION['user_role'] = $result['user']['vai_tro'];

        header("Location: index.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>

    <title>login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-3 p-2 my-5 bg-dark text-white" style="text-align: center;">

        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link" style="color: white;">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color: white;">Đăng nhập</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color: aqua;">Đăng ký</a>
            </li>

        </ul>
    </div>
    <div class="container" style="display: flex; justify-content: center; padding-top: 50px;">
        <div id="loginbox" class="col-md-6 col-sm-8" style="border: 2px solid black ;">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title text-center mt-3">
                        <h3>LOGIN</h3>
                    </div>
                </div>

                <div style="padding: 30px" class="panel-body">
                    
                    <form method="post" class="form-horizontal">
                        <?php if ($error): ?>
                            
                            <div class="alert alert-danger text-center"><?= $error ?></div>
                        <?php endif; ?>

                        <div class="input-group" style="margin-bottom: 20px">
                            <span class="input-group-addon">Email</span>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="input-group" style="margin-bottom: 20px">
                            <span class="input-group-addon">Mật khẩu</span>
                            <input type="password" name="mat_khau" class="form-control" required>
                        </div>
                        <div class="margin-bottom-25">
                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                            <label for="remember"> Ghi nhớ đăng nhập</label>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" name="login_btn" class="btn btn-primary">Đăng nhập</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div class="container mt-3 p-3 my-5 bg-dark text-white" style="text-align: center;">
            <span>Lập trình backend wed 2</span>
        </div>
    </footer>

</body>

</html>