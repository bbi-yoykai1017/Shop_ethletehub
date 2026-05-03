<?php
session_start();
require_once "Database.php";
require_once "auth.php";

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? null;
$order = null;
$items = [];
$error = '';
$success = '';

// Lấy thông tin đơn hàng
if ($order_id) {
    $stmt = $conn->prepare("
        SELECT dh.*, nd.ten as ten_khach_hang 
        FROM don_hang dh 
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
        WHERE dh.id = ? AND dh.nguoi_dung_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $stmt2 = $conn->prepare("
            SELECT ct.*, sp.ten as ten_sp, sp.hinh_anh_chinh 
            FROM chi_tiet_don_hang ct 
            JOIN san_pham sp ON ct.san_pham_id = sp.id 
            WHERE ct.don_hang_id = ?
        ");
        $stmt2->execute([$order_id]);
        $items = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Kiểm tra đã có yêu cầu trả hàng chưa
        $stmt3 = $conn->prepare("SELECT id FROM yeu_cau_tra_hang WHERE don_hang_id = ?");
        $stmt3->execute([$order_id]);
        $da_co_yeu_cau = $stmt3->fetch();
    }
}

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ly_do    = $_POST['ly_do'] ?? '';
    $mo_ta    = $_POST['mo_ta'] ?? '';
    $hinh_anh = '';

    // Upload hình ảnh nếu có
    if (!empty($_FILES['hinh_anh']['name'])) {
        $upload_dir = 'public/returns/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext = pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION);
        $filename = 'return_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_dir . $filename)) {
            $hinh_anh = $filename;
        }
    }

    if (empty($ly_do)) {
        $error = 'Vui lòng chọn lý do trả hàng!';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO yeu_cau_tra_hang 
            (don_hang_id, nguoi_dung_id, ly_do, mo_ta, hinh_anh) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $user_id, $ly_do, $mo_ta, $hinh_anh]);

        // Cập nhật trạng thái đơn hàng
        $conn->prepare("UPDATE don_hang SET trang_thai = 'cho_tra_hang' WHERE id = ?")
             ->execute([$order_id]);

        $success = 'Yêu cầu trả hàng đã được gửi thành công! Chúng tôi sẽ liên hệ trong 24h.';
    }
}

