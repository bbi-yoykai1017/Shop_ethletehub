<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user_id'];

$mode = isset($_GET['action']) && $_GET['action'] == 'edit' ? 'edit' : 'view';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_save'])) {
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);

    $stmt_old = $conn->prepare("SELECT anh_dai_dien FROM nguoi_dung WHERE id = ?");
    $stmt_old->execute([$user_id]);
    $old_data = $stmt_old->fetch();
    $ten_anh_db = $old_data['anh_dai_dien'];

    if (!empty($_FILES['anh_dai_dien']['name'])) {
        $target_dir = "uploads/avatars/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
        $file_name = "user_" . $user_id . "_" . time() . "." . $ext;
        if (move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $target_dir . $file_name)) {
            $ten_anh_db = $file_name;
        }
    }

    $sql_update = "UPDATE nguoi_dung SET ten = ?, so_dien_thoai = ?, dia_chi = ?, anh_dai_dien = ? WHERE id = ?";
    if ($conn->prepare($sql_update)->execute([$ten, $sdt, $dia_chi, $ten_anh_db, $user_id])) {
        $success = "Cập nhật thông tin thành công!";
        $mode = 'view';
    } else {
        $error = "Lỗi cập nhật dữ liệu.";
    }
}

$stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$has_avatar = !empty($user['anh_dai_dien']);
$avatar_path = $has_avatar ? 'uploads/avatars/' . $user['anh_dai_dien'] : '';

