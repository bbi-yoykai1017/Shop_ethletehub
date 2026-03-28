<?php
// Thay đổi mật khẩu bạn muốn tạo ở đây
$password_vi_du = 'admin12345'; 

// Tạo mã hash theo chuẩn BCrypt
$hashed_password = password_hash($password_vi_du, PASSWORD_DEFAULT);

echo "<h3>Mật khẩu gốc:</h3> " . $password_vi_du;
echo "<h3>Mã Hash để dán vào Database:</h3>";
echo "<code style='background: #eee; padding: 10px; display: block; word-break: break-all;'>" . $hashed_password . "</code>";

echo "<hr>";
echo "<strong>Lưu ý:</strong> Hãy copy toàn bộ chuỗi trên (bao gồm cả các ký tự $2y$...) và dán vào cột <b>mat_khau</b> trong database.";
?>