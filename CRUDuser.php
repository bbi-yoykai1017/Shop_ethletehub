<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';

$db = new Database();
$conn = $db->connect();

$update_mode = false;
$edit_user = ['id' => '', 'ten' => '', 'email' => '', 'so_dien_thoai' => '', 'vai_tro' => '', 'trang_thai' => 'hoat_dong'];

// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA =================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_user = $result;
        $update_mode = true; // Bật chế độ cập nhật
    }
}

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_user'])) {
    $ten = $_POST['ten'];
    $email = $_POST['email'];
    $sdt = $_POST['so_dien_thoai'];
    $vai_tro = $_POST['vai_tro']; // Lấy vai trò từ form
    $trang_thai = $_POST['trang_thai']; // Lấy trạng thái từ form


    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // CẬP NHẬT
        $id = $_POST['id'];
        $sql = "UPDATE nguoi_dung SET ten=?, email=?, so_dien_thoai=?, vai_tro=?, trang_thai=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $email, $sdt, $vai_tro, $trang_thai, $id]);
    } else {
        // THÊM MỚI
        $sql = "INSERT INTO nguoi_dung (ten, email, so_dien_thoai, vai_tro, trang_thai) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $email, $sdt, $vai_tro, $trang_thai]);
    }
    header("Location: CRUDuser.php");
    exit;
}

// ================= 3. XỬ LÝ "Khóa" (CHUYỂN TRẠNG THÁI) =================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Thay vì DELETE, chúng ta UPDATE trạng thái
    $sql = "UPDATE nguoi_dung SET trang_thai = 'bi_khoa' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$id])) {
        // Thông báo thành công (có thể dùng session để hiện alert)
        header("Location: CRUDuser.php?msg=locked");
    } else {
        header("Location: CRUDuser.php?msg=error");
    }
    exit;
}
// ================= 4. XỬ LÝ TÌM KIẾM & PHÂN TRANG =================

// Cấu hình phân trang
$limit = 10; // Số dòng trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Xử lý từ khóa tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];
$where_sql = "";

if (!empty($search)) {
    // Nếu nội dung tìm kiếm là số, ưu tiên tìm chính xác ID người dùng
    if (is_numeric($search)) {
        $where_sql = " WHERE id = ? ";
        $params[] = $search; // Tìm chính xác số ID
       // $params[] = "%$search%"; // tuong doi
    } else {
        // Nếu là chữ, tìm gần đúng theo ten
        $where_sql = " WHERE ten LIKE ? ";
        $params[] = "%$search%";
    }
}

// Đếm tổng số dòng để tính số trang
$total_sql = "SELECT COUNT(*) FROM nguoi_dung" . $where_sql;
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu theo trang và tìm kiếm
$sql_list = "SELECT * FROM nguoi_dung" . $where_sql . " ORDER BY id ASC LIMIT $limit OFFSET $offset";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->execute($params);
$listusers = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý người dùng - EthleteHub</title>



    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <link href="css/crud.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/admin-layout.css">
    <link rel="stylesheet" href="css/page-link.css">
</head>

