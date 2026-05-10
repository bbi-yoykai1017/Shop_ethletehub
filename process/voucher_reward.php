<?php
$voucher_id = 0;

// Xác định voucher theo hạng
switch ($newRank) {
    case 2:
        $voucher_id = 1; // bạc
        break;
    case 3:
        $voucher_id = 2; // vàng
        break;
    case 4:
        $voucher_id = 3; // kim cương
        break;
}

// Kiểm tra trước khi insert
if ($voucher_id > 0) {

    if (!isset($user_id)) {
        throw new Exception("Thiếu user_id");
    }

    // Insert voucher bằng PDO
    $stmt = $conn->prepare("
        INSERT INTO voucher_thanh_vien (nguoi_dung_id, ma_giam_gia_id, loai)
        VALUES (:user_id, :voucher_id, 'rank')
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':voucher_id' => $voucher_id
    ]);
}
?>