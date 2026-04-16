<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi - Athlete Hub Professional</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/news.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/utilities.css">
    <style>
        :root {
            --primary: #e63946;
            --secondary: #1d3557;
            --accent: #457b9d;
            --light: #f1faee;
            --dark: #1d3557;
            --gray: #666;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.8;
            color: #333;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
        }

        /* --- Hero Section --- */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=1470');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        /* --- Section Styling --- */
        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--secondary);
            text-transform: uppercase;
        }

        .section-title p {
            color: var(--primary);
            font-weight: 600;
        }

        /* --- Philosophy (Z-Layout) --- */
        .flex-row {
            display: flex;
            align-items: center;
            gap: 50px;
            margin-bottom: 60px;
        }

        .flex-row:nth-child(even) {
            flex-direction: row-reverse;
        }

        .content-box {
            flex: 1;
        }

        .image-box {
            flex: 1;
        }

        .image-box img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 20px 20px 0px var(--light);
        }

        /* --- Steps Section --- */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .step-card {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            border-bottom: 5px solid var(--primary);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .step-card span {
            font-size: 3rem;
            font-weight: 800;
            color: rgba(230, 57, 70, 0.2);
            display: block;
        }

        /* --- Team Section --- */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .team-item {
            text-align: center;
        }

        .team-item img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 5px solid var(--light);
        }

        /* --- Call to Action (CTA) --- */
        .cta {
            background: var(--secondary);
            color: #fff;
            text-align: center;
            padding: 100px 20px;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #fff;
            color: var(--primary);
        }

        /* Hiệu ứng xuất hiện lần lượt cho 4 bước */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .steps-grid .step-card {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            border: none;
            /* Bỏ border cũ */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1;
            /* Gán animation xuất hiện */
            animation: fadeInUp 0.8s ease backwards;
        }

        /* Tạo độ trễ cho từng card để xuất hiện lần lượt */
        .step-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .step-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .step-card:nth-child(3) {
            animation-delay: 0.5s;
        }

        .step-card:nth-child(4) {
            animation-delay: 0.7s;
        }

        /* Hiệu ứng Hover cực đẹp */
        .step-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(230, 57, 70, 0.15);
            background: var(--secondary);
            color: #fff;
        }

        /* Chữ số 01, 02 ẩn sau nền */
        .step-card span {
            font-size: 5rem;
            font-weight: 900;
            color: rgba(230, 57, 70, 0.1);
            /* Màu đỏ nhạt */
            position: absolute;
            top: -10px;
            right: 10px;
            z-index: -1;
            transition: all 0.4s;
        }

        .step-card:hover span {
            color: rgba(255, 255, 255, 0.1);
            transform: scale(1.2);
        }

        /* Biểu tượng Icon (Thêm vào HTML phía dưới) */
        .step-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
            display: inline-block;
            transition: all 0.4s;
        }

        .step-card:hover i {
            color: #fff;
            transform: rotateY(360deg);
            /* Xoay vòng khi hover */
        }

        .step-card h3 {
            margin-bottom: 15px;
            font-weight: 700;
        }

        .step-card p {
            font-size: 0.95rem;
            color: var(--gray);
            transition: all 0.4s;
        }

        .step-card:hover p {
            color: #e0e0e0;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .flex-row,
            .flex-row:nth-child(even) {
                flex-direction: column;
            }

            .hero h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container px-4">
            <div class="row">
                <div class="col-12">
                    <!-- Logo & Brand -->
                    <a class="navbar-brand" href="index.php">
                        <i class="fas fa-dumbbell"></i>
                        AthleteHub
                    </a>
                </div>
                <div class="col-12">
                    <!-- Mobile Menu Toggle -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Navbar Content -->
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <!-- Left Navigation Links -->
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link active" href="index.php">Trang chủ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="products.php">Sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#categories">Danh mục</a>
                        </ul>                        
                    </div>
                </div>
            </div>
    </nav>
    <section class="hero">
        <div class="container">
            <h2>ATHLETE HUB</h2>
            <p>Nâng tầm giới hạn - Bứt phá thành công</p>
        </div>
    </section>

    <section class="container">
        <div class="flex-row">
            <div class="content-box">
                <h1 style="color:var(--primary); font-weight:bold;">Về Chúng Tôi</h1>
                <h2>Không chỉ là trang phục, đó là động lực</h2>
                <p>Tại <strong>Athlete Hub</strong>, chúng tôi tin rằng mỗi bộ đồ tập không đơn thuần là vật dụng hỗ trợ. Chúng là "ngôn ngữ" khẳng định kỷ luật bản thân. Chúng tôi không chạy theo doanh số ngắn hạn, chúng tôi xây dựng một cộng đồng bền vững, nơi mọi người truyền cảm hứng tập luyện cho nhau.</p>
            </div>
            <div class="image-box">
                <img src="public/trietly.jpg" alt="Triết lý">
            </div>
        </div>

        <div class="flex-row">
            <div class="content-box">
                <h2>Hành trình từ đam mê</h2>
                <p>Ra đời từ những ngày đầu năm 2024 tại một căn phòng nhỏ, đội ngũ sáng lập của Athlete Hub – vốn là những người đam mê Gym và chạy bộ – đã luôn trăn trở về việc tìm kiếm những bộ đồ vừa túi tiền nhưng vẫn đạt chuẩn quốc tế.</p>
                <p>Hôm nay, chúng tôi tự hào mang đến giải pháp tối ưu cho hàng nghìn vận động viên tại Việt Nam.</p>
            </div>
            <div class="image-box">
                <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=1000" alt="Hành trình">
            </div>
        </div>
    </section>

    <section style="background: #fdfdfd; overflow: hidden;">
        <div class="container">
            <div class="section-title">
                <p>Quy trình chuyên nghiệp</p>
                <h2>Cách chúng tôi vận hành</h2>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <span>01</span>
                    <i class="fas fa-microchip"></i>
                    <h3>Tuyển chọn</h3>
                    <p>Làm việc trực tiếp với các nhà máy đạt tiêu chuẩn xuất khẩu để chọn lọc sợi vải kỹ thuật cao.</p>
                </div>
                <div class="step-card">
                    <span>02</span>
                    <i class="fas fa-running"></i>
                    <h3>Thử nghiệm</h3>
                    <p>Mẫu thiết kế được thử nghiệm thực tế bởi các HLV trong ít nhất 100 giờ tập luyện cường độ cao.</p>
                </div>
                <div class="step-card">
                    <span>03</span>
                    <i class="fas fa-check-double"></i>
                    <h3>Kiểm soát</h3>
                    <p>Hệ thống kiểm tra 3 lớp đảm bảo không có bất kỳ lỗi chỉ thừa hay sai sót nhỏ nào.</p>
                </div>
                <div class="step-card">
                    <span>04</span>
                    <i class="fas fa-box-open"></i>
                    <h3>Lắng nghe</h3>
                    <p>Mỗi đánh giá của bạn là dữ liệu quý giá để chúng tôi nâng cấp phiên bản sản phẩm tiếp theo.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="section-title">
            <h2>Đội ngũ nòng cốt</h2>
        </div>
        <div class="team-grid">
            <div class="team-item">
                <img src="public/Quan.png" alt="CEO">
                <h3>Minh Quân</h3>
                <p>Founder & CEO</p>
            </div>
            <div class="team-item">
                <img src="public/Tu.png" alt="Manager">
                <h3>Anh Tú</h3>
                <p>Trưởng phòng Sản phẩm</p>
            </div>
            <div class="team-item">
                <img src="public/tuan.png" alt="Designer">
                <h3>Anh Tuấn</h3>
                <p>Nhà thiết kế chính</p>
            </div>
            <div class="team-item">
                <img src="public/Dat.png" alt="Support">
                <h3>Thành Đạt</h3>
                <p>Trưởng nhóm CSKH</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Bạn đã sẵn sàng để bứt phá chưa?</h2>
            <p>Hãy để Athlete Hub đồng hành cùng bạn trên con đường chinh phục những giới hạn mới.</p>
            <a href="index.php" class="btn">MUA SẮM NGAY</a>
        </div>
    </section>

    <footer style="text-align: center; padding: 30px; color: var(--gray);">
        <p>&copy; 2026 Athlete Hub. Build with passion for Athletes.</p>
    </footer>

</body>

</html>