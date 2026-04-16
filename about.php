<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi - Athlete Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #e63946; /* Màu đỏ thể thao */
            --dark-color: #1d3557;
            --light-color: #f1faee;
            --text-color: #333;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 0 20px;
        }

        /* Header Section */
        .about-header {
            background: linear-gradient(rgba(29, 53, 87, 0.8), rgba(29, 53, 87, 0.8)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=1470') no-repeat center center/cover;
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
        }

        .about-header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Section chung */
        section {
            padding: 60px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            width: 50px;
            height: 4px;
            background: var(--primary-color);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Câu chuyện thương hiệu */
        .about-story {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .about-story img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        /* Giá trị cốt lõi */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .value-item {
            padding: 30px;
            background: var(--light-color);
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .value-item:hover {
            transform: translateY(-10px);
            background: var(--primary-color);
            color: #fff;
        }

        .value-item i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .value-item:hover i {
            color: #fff;
        }

        /* Thống kê số liệu */
        .stats {
            background: var(--dark-color);
            color: #fff;
            display: flex;
            justify-content: space-around;
            padding: 50px 0;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2.5rem;
            margin: 0;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .about-story {
                grid-template-columns: 1fr;
            }
            .about-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <header class="about-header">
        <div class="container">
            <h1>Về Chúng Tôi</h1>
            <p>Nơi niềm đam mê hội tụ, nơi giá trị bắt đầu.</p>
        </div>
    </header>

    <section class="container">
        <div class="about-story">
            <div>
                <h2>Hành trình của Athlete Hub</h2>
                <p>Ra đời từ năm 2024, <strong>Athlete Hub</strong> bắt đầu bằng khát vọng mang đến những thiết bị và trang phục thể thao chất lượng nhất cho cộng đồng yêu vận động.</p>
                <p>Chúng tôi hiểu rằng khách hàng không chỉ tìm kiếm một sản phẩm, mà họ tìm kiếm một giải pháp, một sự đồng hành và một niềm tin. Đó là lý do chúng tôi luôn không ngừng chuyển mình mỗi ngày.</p>
            </div>
            <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=1470" alt="Gym">
        </div>
    </section>

    <section style="background-color: #f9f9f9;">
        <div class="container">
            <div class="section-title">
                <h2>Giá Trị Cốt Lõi</h2>
            </div>
            <div class="values-grid">
                <div class="value-item">
                    <i class="fas fa-medal"></i>
                    <h3>Chất Lượng</h3>
                    <p>Mỗi sản phẩm đều được kiểm duyệt khắt khe trước khi đến tay bạn.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Sáng Tạo</h3>
                    <p>Luôn cập nhật công nghệ và xu hướng thể thao mới nhất.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-heart"></i>
                    <h3>Tận Tâm</h3>
                    <p>Sự hài lòng của khách hàng là thước đo thành công duy nhất.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Minh Bạch</h3>
                    <p>Mọi quy trình và cam kết luôn rõ ràng, trung thực.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <h3>10k+</h3>
            <p>Khách hàng tin dùng</p>
        </div>
        <div class="stat-item">
            <h3>98%</h3>
            <p>Phản hồi tích cực</p>
        </div>
        <div class="stat-item">
            <h3>5+</h3>
            <p>Năm kinh nghiệm</p>
        </div>
    </section>

    <footer style="text-align: center; padding: 40px 0; background: #eee;">
        <p>&copy; 2026 Athlete Hub. All rights reserved.</p>
    </footer>

</body>
</html>