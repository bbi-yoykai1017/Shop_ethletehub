<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/model/Mailer.php';

$mailer = new Mailer();

$order = [
    'ma_don_hang'           => 'ATH-TEST123',
    'ngay_dat'              => date('Y-m-d H:i:s'),
    'ten_nguoi_nhan'        => 'Nguyễn Test',
    'so_dien_thoai_nhan'    => '0901234567',
    'dia_chi_giao_hang'     => '123 Nguyễn Trãi, Q.1, TP.HCM',
    'phuong_thuc_thanh_toan'=> 'tien_mat',
    'tong_tien'             => 500000,
    'tien_giam'             => 50000,
    'thanh_tien'            => 450000,
];

$items = [
    [
        'ten'       => 'Áo thể thao Nike',
        'so_luong'  => 2,
        'gia'       => 200000,
        'kich_thuoc'=> 'L',
        'mau_sac'   => 'Đen',
    ],
    [
        'ten'       => 'Quần shorts Adidas',
        'so_luong'  => 1,
        'gia'       => 100000,
        'kich_thuoc'=> 'M',
        'mau_sac'   => 'Xanh',
    ],
];

// ⚠️ Đổi thành email thật của bạn để nhận test
$user = [
    'email' => 'td130999@ail.com',
    'ten'   => 'Nguyễn Test',
];

$result = $mailer->sendOrderConfirmation($order, $items, $user);

echo '<pre>';
echo $result['sent'] ? '✅ Gửi thành công!' : '❌ Lỗi: ' . $result['error'];
echo '</pre>';