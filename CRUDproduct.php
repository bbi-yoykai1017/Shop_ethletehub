<?php
session_start();
require_once 'Database.php';
require_once 'model/CRUD.php';
require_once 'auth.php';
// Truy vấn lấy ID và Tên danh mục

$db = new Database();
$conn = $db->connect();

$update_mode = false;

$edit_product = [
    'id' => '',
    'danh_muc_id' => '',
    'ten' => '',
    'mo_ta' => '',
    'gia' => '',
    'gia_goc' => '',
    'phan_tram_giam' => 0,
    'hinh_anh_chinh' => ''
];
// Truy vấn lấy ID và Tên danh mục sap xep theoo id tang dan
$sql_dm = "SELECT id, ten_danh_muc FROM danh_muc ORDER BY id ASC";
/** @var PDOStatement $stmt_dm */
$stmt_dm = $conn->prepare($sql_dm);
$stmt_dm->execute();
$list_danhmuc = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);
// ================= 1. XỬ LÝ LẤY DỮ LIỆU ĐỂ SỬA (Sửa Link ở dưới thành ?edit=) =================
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $edit_product = $result;
        $update_mode = true;
    }
}

// ================= 2. XỬ LÝ THÊM HOẶC CẬP NHẬT =================
if (isset($_POST['save_product'])) {
    $danh_muc_id = $_POST['danh_muc_id'];
    $ten = $_POST['ten'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia'];
    $gia_goc = $_POST['gia_goc'];
    $phan_tram_giam = $_POST['phan_tram_giam'];
   


    // Xử lý Upload Ảnh
    $hinh_anh = $_POST['hinh_anh_cu'] ?? '';

    if (isset($_FILES['hinh_anh_upload']) && $_FILES['hinh_anh_upload']['error'] == 0) {
        $target_dir = "public/";
        $file_name = basename($_FILES["hinh_anh_upload"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["hinh_anh_upload"]["tmp_name"], $target_file)) {
            $hinh_anh = $file_name; // Gán tên file mới để lưu vào DB
        }
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "UPDATE san_pham SET danh_muc_id=?, ten=?, mo_ta=?, gia=?, gia_goc=?, phan_tram_giam=?, hinh_anh_chinh=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$danh_muc_id, $ten, $mo_ta, $gia, $gia_goc, $phan_tram_giam, $hinh_anh, $id]);
    } else {
        $sql = "INSERT INTO san_pham (danh_muc_id, ten, mo_ta, gia, gia_goc, phan_tram_giam,  hinh_anh_chinh) VALUES (?,  ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$danh_muc_id, $ten, $mo_ta, $gia, $gia_goc, $phan_tram_giam, $hinh_anh]);
    }
    header("Location: CRUDproduct.php");
    exit;
}

// ================= 3. XỬ LÝ XÓA (Sửa Link ở dưới thành ?delete=) =================
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['delete']]);
    header("Location: CRUDproduct.php");
    exit;
}

