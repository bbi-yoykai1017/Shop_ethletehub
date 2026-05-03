<?php
require_once 'database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$db = new Database();
$conn = $db->connect();
function generateMembershipCode($user_id) {
    return 'MTV' . str_pad((int)$user_id, 4, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5(uniqid((string)$user_id, true)), 0, 6));
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT ten 
    FROM nguoi_dung 
    WHERE id = :id
");

$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Lấy thông tin thành viên
$stmt = $conn->prepare("
    SELECT tv.*, htv.ten_hang
    FROM thanh_vien_nguoi_dung tv
    JOIN hang_thanh_vien htv ON tv.hang_id = htv.id
    WHERE tv.nguoi_dung_id = :user_id
");
$stmt->execute([':user_id' => $user_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$member) {
  // tạo mới membership
  $membershipCode = generateMembershipCode($user_id);
  $stmt = $conn->prepare("
        INSERT INTO thanh_vien_nguoi_dung (nguoi_dung_id, hang_id, ma_thanh_vien, tong_diem, tong_chi_tieu)
        VALUES (:user_id, 1, :code, 0, 0)
    ");
  $stmt->execute([':user_id' => $user_id, ':code' => $membershipCode]);

  // lấy lại
  $stmt = $conn->prepare("SELECT tv.*, htv.ten_hang FROM thanh_vien_nguoi_dung tv JOIN hang_thanh_vien htv ON tv.hang_id = htv.id WHERE tv.nguoi_dung_id = :user_id");
  $stmt->execute([':user_id' => $user_id]);
  $member = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif (empty($member['ma_thanh_vien'])) {
  $membershipCode = generateMembershipCode($user_id);
  $stmt = $conn->prepare("UPDATE thanh_vien_nguoi_dung SET ma_thanh_vien = :code WHERE nguoi_dung_id = :user_id");
  $stmt->execute([':code' => $membershipCode, ':user_id' => $user_id]);

  $stmt = $conn->prepare("SELECT tv.*, htv.ten_hang FROM thanh_vien_nguoi_dung tv JOIN hang_thanh_vien htv ON tv.hang_id = htv.id WHERE tv.nguoi_dung_id = :user_id");
  $stmt->execute([':user_id' => $user_id]);
  $member = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Tổng đơn hàng hoàn thành
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_orders
    FROM don_hang
    WHERE nguoi_dung_id = :user_id
      AND trang_thai IN ('da_giao', 'hoan_thanh')
");
$stmt->execute([':user_id' => $user_id]);
$orderStats = $stmt->fetch(PDO::FETCH_ASSOC);
$total_orders = (int) ($orderStats['total_orders'] ?? 0);

// Tổng chi tiêu đã được cập nhật bởi membership_process
$total_spent = (float) ($member['tong_chi_tieu'] ?? 0);

// Tiến trình hạng
$rankThresholds = [
    1 => 0,
    2 => 3000000,
    3 => 10000000,
    4 => 25000000,
];
$rankNames = [
    1 => 'Đồng',
    2 => 'Bạc',
    3 => 'Vàng',
    4 => 'Kim Cương',
];
$currentRank = max(1, min(4, (int) ($member['hang_id'] ?? 1)));
$nextRank = min(4, $currentRank + 1);
$currentThreshold = $rankThresholds[$currentRank];
$nextThreshold = $rankThresholds[$nextRank];
if ($currentRank === 4) {
    $progress = 100;
    $remainingAmount = 0;
    $progressLabel = 'Đã đạt hạng Kim Cương';
    $progressText = number_format($total_spent, 0, ',', '.') . ' đ';
} else {
    $progress = $currentThreshold === $nextThreshold ? 100 : min(100, max(0, ($total_spent - $currentThreshold) / ($nextThreshold - $currentThreshold) * 100));
    $remainingAmount = max(0, $nextThreshold - $total_spent);
    $progressLabel = 'Tiến trình lên ' . $rankNames[$nextRank];
    $progressText = number_format(min($total_spent, $nextThreshold), 0, ',', '.') . ' / ' . number_format($nextThreshold, 0, ',', '.') . ' đ';
}

// Tổng voucher
$stmt = $conn->prepare("
    SELECT COUNT(*) FROM voucher_thanh_vien 
    WHERE nguoi_dung_id = :user_id AND trang_thai = 'chua_dung'
");
$stmt->execute([':user_id' => $user_id]);
$total_vouchers = $stmt->fetchColumn();

// Lịch sử điểm
$stmt = $conn->prepare("
    SELECT * FROM lich_su_diem 
    WHERE nguoi_dung_id = :user_id
    ORDER BY id DESC LIMIT 5
");
$stmt->execute([':user_id' => $user_id]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thành Viên & Ưu Đãi - SportZone</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Be Vietnam Pro', sans-serif;
    }

    .glass {
      backdrop-filter: blur(10px);
    }
  </style>
</head>

<body class="bg-slate-100 text-slate-800">
  <div class="max-w-7xl mx-auto p-4 md:p-8">
    <!-- Header -->
    <header class="mb-8">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
        <div>
          <h1 class="text-3xl md:text-4xl font-extrabold">Thẻ Thành Viên & Ưu Đãi</h1>
          <p class="text-slate-500 mt-2">Tích điểm khi mua đồ thể thao, nâng hạng nhận voucher độc quyền và quà sinh nhật hàng năm</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl shadow-lg font-semibold"><a href="index.php"> Mua sắm ngay</a>

        </button>
      </div>
    </header>

    <!-- Main Grid -->
    <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
      <!-- Membership Card -->
      <div class="xl:col-span-2 bg-gradient-to-br from-cyan-500 to-blue-700 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden min-h-[360px]">
        <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-36 h-36 bg-white/10 rounded-full"></div>
        <div class="flex justify-between items-start">
          <div>
            <p class="uppercase tracking-[0.3em] text-sm font-semibold text-cyan-100">SportZone Club</p>
            <h2 class="text-4xl font-extrabold mt-3">
              <?= $member['ten_hang'] ?>
            </h2>
          </div>
          <div class="text-4xl">⭐</div>
        </div>

        <div class="mt-8 tracking-[0.25em] text-lg font-medium"><?= htmlspecialchars($member['ma_thanh_vien'] ?? 0) ?></div>
        <div class="mt-6">
          <p class="text-2xl font-bold"><?= htmlspecialchars($user['ten']) ?></p>
          <p class="text-cyan-100 mt-2">Thành viên từ 05/2024 • Hạn thẻ: 05/2027</p>
        </div>

        <div class="grid grid-cols-2 gap-8 mt-12">
          <div>
            <p class="text-cyan-100 text-sm uppercase">Điểm tích lũy</p>
            <p class="text-4xl font-extrabold mt-2">
              <?= number_format($member['tong_diem'] ?? 0) ?>
            </p>
          </div>
          <div class="text-right">
            <p class="text-cyan-100 text-sm uppercase">Tổng chi tiêu</p>
            <p class="text-4xl font-extrabold mt-2">
              <?= number_format($total_spent, 0, ',', '.') ?> đ
            </p>
          </div>
        </div>
      </div>

      <!-- Progress & Stats -->
      <div class="xl:col-span-3 bg-white rounded-3xl p-6 shadow-lg border border-slate-200">
        <h3 class="font-extrabold text-lg mb-6">🏅 Tiến Trình Thăng Hạng</h3>

        <div class="grid grid-cols-4 text-center mb-6">
          <div>
            <div class="text-2xl">🥉</div>
            <p class="font-semibold mt-2">Đồng</p>
            <p class="text-sm text-slate-400">0đ+</p>
          </div>
          <div>
            <div class="text-2xl">🥈</div>
            <p class="font-semibold mt-2">Bạc</p>
            <p class="text-sm text-slate-400">3M+</p>
          </div>
          <div>
            <div class="text-2xl">⭐</div>
            <p class="font-semibold mt-2 text-yellow-500">Vàng</p>
            <p class="text-sm text-slate-400">10M+</p>
          </div>
          <div>
            <div class="text-2xl">💎</div>
            <p class="font-semibold mt-2">Kim Cương</p>
            <p class="text-sm text-slate-400">25M+</p>
          </div>
        </div>

        <p class="text-slate-600 font-medium mb-2"><?= htmlspecialchars($progressLabel) ?></p>
        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
          <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-3 rounded-full" style="width: <?= round($progress, 2) ?>%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm">
          <span class="text-slate-500"><?php if ($currentRank === 4): ?>Đã đạt cấp cao nhất<?php else: ?>Còn thiếu <strong><?= number_format($remainingAmount, 0, ',', '.') ?> đ</strong><?php endif; ?></span>
          <span class="font-bold text-blue-600"><?= htmlspecialchars($progressText) ?></span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-8">
          <div class="border rounded-2xl p-5 bg-slate-50">
            <p class="text-slate-500">🛍️ Đơn hàng đã mua</p>
            <p class="text-3xl font-extrabold mt-2">
              <?= $total_orders ?> đơn
            </p>
          </div>
          <div class="border rounded-2xl p-5 bg-slate-50">
            <p class="text-slate-500">🎁 Voucher hiện có</p>
            <p class="text-3xl font-extrabold mt-2">
              <?= $total_vouchers ?> mã
            </p>
          </div>
          <div class="border rounded-2xl p-5 bg-slate-50">
            <p class="text-slate-500">🎂 Quà sinh nhật</p>
            <p class="text-lg font-bold mt-2">20% OFF hàng năm</p>
          </div>
          <div class="border rounded-2xl p-5 bg-slate-50">
            <p class="text-slate-500">💸 Tổng tiết kiệm</p>
            <p class="text-3xl font-extrabold mt-2">2.4M đ</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Bottom Section -->
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-8">
      <!-- Voucher -->
      <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-200">
        <h3 class="font-extrabold text-lg mb-5">🎟️ Voucher & Ưu Đãi Theo Hạng</h3>

        <div class="space-y-4">
          <div class="bg-green-50 border border-green-200 rounded-2xl p-5 flex justify-between items-center">
            <div>
              <span class="bg-green-600 text-white text-xs px-3 py-1 rounded-full font-bold">GOLD-SPORT20</span>
              <h4 class="font-bold text-lg mt-3">Giảm 20% giày chạy bộ & gym</h4>
              <p class="text-slate-500">Dành cho hạng Vàng • HSD: 31/12/2026</p>
            </div>
            <button class="border border-green-400 text-green-700 px-4 py-2 rounded-xl font-semibold">Áp dụng</button>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex justify-between items-center">
            <div>
              <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded-full font-bold">BIRTHDAY25</span>
              <h4 class="font-bold text-lg mt-3">Voucher sinh nhật 25% toàn bộ sản phẩm</h4>
              <p class="text-slate-500">Tự động tặng vào ngày sinh nhật mỗi năm</p>
            </div>
            <button class="border border-blue-400 text-blue-700 px-4 py-2 rounded-xl font-semibold">Nhận quà</button>
          </div>

          <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 flex justify-between items-center">
            <div>
              <span class="bg-purple-600 text-white text-xs px-3 py-1 rounded-full font-bold">VIP-FREESHIP</span>
              <h4 class="font-bold text-lg mt-3">Miễn phí giao hàng toàn quốc</h4>
              <p class="text-slate-500">Cho đơn từ 500K • Hạng Bạc trở lên</p>
            </div>
            <button class="border border-purple-400 text-purple-700 px-4 py-2 rounded-xl font-semibold">Sử dụng</button>
          </div>
        </div>
      </div>

      <!-- History -->
      <div class="bg-white rounded-3xl p-6 shadow-lg border border-slate-200">
        <h3 class="font-extrabold text-lg mb-5">📜 Lịch Sử Tích Điểm</h3>

        <div class="space-y-5">
          <div class="flex justify-between items-center border-b pb-4">
            <div class="flex gap-4 items-center">
              <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-xl">+</div>
              <div>
                <p class="font-bold">Mua giày bóng rổ</p>
                <p class="text-slate-500 text-sm">12/04/2026 • 3,200,000 đ</p>
              </div>
            </div>
            <span class="font-bold text-green-600">+320 điểm</span>
          </div>

          <div class="flex justify-between items-center border-b pb-4">
            <div class="flex gap-4 items-center">
              <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-xl">+</div>
              <div>
                <p class="font-bold">Áo tập gym premium</p>
                <p class="text-slate-500 text-sm">02/04/2026 • 1,500,000 đ</p>
              </div>
            </div>
            <span class="font-bold text-green-600">+150 điểm</span>
          </div>

          <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center">
              <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-xl">🎁</div>
              <div>
                <p class="font-bold">Đổi voucher sinh nhật</p>
                <p class="text-slate-500 text-sm">25/03/2026</p>
              </div>
            </div>
            <span class="font-bold text-red-500">-500 điểm</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>

</html>