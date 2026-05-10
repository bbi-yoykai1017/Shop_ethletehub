<?php
// 1. Nhập mật khẩu bạn muốn chuyển đổi ở đây
$password = '123456'; 

// 2. Tạo chuỗi hash bảo mật (Bcrypt)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 3. Xuất ra màn hình để copy
echo "Mật khẩu gốc: <b>" . $password . "</b><br>";
echo "Chuỗi Hash để copy vào Database:<br>";
echo "<input type='text' value='" . $hashed_password . "' style='width:100%; padding:10px;' readonly onclick='this.select()'>";
echo "<br><br><small><i>Lưu ý: Mỗi lần F5 trang này, chuỗi hash sẽ khác nhau nhưng đều dùng được cho mật khẩu trên.</i></small>";
?>