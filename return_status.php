<?php
session_start();
require_once "Database.php";

$db = new Database();
$conn = $db->connect();

$order_id = $_GET['order_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$yeu_cau = null;
if ($order_id) {
    $stmt = $conn->prepare("
        SELECT y.*, dh.ma_don_hang, dh.thanh_tien, dh.trang_thai as trang_thai_don
        FROM yeu_cau_tra_hang y
        JOIN don_hang dh ON y.don_hang_id = dh.id
        WHERE y.don_hang_id = ? AND y.nguoi_dung_id = ?
        ORDER BY y.ngay_tao DESC LIMIT 1
    ");
    $stmt->execute([$order_id, $user_id]);
    $yeu_cau = $stmt->fetch(PDO::FETCH_ASSOC);
}

$steps = [
    'cho_duyet'  => ['step' => 1, 'label' => 'Chờ duyệt',   'icon' => 'fa-clock',        'color' => '#ffc107'],
    'da_duyet'   => ['step' => 2, 'label' => 'Đã duyệt',    'icon' => 'fa-check-circle', 'color' => '#28a745'],
    'tu_choi'    => ['step' => 0, 'label' => 'Bị từ chối',  'icon' => 'fa-times-circle', 'color' => '#dc3545'],
    'hoan_thanh' => ['step' => 5, 'label' => 'Hoàn thành',  'icon' => 'fa-star',         'color' => '#f97316'],
];
$current = $steps[$yeu_cau['trang_thai'] ?? 'cho_duyet'] ?? $steps['cho_duyet'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Theo dõi yêu cầu trả hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .status-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        .timeline {
            position: relative;
            padding-left: 36px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 18px;
            /* = (20px dot width / 2) - (2px line width / 2) = tâm dot */
            top: 10px;
            bottom: 24px;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -27px;
            /* = -(padding-left) + (36 - 9) = căn tâm dot với đường */
            top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        .dot-done {
            background: #28a745;
        }

        .dot-active {
            background: #f97316;
        }

        .dot-pending {
            background: #dee2e6;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-dumbbell"></i> AthleteHub</a>
        </div>
    </nav>

    <div class="container py-5" style="max-width: 700px;">
        <?php if ($yeu_cau): ?>
            <div class="status-card mb-4 text-center">
                <i class="fas <?= $current['icon'] ?> fa-3x mb-3" style="color:<?= $current['color'] ?>"></i>
                <h4><?= $current['label'] ?></h4>
                <p class="text-muted">Đơn hàng #<?= $yeu_cau['ma_don_hang'] ?></p>
                <p class="text-muted small">Gửi lúc: <?= date('H:i d/m/Y', strtotime($yeu_cau['ngay_tao'])) ?></p>
                <?php if ($yeu_cau['ghi_chu_admin']): ?>
                    <div class="alert alert-info mt-3">
                        <strong>Phản hồi từ shop:</strong> <?= htmlspecialchars($yeu_cau['ghi_chu_admin']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="status-card">
                <h5 class="mb-4">Tiến trình xử lý</h5>
                <div class="timeline">
                    <?php
                    $timeline = [
                        ['label' => 'Gửi yêu cầu trả hàng', 'desc' => 'Yêu cầu đã được ghi nhận',  'step' => 0],
                        ['label' => 'Shop xem xét',          'desc' => 'Đang kiểm tra thông tin',    'step' => 1],
                        ['label' => 'Duyệt yêu cầu',         'desc' => 'Shop xác nhận trả hàng',     'step' => 2],
                        ['label' => 'Khách gửi hàng về',     'desc' => 'Gửi về địa chỉ shop',        'step' => 3],
                        ['label' => 'Hoàn tiền',             'desc' => 'Tiền hoàn trong 3-5 ngày',   'step' => 4],
                    ];
                    foreach ($timeline as $t):
                        $done = $current['step'] > $t['step'];
                        $active = $current['step'] == $t['step'];
                        $dotClass = $done ? 'dot-done' : ($active ? 'dot-active' : 'dot-pending');
                    ?>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $dotClass ?>">
                                <i class="fas <?= $done ? 'fa-check' : ($active ? 'fa-circle' : '') ?>"></i>
                            </div>
                            <div class="<?= $active ? 'fw-bold text-dark' : ($done ? 'text-success' : 'text-muted') ?>">
                                <?= $t['label'] ?>
                                <div class="small fw-normal"><?= $t['desc'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="orders.php?id=<?= $order_id ?>" class="btn btn-outline-primary px-4">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại đơn hàng
                </a>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Không tìm thấy yêu cầu trả hàng!</div>
        <?php endif; ?>
    </div>
</body>

</html>