$listproduct = getAllProducts($conn);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý sản phẩm - EthleteHub</title>



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

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <ul>
                <li><a href="CRUDproduct.php" class="active"><i class="fas fa-box me-2"></i> Sản phẩm</a></li>
                <li><a href="CRUDuser.php"><i class="fas fa-users me-2"></i> Khách hàng</a></li>
                <li><a href="CRUDdonhang.php"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a></li>
                <li><a href="CRUDgiamgia.php"><i class="fas fa-tags me-2"></i> Mã giảm giá</a></li>
                <li class="d-lg-none"><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
            </ul>
        </aside>

        <!-- NỘI DUNG -->
        <div class="main-content">

            <div class="card shadow border-0 mb-4" >
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <?php if ($update_mode): ?>
                            <i class="fas fa-edit me-2"></i> Chỉnh sửa sản phẩm
                        <?php else: ?>
                            <i class="fas fa-plus-circle me-2"></i> Thêm sản phẩm mới
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">  
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Danh mục sản phẩm</label>
                            <select name="danh_muc_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($list_danhmuc as $dm): ?>
                                    <option value="<?= $dm['id'] ?>"
                                        <?= ($update_mode && $edit_product['danh_muc_id'] == $dm['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>

                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tên sản phẩm</label>
                            <input type="text" name="ten" class="form-control" value="<?= $edit_product['ten'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <input type="text" name="mo_ta" class="form-control" value="<?= $edit_product['mo_ta'] ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Giá gốc</label>
                            <input type="number" id="gia_goc" name="gia_goc" class="form-control" value="<?= $edit_product['gia_goc'] ?>" min="0">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Giá bán</label>
                            <input type="number" id="gia_ban" name="gia" class="form-control" value="<?= $edit_product['gia'] ?>" min="0" required>
                            <div class="invalid-feedback">Giá bán không được cao hơn giá gốc!</div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Phần trăm giảm (%)</label>
                            <input type="number" id="phan_tram_giam" step="1" name="phan_tram_giam" class="form-control" value="<?= $edit_product['phan_tram_giam'] ?>" required>
                            <small class="text-muted">Phần trăm giảm phải nằm trong khoảng 0-100</small>
                        </div>


                        <div class="col-md-3">
                            <label class="form-label fw-bold">Hình ảnh sản phẩm</label>
                            <input type="file" name="hinh_anh_upload" class="form-control" accept="image/*">
                            <?php if ($update_mode && !empty($edit_product['hinh_anh_chinh'])): ?>
                                <small class="text-muted">Ảnh hiện tại: <?= $edit_product['hinh_anh_chinh'] ?></small>
                                <input type="hidden" name="hinh_anh_cu" value="<?= $edit_product['hinh_anh_chinh'] ?>">
                            <?php endif; ?>
                        </div>                  
                        <div class="col-md-3 d-flex align-items-end">
                            <?php if ($update_mode): ?>
                                <button name="save_product" class="btn btn-warning w-50 me-2">Cập nhật</button>
                                <a href="CRUDproduct.php" class="btn btn-secondary w-50">Hủy</a>
                            <?php else: ?>
                                <button name="save_product" class="btn btn-success w-100">Lưu sản phẩm</button>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 ">
                                <span class="badge bg-secondary">Tổng cộng: <?= count($listproduct) ?> sản phẩm</span>
                         </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">

                <table class="table table-hover align-middle text-center">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Danh mục</th>
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Giá bán</th>
                            <th>Giá gốc</th>
                            <th>Phần trăm giảm</th>                  
                          <th>Hình ảnh</th>
                            <th width="180">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listproduct as $product) { ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['danh_muc_id'] ?></td>
                                <td><?= $product['ten'] ?></td>
                                <td><?= $product['mo_ta'] ?></td>
                                <td><?= $product['gia'] ?></td>
                                <td><?= $product['gia_goc'] ?></td>
                                <td><?= $product['phan_tram_giam'] ?></td>                        
                                <td>
                                    <img src="./public/<?php echo htmlspecialchars($product['hinh_anh_chinh']); ?>"
                                        alt="<?= $product['ten'] ?>" width="80" height="80">
                                </td>
                                <td>
                                    <a href="?edit=<?= $product['id'] ?>" class="btn btn-sm btn-outline-warning">Sửa</a>
                                    <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    </div>

    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
        EthleteHub Admin © 2026
    </footer>

    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const giaGocInput = document.getElementById('gia_goc');
        const giaBanInput = document.getElementById('gia_ban');
        const phanTramInput = document.getElementById('phan_tram_giam');
        const btnSave = document.querySelector('button[name="save_product"]');

        function validateForm(e) {
            let giaGoc = parseFloat(giaGocInput.value) || 0;
            let giaBan = parseFloat(giaBanInput.value) || 0;
            let phanTram = parseFloat(phanTramInput.value) || 0;
            let isValid = true;

            // Lấy ID của ô đang trực tiếp gõ vào
            const activeId = e ? e.target.id : "";

            if (giaGoc > 0) {
                // TRƯỜNG HỢP 1: Đang gõ vào ô Giá Bán -> Tính % Giảm
                if (activeId === 'gia_ban') {
                    if (giaBanInput.value === "") {
                        phanTramInput.value = "";
                    } else if (giaBan <= giaGoc) {
                        phanTramInput.value = (((giaGoc - giaBan) / giaGoc) * 100).toFixed(1);
                    }
                } 
                // TRƯỜNG HỢP 2: Đang gõ vào ô % Giảm -> Tính Giá Bán
                else if (activeId === 'phan_tram_giam') {
                    if (phanTramInput.value === "") {
                        giaBanInput.value = giaGoc; // Nếu xóa trống % thì giá bán = giá gốc
                    } else if (phanTram >= 0 && phanTram <= 100) {
                        let tinhGiaBan = Math.round(giaGoc * (1 - phanTram / 100));
                        giaBanInput.value = tinhGiaBan;
                        giaBan = tinhGiaBan; // Cập nhật để validate bên dưới
                    }
                }
                // TRƯỜNG HỢP 3: Đang gõ vào ô Giá Gốc -> Cập nhật lại % hoặc Giá bán tùy bạn chọn
                // Ở đây ta ưu tiên giữ nguyên % và tính lại Giá bán
                else if (activeId === 'gia_goc' && phanTramInput.value !== "") {
                     giaBanInput.value = Math.round(giaGoc * (1 - phanTram / 100));
                     giaBan = parseFloat(giaBanInput.value);
                }
            }

            // --- KIỂM TRA VALIDATE ---
            // Validate Giá bán
            if (giaGoc > 0 && giaBan > giaGoc) {
                giaBanInput.classList.add('is-invalid');
                isValid = false;
            } else {
                giaBanInput.classList.remove('is-invalid');
                if (giaGoc > 0 && giaBan > 0) giaBanInput.classList.add('is-valid');
            }

            // Validate % Giảm
           if (phanTramInput.value !== "" && (phanTram < 0 || phanTram > 100)) {
                phanTramInput.classList.add('is-invalid');
                phanTramInput.classList.remove('is-valid');
                isValid = false;
            } else {
                phanTramInput.classList.remove('is-invalid');
                // Nếu ô không trống, thì dù là 0 vẫn tính là hợp lệ (is-valid)
                if (phanTramInput.value !== "") phanTramInput.classList.add('is-valid');
            }

            if (btnSave) btnSave.disabled = !isValid;
        }

        // Lắng nghe sự kiện
        [giaGocInput, giaBanInput, phanTramInput].forEach(input => {
            // Truyền event 'e' vào hàm để biết chính xác target
            input.addEventListener('input', (e) => validateForm(e));
        });
    });
</script>
</body>

</html>