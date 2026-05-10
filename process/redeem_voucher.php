<?php
if (!isset($voucher_user_id) || !is_numeric($voucher_user_id)) {
    throw new Exception("Voucher không hợp lệ");
}

$stmt = $conn->prepare("
    UPDATE voucher_thanh_vien
    SET trang_thai = 'da_dung',
        ngay_su_dung = NOW()
    WHERE id = :id
");

$stmt->execute([
    ':id' => $voucher_user_id
]);
?>