<body style="background:#f4f6f9;">


    <!-- NAVBAR ADMIN -->

    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-speedometer2"></i> AthleteHub Admin
            </span>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-house"></i> Trang chủ
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="layout">

        <aside class="sidebar">

            <ul>
                <li><a href="CRUDproduct.php"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php" class="active"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="container-fluid p-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas <?= $update_mode ? 'fa-user-edit' : 'fa-user-plus' ?> me-2"></i>
                            <?= $update_mode ? "Cập nhật người dùng" : "Thêm người dùng mới" ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" class="row g-3 mb-4">
                            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label small fw-bold">Tên khách hàng</label>
                                <input type="text" name="ten" class="form-control" placeholder="Nhập tên..." value="<?= $edit_user['ten'] ?>" minlength="2" maxlength="100" required>
                                <div class="invalid-feedback">Tên khách hàng không được để trống và phải từ 2 đến 100 ký tự!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= $edit_user['email'] ?>" required>
                                <div class="invalid-feedback">Email không hợp lệ!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control" placeholder="SĐT" value="<?= $edit_user['so_dien_thoai'] ?>" minlength="10" maxlength="10" required>
                                <div class="invalid-feedback">Số điện thoại không hợp lệ!</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label small fw-bold">Vai trò</label>
                                <select class="form-select" name="vai_tro" required>
                                    <option value="khach_hang" <?= ($update_mode && $edit_user['vai_tro'] === 'khach_hang') ? 'selected' : '' ?>>Khách Hàng</option>
                                    <option value="admin" <?= ($update_mode && $edit_user['vai_tro'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label small fw-bold">Trạng thái</label>
                                <select class="form-select" name="trang_thai" required>
                                    <option value="hoat_dong" <?= ($update_mode && $edit_user['trang_thai'] === 'hoat_dong') ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="bi_khoa" <?= ($update_mode && $edit_user['trang_thai'] === 'bi_khoa') ? 'selected' : '' ?>>Bị khóa</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-2 d-flex align-items-end">
                                <?php if ($update_mode): ?>
                                    <div class="w-100">
                                        <button type="submit" name="save_user" class="btn btn-warning w-100 mb-1">Cập nhật</button>
                                        <a href="CRUDuser.php" class="btn btn-light btn-sm w-100 border">Hủy</a>
                                    </div>
                                <?php else: ?>
                                    <button type="submit" name="save_user" class="btn btn-success w-100"><i class="fas fa-plus me-1"></i> Thêm mới</button>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-10 text-end d-flex align-items-end justify-content-end">
                                <span class="badge bg-secondary">Tổng cộng: <?= count($listusers) ?> người dùng</span>
                            </div>
                        </form>
                           <!-- Tim kiem -->
                        <div class="filter-group mb-3">
                            <h4 class="filter-title"><i class="fas fa-search"></i> Tìm kiếm</h4>
                            <form method="GET" action="CRUDuser.php" class="search-box d-flex gap-2">
                                <input type="text" name="search" id="searchInput" class="form-control"
                                    placeholder="Nhập ID hoặc ten khách..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-primary">Tìm</button>
                                <?php if (!empty($search)): ?>
                                    <a href="CRUDuser.php" class="btn btn-outline-secondary">Xóa</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Thông tin khách hàng</th>
                                        <th class="text-center d-none d-md-table-cell">SĐT</th>
                                        <th class="text-center">Vai trò</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-end">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listusers as $user): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $user['id'] ?></td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($user['ten'] ?? 0) ?></div>
                                                <div class="small text-muted"><?= htmlspecialchars($user['email'] ?? 0) ?></div>
                                            </td>
                                            <td class="text-center d-none d-md-table-cell"><?= htmlspecialchars($user['so_dien_thoai'] ?? 0) ?></td>
                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-light text-dark border"><?= $user['vai_tro'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($user['trang_thai'] === 'hoat_dong'): ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Bị khóa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="?edit=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn KHÓA người dùng này không?')" title="Khóa tài khoản">
                                                        <i class="fas fa-user-slash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-section">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                                            <i class="fas fa-chevron-left"></i> Trước
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                                            <span>Tiếp</span> <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="text-center mt-2 small text-muted">
                                Hiển thị trang <?= $page ?> / <?= $total_pages ?> (Tổng <?= $total_rows ?> Người dùng)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tenInput = document.querySelector('input[name="ten"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.getElementById('so_dien_thoai');
            const btnSave = document.querySelector('button[name="save_user"]');

            function validateForm() {
                //1 kiem tra ky tu ten khach hang
                let isValid = true;

                let tenRaw = tenInput.value;
                let tenClean = tenRaw.trim().replace(/\s\s+/g, ' ');

                // Regex này cho phép: Chữ cái (Unicode), khoảng trắng, và flag 'u' để xử lý tiếng Việt chuẩn
                const vietnameseRegex = /^[\p{L}\s]+$/u;

                const isDoubleSpace = /\s\s+/.test(tenRaw);
                const isInvalidChar = !vietnameseRegex.test(tenClean);
                const isTooShort = tenClean.length < 2;

                const tenHasError = isDoubleSpace || isInvalidChar || isTooShort;

                if (!tenHasError) {
                    tenInput.classList.remove('is-invalid');
                    tenInput.classList.add('is-valid');
                } else {
                    tenInput.classList.add('is-invalid');
                    tenInput.classList.remove('is-valid');
                    isValid = false;

                    const feedback = tenInput.nextElementSibling;
                    if (isDoubleSpace) {
                        feedback.innerText = "Không được có 2 khoảng trắng liên tiếp!";
                    } else if (isInvalidChar) {
                        feedback.innerText = "Tên không được chứa số hoặc ký tự đặc biệt!";
                    } else if (isTooShort) {
                        feedback.innerText = "Tên phải từ 2 ký tự trở lên!";
                    }
                }

                // --- 2. KIỂM TRA EMAIL (Tối ưu: cấm khoảng trắng hoàn toàn) ---
                let emailRaw = emailInput.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const hasSpaceEmail = /\s/.test(emailRaw); // Email tuyệt đối không có khoảng trắng

                if (emailRegex.test(emailRaw) && !hasSpaceEmail) {
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
                } else {
                    emailInput.classList.add('is-invalid');
                    emailInput.classList.remove('is-valid');
                    isValid = false;
                    const feedbackEmail = emailInput.nextElementSibling;
                    if (hasSpaceEmail) feedbackEmail.innerText = "Email không được chứa khoảng trắng!";
                    else feedbackEmail.innerText = "Định dạng Email không hợp lệ (vd: abc@gmail.com)!";
                }

                // --- 3. KIỂM TRA SỐ ĐIỆN THOẠI (Tối ưu: 10 số, bắt đầu bằng 0, cấm khoảng trắng/chữ) ---
                let phoneRaw = phoneInput.value;
                const phoneRegex = /^0\d{9}$/;
                const hasSpacePhone = /\s/.test(phoneRaw);
                const hasCharPhone = /[a-zA-Z]/.test(phoneRaw);

                if (phoneRegex.test(phoneRaw) && !hasSpacePhone) {
                    phoneInput.classList.remove('is-invalid');
                    phoneInput.classList.add('is-valid');
                } else {
                    phoneInput.classList.add('is-invalid');
                    phoneInput.classList.remove('is-valid');
                    isValid = false;
                    const feedbackPhone = phoneInput.nextElementSibling;
                    if (hasSpacePhone) feedbackPhone.innerText = "Số điện thoại không được chứa khoảng trắng!";
                    else if (hasCharPhone) feedbackPhone.innerText = "Số điện thoại không được chứa chữ cái!";
                    else feedbackPhone.innerText = "SĐT phải gồm 10 số và bắt đầu bằng số 0!";
                }

                // Kích hoạt/Vô hiệu hóa nút Lưu
                btnSave.disabled = !isValid;
            }

            // Lắng nghe sự kiện 'input' để kiểm tra ngay lập tức khi gõ
            [tenInput, emailInput, phoneInput].forEach(input => {
                input.addEventListener('input', validateForm);
            });

            // Kiểm tra lần đầu khi load trang (hữu ích cho chế độ Edit)
            validateForm();
        });
    </script>
</body>

</html>