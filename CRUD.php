<?php
require_once 'Database.php';
require_once 'functions.php';

session_start();

// Kiểm tra: Nếu chưa đăng nhập HOẶC không phải là admin thì đá về trang chủ
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$db = new Database();
$conn = $db->connect();
$listusers = getAllUsers($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Shop Item - Start Bootstrap Template</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="bootstrap-5.3.8/js/bootstrap.bundle.min.js" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="bootstrap-5.3.8/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <!-- Navigation S4-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#!">Start Bootstrap</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#!">All Products</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item" href="#!">Popular Items</a></li>
                            <li><a class="dropdown-item" href="#!">New Arrivals</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex">
                    <button class="btn btn-outline-dark" type="submit">
                        <i class="bi-cart-fill me-1"></i>
                        Cart
                        <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
    <!-- Product section-->
    <section class="py-4">
        <div class="container px-4 px-lg-5 my-4">
            <div class="row gx-4 gx-lg-5 align-items-center">
                <div class="table-wrapper">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2>Manage <b>Categories</b></h2>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-success" href="frmthem.php"><span>Add New Category</span></a>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th> ID </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($listusers as $user) {
                            ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= $user['ten'] ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td><?= $user['so_dien_thoai'] ?></td>
                                    <td><?= $user['vai_tro'] ?></td>
                                    <a class="btn btn-warning" href="frmsua.php?cateid=<?= $loai['id'] ?>&catename=<?= $loai['name'] ?>&cateimage=<?= $loai['image'] ?>">Edit</a>
                                    <a onclick="return confirm('Bạn có muốn xóa category <?= $loai['id'] ?>')" class="btn btn-danger" href="xulidelete.php?cateid=<?= $loai['id'] ?>">Delete</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>

    <!-- Footer S4-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Your Website 2023</p>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>

</body>

</html>