function formatPrice($price) {
    return number_format($price ?? 0, 0, ',', '.') . '₫';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu trả hàng - AthleteHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        body { background: #f8f9fa; }
        .page-header {
            background: linear-gradient(135deg, #1e3a5f, #2d5a8e);
            color: white; padding: 40px 0;
        }
        .return-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 24px;
        }
        .step-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        .step-bar::before {
            content: '';
            position: absolute;
            top: 20px; left: 10%; right: 10%;
            height: 2px; background: #dee2e6;
        }
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .step-circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px;
            font-weight: bold; font-size: 14px;
            border: 2px solid #dee2e6;
            background: white;
            position: relative; z-index: 1;
        }
        .step.active .step-circle {
            background: #f97316; border-color: #f97316; color: white;
        }
        .step.done .step-circle {
            background: #28a745; border-color: #28a745; color: white;
        }
        .step-label { font-size: 12px; color: #666; }
        .step.active .step-label { color: #f97316; font-weight: 600; }

        .order-item-mini {
            display: flex; gap: 12px; align-items: center;
            padding: 12px; border: 1px solid #e5e7eb;
            border-radius: 10px; margin-bottom: 10px;
        }
        .order-item-mini img {
            width: 60px; height: 60px;
            object-fit: cover; border-radius: 8px;
        }
        .reason-option {
            border: 2px solid #e5e7eb;
            border-radius: 10px; padding: 14px 16px;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 8px;
        }
        .reason-option:hover { border-color: #f97316; background: #fff8f5; }
        .reason-option input[type=radio] { accent-color: #f97316; width: 18px; height: 18px; }
        .reason-option.selected { border-color: #f97316; background: #fff8f5; }

        .upload-zone {
            border: 2px dashed #dee2e6;
            border-radius: 12px; padding: 30px;
            text-align: center; cursor: pointer;
            transition: all 0.2s;
        }
        .upload-zone:hover { border-color: #f97316; background: #fff8f5; }
        .upload-zone i { font-size: 2rem; color: #aaa; margin-bottom: 10px; }

        .btn-return {
            background: #f97316; color: white; border: none;
            padding: 14px 30px; border-radius: 10px;
            font-size: 16px; font-weight: 600; width: 100%;
            transition: background 0.2s;
        }
        .btn-return:hover { background: #ea6c0a; color: white; }

        .policy-box {
            background: #f0f7ff;
            border-left: 4px solid #2d5a8e;
            border-radius: 8px; padding: 16px;
            margin-bottom: 20px;
        }
        .policy-box h6 { color: #1e3a5f; margin-bottom: 10px; }
        .policy-box ul { margin: 0; padding-left: 18px; font-size: 13px; color: #555; }

        .success-box {
            text-align: center; padding: 40px;
        }
        .success-box i { font-size: 4rem; color: #28a745; margin-bottom: 16px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-dumbbell"></i> AthleteHub</a>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <h2><i class="fas fa-undo-alt me-2"></i>Yêu cầu trả hàng / hoàn tiền</h2>
        <p class="mb-0 opacity-75">Đơn hàng #<?= htmlspecialchars($order['ma_don_hang'] ?? '') ?></p>
    </div>
</div>

<div class="container py-4">

<?php if ($success): ?>
    <!-- THÀNH CÔNG -->
    <div class="return-card success-box">
        <i class="fas fa-check-circle"></i>
        <h3 class="text-success mb-3">Gửi yêu cầu thành công!</h3>
        <p class="text-muted mb-4"><?= $success ?></p>
        <div class="row justify-content-center g-3">
            <div class="col-auto">
                <a href="orders.php?id=<?= $order_id ?>" class="btn btn-outline-primary px-4">
                    <i class="fas fa-eye me-2"></i>Xem đơn hàng
                </a>
            </div>
            <div class="col-auto">
                <a href="return_status.php?order_id=<?= $order_id ?>" class="btn btn-primary px-4">
                    <i class="fas fa-tasks me-2"></i>Theo dõi yêu cầu
                </a>
            </div>
        </div>
    </div>

<?php elseif (!$order): ?>
    <div class="alert alert-warning">Không tìm thấy đơn hàng!</div>

<?php elseif (isset($da_co_yeu_cau) && $da_co_yeu_cau): ?>
    <div class="return-card text-center py-5">
        <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
        <h4>Bạn đã gửi yêu cầu trả hàng cho đơn này</h4>
        <p class="text-muted">Chúng tôi đang xử lý. Vui lòng chờ phản hồi trong 24h.</p>
        <a href="return_status.php?order_id=<?= $order_id ?>" class="btn btn-primary mt-2">
            <i class="fas fa-tasks me-2"></i>Theo dõi trạng thái
        </a>
    </div>

<?php else: ?>

    <!-- THANH BƯỚC -->
    <div class="return-card">
        <div class="step-bar">
            <div class="step done">
                <div class="step-circle"><i class="fas fa-check"></i></div>
                <div class="step-label">Đặt hàng</div>
            </div>
            <div class="step active">
                <div class="step-circle">2</div>
                <div class="step-label">Gửi yêu cầu</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Chờ duyệt</div>
            </div>
            <div class="step">
                <div class="step-circle">4</div>
                <div class="step-label">Gửi hàng về</div>
            </div>
            <div class="step">
                <div class="step-circle">5</div>
                <div class="step-label">Hoàn tiền</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- SẢN PHẨM TRONG ĐƠN -->
                <div class="return-card">
                    <h5 class="mb-3"><i class="fas fa-box me-2 text-primary"></i>Sản phẩm trong đơn</h5>
                    <?php foreach ($items as $item): ?>
                    <div class="order-item-mini">
                        <img src="public/<?= htmlspecialchars($item['hinh_anh_chinh'] ?? 'placeholder.svg') ?>"
                             onerror="this.onerror=null; this.src='public/placeholder.svg'">
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($item['ten_sp']) ?></div>
                            <div class="text-muted small">
                                SL: <?= $item['so_luong'] ?> | 
                                Giá: <span class="text-danger fw-bold"><?= formatPrice($item['gia']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- LÝ DO TRẢ HÀNG -->
                <div class="return-card">
                    <h5 class="mb-3"><i class="fas fa-question-circle me-2 text-primary"></i>Lý do trả hàng <span class="text-danger">*</span></h5>
                    
                    <?php
                    $reasons = [
                        ['value' => 'san_pham_loi', 'icon' => 'fa-exclamation-triangle', 'label' => 'Sản phẩm bị lỗi / hư hỏng', 'color' => '#dc3545'],
                        ['value' => 'sai_hang', 'icon' => 'fa-times-circle', 'label' => 'Giao sai sản phẩm / màu sắc / size', 'color' => '#fd7e14'],
                        ['value' => 'khong_dung_mo_ta', 'icon' => 'fa-image', 'label' => 'Sản phẩm không đúng mô tả', 'color' => '#6f42c1'],
                        ['value' => 'thay_doi_y_dinh', 'icon' => 'fa-heart-broken', 'label' => 'Thay đổi ý định mua hàng', 'color' => '#6c757d'],
                        ['value' => 'khac', 'icon' => 'fa-ellipsis-h', 'label' => 'Lý do khác', 'color' => '#17a2b8'],
                    ];
                    foreach ($reasons as $r): ?>
                    <label class="reason-option" onclick="this.classList.toggle('selected')">
                        <input type="radio" name="ly_do" value="<?= $r['value'] ?>" required>
                        <i class="fas <?= $r['icon'] ?>" style="color:<?= $r['color'] ?>; width:20px;"></i>
                        <span><?= $r['label'] ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- MÔ TẢ THÊM -->
                <div class="return-card">
                    <h5 class="mb-3"><i class="fas fa-comment me-2 text-primary"></i>Mô tả chi tiết</h5>
                    <textarea name="mo_ta" class="form-control" rows="4"
                        placeholder="Mô tả chi tiết vấn đề bạn gặp phải để chúng tôi xử lý nhanh hơn..."></textarea>
                </div>

                <!-- UPLOAD HÌNH -->
                <div class="return-card">
                    <h5 class="mb-3"><i class="fas fa-camera me-2 text-primary"></i>Hình ảnh minh chứng</h5>
                    <div class="upload-zone" onclick="document.getElementById('hinh_anh').click()">
                        <i class="fas fa-cloud-upload-alt d-block"></i>
                        <p class="mb-0 text-muted">Nhấn để chọn ảnh (JPG, PNG — tối đa 5MB)</p>
                        <small class="text-muted">Hình ảnh giúp xử lý nhanh hơn</small>
                    </div>
                    <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*" class="d-none"
                           onchange="previewImg(this)">
                    <img id="img-preview" src="" class="mt-3 rounded d-none" style="max-width:100%; max-height:200px;">
                </div>

                <button type="submit" class="btn-return">
                    <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu trả hàng
                </button>
            </form>
        </div>

        <!-- SIDEBAR CHÍNH SÁCH -->
        <div class="col-lg-4">
            <div class="return-card">
                <h5 class="mb-3"><i class="fas fa-shield-alt me-2 text-primary"></i>Thông tin đơn hàng</h5>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">Mã đơn:</td><td><strong>#<?= $order['ma_don_hang'] ?></strong></td></tr>
                    <tr><td class="text-muted">Ngày đặt:</td><td><?= date('d/m/Y', strtotime($order['ngay_dat'])) ?></td></tr>
                    <tr><td class="text-muted">Tổng tiền:</td><td class="text-danger fw-bold"><?= formatPrice($order['thanh_tien']) ?></td></tr>
                    <tr><td class="text-muted">Trạng thái:</td><td><?= $order['trang_thai'] ?></td></tr>
                </table>
            </div>

            <div class="policy-box">
                <h6><i class="fas fa-info-circle me-2"></i>Chính sách trả hàng</h6>
                <ul>
                    <li>Trả hàng trong vòng <strong>7 ngày</strong> kể từ khi nhận</li>
                    <li>Sản phẩm còn nguyên tem, chưa qua sử dụng</li>
                    <li>Có hóa đơn / bằng chứng mua hàng</li>
                    <li>Hoàn tiền trong <strong>3-5 ngày làm việc</strong></li>
                    <li>Phí ship trả hàng do khách chịu (trừ lỗi shop)</li>
                </ul>
            </div>

            <div class="policy-box" style="background:#fff8f0; border-color:#f97316;">
                <h6 style="color:#f97316;"><i class="fas fa-headset me-2"></i>Cần hỗ trợ?</h6>
                <p class="mb-1 small">Hotline: <strong>0764567781</strong></p>
                <p class="mb-0 small">Email: <strong>ShopAthuelub</strong></p>
            </div>
        </div>
    </div>

<?php endif; ?>
</div>

<script>
function previewImg(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Highlight radio option khi chọn
document.querySelectorAll('.reason-option input[type=radio]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.reason-option').forEach(el => el.classList.remove('selected'));
        radio.closest('.reason-option').classList.add('selected');
    });
});
</script>
</body>
</html>