// Tạo chữ viết tắt từ tên
$name_parts = explode(' ', trim($user['ten']));
$initials = '';
if (count($name_parts) >= 2) {
    $initials = mb_substr($name_parts[0], 0, 1) . mb_substr(end($name_parts), 0, 1);
} else {
    $initials = mb_substr($user['ten'], 0, 2);
}
$initials = mb_strtoupper($initials);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ — <?= htmlspecialchars($user['ten']) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root{
            --bg0:#05060a;
            --bg1:#0b1022;
            --card: rgba(255,255,255,.07);
            --card2: rgba(255,255,255,.10);
            --stroke: rgba(255,255,255,.12);
            --stroke2: rgba(255,255,255,.18);
            --text:#eaf0ff;
            --muted: rgba(234,240,255,.68);
            --faint: rgba(234,240,255,.48);
            --neon:#7c3aed; /* tím */
            --neon2:#22d3ee; /* cyan */
            --green:#22c55e;
            --danger:#fb7185;
            --radius-card: 22px;
            --radius-field: 12px;
            --radius-btn: 14px;
            --shadow: 0 22px 70px rgba(0,0,0,.50);
        }

        body{
            min-height: 100vh;
            padding: 2.5rem 1rem 4rem;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            overflow-x: hidden;
            background:
                radial-gradient(1000px 600px at 10% 10%, rgba(124,58,237,.25), transparent 55%),
                radial-gradient(900px 500px at 90% 0%, rgba(34,211,238,.22), transparent 55%),
                linear-gradient(180deg, var(--bg0), var(--bg1));
        }

        /* subtle grid */
        body::before{
            content:"";
            position: fixed;
            inset:0;
            pointer-events:none;
            background:
                linear-gradient(to right, rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 70px 70px;
            opacity:.22;
            mask-image: radial-gradient(600px 320px at 50% 0%, black 45%, transparent 75%);
        }

        .wrap{
            max-width: 860px;
            margin: 0 auto;
            display:flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .breadcrumb{
            font-size: 12px;
            color: var(--muted);
            letter-spacing: .04em;
            padding: 0 6px;
        }
        .breadcrumb a{
            color: var(--muted);
            text-decoration: none;
            border-bottom: 1px dashed rgba(255,255,255,.22);
        }
        .breadcrumb a:hover{ color: var(--text); border-bottom-color: rgba(34,211,238,.7); }
        .breadcrumb span{ color: var(--text); }

        .card{
            background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.05));
            border-radius: var(--radius-card);
            border: 1px solid rgba(255,255,255,.14);
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .top-cover{
            position: relative;
            height: 130px;
            background:
                radial-gradient(900px 160px at 15% 20%, rgba(124,58,237,.55), transparent 60%),
                radial-gradient(800px 170px at 85% 0%, rgba(34,211,238,.45), transparent 60%),
                linear-gradient(135deg, rgba(124,58,237,.18), rgba(34,211,238,.12));
            border-bottom: 1px solid rgba(255,255,255,.10);
        }
        .top-cover::after{
            content:"";
            position:absolute;
            inset:0;
            background:
                linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
            transform: translateX(-60%) skewX(-18deg);
            animation: shine 3.8s ease-in-out infinite;
            opacity:.55;
        }
        @keyframes shine{
            0%, 55% { transform: translateX(-80%) skewX(-18deg); opacity:0; }
            70% { opacity:.55; }
            100% { transform: translateX(130%) skewX(-18deg); opacity:0; }
        }

        .identity{
            padding: 0 2rem 1.25rem;
            margin-top: -48px;
            display:flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .identity-left{ display:flex; align-items:center; gap: 1.25rem; min-width: 0; }

        .avatar-wrap{ position: relative; flex-shrink:0; }
        .avatar{
            width: 92px;
            height: 92px;
            border-radius: 22px;
            background:
                radial-gradient(65px 65px at 30% 25%, rgba(34,211,238,.8), transparent 55%),
                radial-gradient(75px 75px at 80% 10%, rgba(124,58,237,.9), transparent 58%),
                rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.18);
            box-shadow: 0 18px 40px rgba(124,58,237,.22);
            display:flex;
            align-items:center;
            justify-content:center;
            font-family: 'Instrument Serif', serif;
            font-size: 28px;
            color: rgba(255,255,255,.92);
            overflow: hidden;
        }
        .avatar img{ width:100%; height:100%; object-fit: cover; display:block; }

        .avatar-edit-btn{
            position: absolute;
            right: -8px;
            bottom: -8px;
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: rgba(10,12,20,.85);
            border: 1px solid rgba(255,255,255,.18);
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
            box-shadow: 0 14px 26px rgba(0,0,0,.35);
        }
        .avatar-edit-btn:hover{
            transform: translateY(-2px);
            background: rgba(255,255,255,.08);
            box-shadow: 0 18px 34px rgba(0,0,0,.45);
        }

        .id-text{ min-width:0; }
        .id-text h1{
            font-family: 'Instrument Serif', serif;
            font-size: 26px;
            font-weight: 400;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .id-text .email{
            font-size: 13px;
            color: var(--muted);
            margin-top: 6px;
            display:flex;
            align-items:center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .editing-tag{
            font-size: 12px;
            color: rgba(34,197,94,.95);
            border: 1px solid rgba(34,197,94,.35);
            background: rgba(34,197,94,.12);
            padding: 4px 10px;
            border-radius: 999px;
        }

        .role-pill{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.92);
            font-size: 12px;
            letter-spacing: .02em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .role-dot{
            width: 9px; height: 9px; border-radius: 50%;
            background: linear-gradient(180deg, var(--neon2), var(--neon));
            box-shadow: 0 0 0 4px rgba(34,211,238,.14);
            flex-shrink:0;
        }

        .content{
            padding: 0 2rem 1.25rem;
        }

        .alert-wrap{ padding: 1rem 2rem 0; }
        .alert{
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            display:flex;
            align-items:flex-start;
            gap: 10px;
            animation: pop .22s ease-out;
        }
        @keyframes pop{ from{ transform: translateY(6px); opacity:.0; } to { transform: translateY(0); opacity:1; } }
        .alert-success{ border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12); }
        .alert-danger{ border-color: rgba(251,113,133,.35); background: rgba(251,113,133,.12); }

        .sections{
            display:grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 14px;
        }

        .section-card{
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 16px;
        }

        .section-title{
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }
        .section-title h2{
            font-size: 13px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(234,240,255,.78);
            font-weight: 600;
        }
        .section-badge{
            font-size: 12px;
            color: rgba(234,240,255,.78);
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.05);
        }

        .grid2{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .field{
            display:flex;
            flex-direction: column;
            gap: 6px;
        }
        .field-label{
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(234,240,255,.54);
        }
        .field-value{
            font-size: 14px;
            color: rgba(234,240,255,.92);
            line-height: 1.45;
            word-break: break-word;
        }
        .field-value.muted{ color: rgba(234,240,255,.60); }

        .field-input,
        .field-textarea{
            font-size: 14px;
            color: rgba(234,240,255,.95);
            border: 1px solid rgba(255,255,255,.16);
            border-radius: var(--radius-field);
            padding: 10px 12px;
            font-family: 'DM Sans', sans-serif;
            background: rgba(0,0,0,.18);
            width: 100%;
            outline: none;
            transition: border-color .18s ease, background .18s ease, box-shadow .18s ease;
            resize: none;
        }
        .field-input:focus,
        .field-textarea:focus{
            border-color: rgba(34,211,238,.55);
            box-shadow: 0 0 0 4px rgba(34,211,238,.12);
            background: rgba(0,0,0,.26);
        }

        .solo{ grid-column: 1 / -1; }

        .card-footer{
            padding: 1.1rem 2rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.10);
            background: rgba(0,0,0,.10);
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 14px;
        }
        .footer-hint{
            font-size: 12px;
            color: rgba(234,240,255,.55);
        }

        .btn-group{ display:flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }

        .btn{
            font-size: 13px;
            padding: 10px 16px;
            border-radius: var(--radius-btn);
            cursor:pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            color: rgba(234,240,255,.92);
            transition: transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap: 8px;
            user-select:none;
        }
        .btn:hover{ transform: translateY(-2px); background: rgba(255,255,255,.10); border-color: rgba(34,211,238,.38); box-shadow: 0 18px 40px rgba(0,0,0,.35); }

        .btn-dark{
            background: linear-gradient(135deg, rgba(124,58,237,.65), rgba(34,211,238,.25));
            border-color: rgba(124,58,237,.35);
        }
        .btn-dark:hover{ background: linear-gradient(135deg, rgba(124,58,237,.75), rgba(34,211,238,.32)); }

        .btn-save{
            background: linear-gradient(135deg, rgba(34,197,94,.65), rgba(34,211,238,.15));
            border-color: rgba(34,197,94,.38);
        }
        .btn-save:hover{ background: linear-gradient(135deg, rgba(34,197,94,.78), rgba(34,211,238,.20)); }

        @media (max-width: 520px){
            body{ padding: 1.5rem .8rem 3rem; }
            .identity{ padding: 0 1.1rem 1.1rem; margin-top: -44px; }
            .content{ padding: 0 1.1rem 1.1rem; }
            .alert-wrap{ padding: 1rem 1.1rem 0; }
            .card-footer{ padding: 1rem 1.1rem 1.1rem; flex-direction: column; align-items:flex-start; }
            .btn-group{ justify-content:flex-start; }
            .grid2{ grid-template-columns: 1fr; }
            .solo{ grid-column: auto; }
            .identity-left{ gap: 12px; }
            .avatar{ width: 74px; height: 74px; border-radius: 18px; font-size: 24px; }
            .role-pill{ display:none; }
        }
    </style>
</head>
<body>

<div class="wrap">
    <nav class="breadcrumb">
        <a href="index.php">Trang chủ</a> &rsaquo; <span>Hồ sơ cá nhân</span> &rsaquo;<a href="membership.php">Ưu đãi thành viên</a>
    </nav>

    <form action="profile.php?action=edit" method="POST" enctype="multipart/form-data">
        <div class="card">
            <div class="top-cover"></div>

            <?php if ($success || $error): ?>
                <div class="alert-wrap">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <span aria-hidden="true">✅</span>
                            <div><?= htmlspecialchars($success) ?></div>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger">
                            <span aria-hidden="true">⚠️</span>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="identity">
                <div class="identity-left">
                    <div class="avatar-wrap">
                        <div class="avatar">
                            <?php if ($has_avatar): ?>
                                <img src="<?= htmlspecialchars($avatar_path) ?>" alt="Avatar" id="previewImg">
                            <?php else: ?>
                                <?= htmlspecialchars($initials) ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($mode == 'edit'): ?>
                            <label for="file_avatar" class="avatar-edit-btn" title="Đổi ảnh đại diện">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M11 2.5l2.5 2.5-7.5 7.5H3.5V10L11 2.5z" stroke="#eaf0ff" stroke-width="1.4" stroke-linejoin="round"/>
                                </svg>
                            </label>
                            <input type="file" name="anh_dai_dien" id="file_avatar" class="d-none" accept="image/*" style="display:none">
                        <?php endif; ?>
                    </div>

                    <div class="id-text">
                        <h1><?= htmlspecialchars($user['ten']) ?></h1>
                        <div class="email">
                            <span><?= htmlspecialchars($user['email']) ?></span>
                            <?php if ($mode == 'edit'): ?>
                                <span class="editing-tag">Đang chỉnh sửa</span>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top:10px;">
                            <span class="role-pill">
                                <span class="role-dot"></span>
                                <?= $user['vai_tro'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="sections">
                    <div class="section-card">
                        <div class="section-title">
                            <h2>Thông tin cơ bản</h2>
                            <span class="section-badge">Profile</span>
                        </div>

                        <div class="grid2">
                            <div class="field">
                                <span class="field-label">Họ và tên</span>
                                <?php if ($mode == 'edit'): ?>
                                    <input type="text" name="ten" class="field-input" value="<?= htmlspecialchars($user['ten']) ?>" required>
                                <?php else: ?>
                                    <div class="field-value"><?= htmlspecialchars($user['ten']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <span class="field-label">Số điện thoại</span>
                                <?php if ($mode == 'edit'): ?>
                                    <input type="text" name="so_dien_thoai" class="field-input" value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>" placeholder="Chưa có số điện thoại">
                                <?php else: ?>
                                    <div class="field-value <?= empty($user['so_dien_thoai']) ? 'muted' : '' ?>">
                                        <?= htmlspecialchars($user['so_dien_thoai'] ?? 'Chưa cập nhật') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <span class="field-label">Email</span>
                                <div class="field-value muted"><?= htmlspecialchars($user['email']) ?></div>
                            </div>

                            <div class="field">
                                <span class="field-label">Vai trò</span>
                                <div class="field-value">
                                    <?= $user['vai_tro'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-title">
                            <h2>Liên hệ & địa chỉ</h2>
                            <span class="section-badge">Contact</span>
                        </div>

                        <div class="grid2">
                            <div class="field solo">
                                <span class="field-label">Địa chỉ</span>
                                <?php if ($mode == 'edit'): ?>
                                    <textarea name="dia_chi" class="field-textarea" rows="2" placeholder="Nhập địa chỉ của bạn"><?= htmlspecialchars($user['dia_chi'] ?? '') ?></textarea>
                                <?php else: ?>
                                    <div class="field-value <?= empty($user['dia_chi']) ? 'muted' : '' ?>">
                                        <?= htmlspecialchars($user['dia_chi'] ?? 'Chưa cập nhật địa chỉ') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <?php if ($mode == 'view'): ?>
                    <span class="footer-hint">Thông tin cá nhân của bạn</span>
                    <div class="btn-group">
                        <a href="index.php" class="btn">Quay lại</a>
                        <a href="profile.php?action=edit" class="btn btn-dark">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                <path d="M11 2.5l2.5 2.5-7.5 7.5H3.5V10L11 2.5z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                            </svg>
                            Chỉnh sửa
                        </a>
                    </div>
                <?php else: ?>
                    <span class="footer-hint">Nhấn Lưu để xác nhận thay đổi</span>
                    <div class="btn-group">
                        <a href="profile.php" class="btn">Hủy bỏ</a>
                        <button type="submit" name="btn_save" class="btn btn-save">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                <path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Lưu thay đổi
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    const fileInput = document.getElementById('file_avatar');
    if (fileInput) {
        fileInput.onchange = function() {
            const file = this.files[0];
            if (!file) return;
            const preview = document.getElementById('previewImg');
            const url = URL.createObjectURL(file);
            if (preview) {
                preview.src = url;
            } else {
                const av = document.querySelector('.avatar');
                av.innerHTML = '<img id="previewImg" src="' + url + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover">';
            }
        };
    }
</script>

</body>
</html>
