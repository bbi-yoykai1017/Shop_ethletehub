<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kiểm tra đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Kiểm tra quyền (Nếu đây là trang Admin)
// Lưu ý: Tên biến $_SESSION['user_role'] phải khớp với lúc bạn gán ở file login.php
if ($_SESSION['user_role'] !== 'admin') {
    echo "<script>
            alert('Bạn không có quyền truy cập vùng này!');
            window.location.href = 'index.php'; 
          </script>";
    exit();
}
?>