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

        :root {
            --ink: #1a1a1a;
            --ink-muted: #999;
            --ink-faint: #bbb;
            --surface: #fff;
            --bg: #f5f4f1;
            --border: #ebebeb;
            --border-light: #f0f0f0;
            --accent: #1a1a2e;
            --green: #1D9E75;
            --green-bg: #eaf3de;
            --green-border: #c0dd97;
            --green-text: #3B6D11;
            --radius-card: 24px;
            --radius-field: 10px;
            --radius-btn: 12px;
        }

        body {
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            padding: 2.5rem 1rem 4rem;
        }

        .wrap {
            max-width: 680px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* ─── Breadcrumb ─── */
        .breadcrumb {
            font-size: 12px;
            color: var(--ink-muted);
            letter-spacing: .04em;
            padding: 0 4px;
        }
        .breadcrumb a { color: var(--ink-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--ink); }
        .breadcrumb span { color: var(--ink); }

        /* ─── Card ─── */
        .card {
            background: var(--surface);
            border-radius: var(--radius-card);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        /* ─── Identity block ─── */
        .identity {
            padding: 2rem 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border-bottom: 1px solid var(--border-light);
        }

        .avatar-wrap { position: relative; flex-shrink: 0; }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Instrument Serif', serif;
            font-size: 26px;
            color: #e8e4d9;
            overflow: hidden;
        }
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: -6px;
            right: -6px;
            width: 24px;
            height: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s;
        }
        .avatar-edit-btn:hover { background: var(--bg); }

        .id-text h1 {
            font-family: 'Instrument Serif', serif;
            font-size: 22px;
            font-weight: 400;
            color: var(--ink);
            line-height: 1.2;
        }
        .id-text .email {
            font-size: 13px;
            color: var(--ink-muted);
            margin-top: 4px;
        }
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            padding: 3px 10px;
            border-radius: 20px;
            background: #f0f0f0;
            font-size: 11px;
            color: #555;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .role-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
        }

        /* ─── Alert ─── */
        .alert-wrap { padding: 1.25rem 2rem 0; }
        .alert {
            padding: 10px 16px;
            border-radius: var(--radius-field);
            font-size: 13px;
        }
        .alert-success {
            background: var(--green-bg);
            color: var(--green-text);
            border: 1px solid var(--green-border);
        }
        .alert-danger {
            background: #fcebeb;
            color: #791f1f;
            border: 1px solid #f7c1c1;
        }

        /* ─── Fields ─── */
        .fields { padding: 0 2rem; }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .field-row:last-child { border-bottom: none; }
        .field-row.solo { grid-template-columns: 1fr; }

        .field-cell {
            padding: 1.1rem 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .field-cell:first-child:not(:last-child) {
            padding-right: 2rem;
            border-right: 1px solid #f5f5f5;
        }
        .field-cell:last-child:not(:first-child) { padding-left: 2rem; }

        .field-label {
            font-size: 10px;
            font-weight: 500;
            color: var(--ink-faint);
            text-transform: uppercase;
            letter-spacing: .1em;
        }
        .field-value {
            font-size: 14px;
            color: var(--ink);
            line-height: 1.5;
        }
        .field-value.muted { color: var(--ink-muted); }

        .field-input,
        .field-textarea {
            font-size: 14px;
            color: var(--ink);
            border: 1px solid var(--border);
            border-radius: var(--radius-field);
            padding: 8px 12px;
            font-family: 'DM Sans', sans-serif;
            background: #fafafa;
            width: 100%;
            outline: none;
            transition: border-color .2s, background .2s;
            resize: none;
        }
        .field-input:focus,
        .field-textarea:focus {
            border-color: var(--accent);
            background: var(--surface);
        }

        /* ─── Footer ─── */
        .card-footer {
            padding: 1.25rem 2rem;
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-light);
        }
        .footer-hint {
            font-size: 12px;
            color: var(--ink-faint);
        }
        .btn-group { display: flex; gap: 8px; }

        .btn {
            font-size: 13px;
            padding: 8px 20px;
            border-radius: var(--radius-btn);
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            border: 1px solid var(--border);
            background: var(--surface);
            color: #444;
            transition: background .15s, border-color .15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:hover { background: #f5f5f5; }

        .btn-dark {
            background: var(--accent);
            color: #e8e4d9;
            border-color: var(--accent);
        }
        .btn-dark:hover { background: #2d2d4e; border-color: #2d2d4e; }

        .btn-save {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }
        .btn-save:hover { background: #0f8060; border-color: #0f8060; }

        /* ─── Editing indicator ─── */
        .editing-tag {
            font-size: 12px;
            color: var(--green);
        }

        @media (max-width: 520px) {
            body { padding: 1.5rem .75rem 3rem; }
            .identity { padding: 1.5rem 1.25rem 1.25rem; gap: 1rem; }
            .identity .avatar { width: 64px; height: 64px; border-radius: 16px; font-size: 21px; }
            .fields { padding: 0 1.25rem; }
            .field-row { grid-template-columns: 1fr; }
            .field-cell:first-child:not(:last-child) { padding-right: 0; border-right: none; border-bottom: 1px solid #f5f5f5; }
            .field-cell:last-child:not(:first-child) { padding-left: 0; }
            .card-footer { padding: 1rem 1.25rem; flex-direction: column; gap: 12px; align-items: flex-end; }
            .alert-wrap { padding: 1rem 1.25rem 0; }
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

            <?php if ($success || $error): ?>
            <div class="alert-wrap">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Identity -->
            <div class="identity">
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
                        <svg width="11" height="11" viewBox="0 0 16 16" fill="none">
                            <path d="M11 2.5l2.5 2.5-7.5 7.5H3.5V10L11 2.5z" stroke="#666" stroke-width="1.5" stroke-linejoin="round"/>
                        </svg>
                    </label>
                    <input type="file" name="anh_dai_dien" id="file_avatar" class="d-none" accept="image/*" style="display:none">
                    <?php endif; ?>
                </div>
                <div class="id-text">
                    <h1><?= htmlspecialchars($user['ten']) ?></h1>
                    <p class="email">
                        <?= htmlspecialchars($user['email']) ?>
                        <?php if ($mode == 'edit'): ?>
                            &nbsp;·&nbsp;<span class="editing-tag">Đang chỉnh sửa</span>
                        <?php endif; ?>
                    </p>
                    <div class="role-pill">
                        <span class="role-dot"></span>
                        <?= $user['vai_tro'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>
                    </div>
                </div>
            </div>

            <!-- Fields -->
            <div class="fields">

                <div class="field-row">
                    <div class="field-cell">
                        <span class="field-label">Họ và tên</span>
                        <?php if ($mode == 'edit'): ?>
                            <input type="text" name="ten" class="field-input"
                                   value="<?= htmlspecialchars($user['ten']) ?>" required>
                        <?php else: ?>
                            <span class="field-value"><?= htmlspecialchars($user['ten']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="field-cell">
                        <span class="field-label">Số điện thoại</span>
                        <?php if ($mode == 'edit'): ?>
                            <input type="text" name="so_dien_thoai" class="field-input"
                                   value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>"
                                   placeholder="Chưa có số điện thoại">
                        <?php else: ?>
                            <span class="field-value <?= empty($user['so_dien_thoai']) ? 'muted' : '' ?>">
                                <?= htmlspecialchars($user['so_dien_thoai'] ?? 'Chưa cập nhật') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field-cell">
                        <span class="field-label">Email</span>
                        <span class="field-value muted"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="field-cell">
                        <span class="field-label">Vai trò</span>
                        <span class="field-value">
                            <?= $user['vai_tro'] == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>
                        </span>
                    </div>
                </div>

                <div class="field-row solo">
                    <div class="field-cell">
                        <span class="field-label">Địa chỉ</span>
                        <?php if ($mode == 'edit'): ?>
                            <textarea name="dia_chi" class="field-textarea" rows="2"
                                      placeholder="Nhập địa chỉ của bạn"><?= htmlspecialchars($user['dia_chi'] ?? '') ?></textarea>
                        <?php else: ?>
                            <span class="field-value <?= empty($user['dia_chi']) ? 'muted' : '' ?>">
                                <?= htmlspecialchars($user['dia_chi'] ?? 'Chưa cập nhật địa chỉ') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer">
                <?php if ($mode == 'view'): ?>
                    <span class="footer-hint">Thông tin cá nhân của bạn</span>
                    <div class="btn-group">
                        <a href="index.php" class="btn">Quay lại</a>
                        <a href="profile.php?action=edit" class="btn btn-dark">
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="none">
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
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="none">
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