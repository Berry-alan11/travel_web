<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelWorld - Nền tảng du lịch cho người Việt</title>
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="./assets/css/style.css">
	<link rel="stylesheet" href="./assets/css/header.css">
	<link rel="stylesheet" href="./assets/css/footer.css">
	<link rel="stylesheet" href="./assets/css/color.css">
	<link rel="stylesheet" href="./assets/css/package.css">
	<link rel="stylesheet" href="./assets/css/popular.css">
	<link rel="stylesheet" href="./assets/css/booking.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</head>

<body id="top">
    <header class="header" data-header>

        <div class="overlay" data-overlay></div>
        <div class="header-bottom">
            <div class="container">

                <ul class="social-list">

                    <li>
                        <a href="#" class="social-link">
                            <ion-icon name="logo-facebook"></ion-icon>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="social-link">
                            <ion-icon name="logo-twitter"></ion-icon>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="social-link">
                            <ion-icon name="logo-youtube"></ion-icon>
                        </a>
                    </li>

                </ul>

                <nav class="navbar" data-navbar>

                    <div class="navbar-top">

                        <a href="#" class="logo">
                            <img src="./assets/images/logo-blue.svg" alt="TravelWorld logo">
                        </a>

                        <button class="nav-close-btn" aria-label="Close Menu" data-nav-close-btn>
                            <ion-icon name="close-outline"></ion-icon>
                        </button>

                    </div>

                    <ul class="navbar-list">

                        <li>
                            <a href="index.php" class="navbar-link" data-nav-link>Trang chủ</a>
                        </li>

                        <li>
                            <a href="about.php" class="navbar-link" data-nav-link>giới thiệu</a>
                        </li>

                        <li>
                            <a href="destination.php" class="navbar-link" data-nav-link>địa điểm</a>
                        </li>

                        <li>
                            <a href="tour.php" class="navbar-link" data-nav-link>tour</a>
                        </li>

                        <li>
                            <a href="contact.php" class="navbar-link" data-nav-link>Liên hệ</a>
                        </li>

                    </ul>

                </nav>

                <a href="book.php" style="position: relative; z-index: 1;">
                    <button class="btn btn-primary">Đặt ngay</button>
                </a>


            </div>
        <div class="header-top">
            <div class="container">

                <a href="tel:+84889013678" class="helpline-box">

                    <div class="icon-box">
                        <ion-icon name="call-outline"></ion-icon>
                    </div>

                    <div class="wrapper">
                        <p class="helpline-title">Nếu có thắc mắc, vui lòng liên hệ :</p>

                        <p class="helpline-number">+84889013678</p>
                    </div>

                </a>

                <a href="#" class="logo">
                    <img src="./assets/images/logo.svg" alt="TravelWorld logo">
                </a>

                <div class="header-btn-group" style="z-index: 2;">
                    <?php if (isset($_SESSION['name'])): ?>
                        <div id="greeting" style="color: white; margin-right: 15px; cursor: pointer;">
                            Xin chào, <?= htmlspecialchars($_SESSION['name']) ?>
                        </div>
                        <ul id="user-dropdown"
                            style="display: none;
                                position: absolute;
                                right: 0;
                                top: 100%;
                                background-color: rgba(255, 255, 255, 0.4); /* nền trắng mờ nhẹ */
                                backdrop-filter: blur(10px);                /* hiệu ứng blur */
                                color: white;                               /* chữ trắng */
                                list-style: none;
                                margin: 0;
                                padding: 10px;
                                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                                border-radius: 8px;
                                min-width: 180px;
                                z-index: 9999;">
                            
							<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0): ?>
								<li><a href="admin/index.php" style="display: block; padding: 8px 12px; text-decoration: none; color: white;">Trang Admin</a></li>
							<?php endif; ?>

							<li><a href="order_history.php" style="display: block; padding: 8px 12px; text-decoration: none; color: white;">Lịch sử đặt hàng</a></li>
							
							<li><a href="profile/profile.php" style="display: block; padding: 8px 12px; text-decoration: none; color: white;">Thông tin cá nhân</a></li>
							<li><a href="php/logout.php" style="display: block; padding: 8px 12px; text-decoration: none; color: white;">Đăng xuất</a></li>
						</ul>
                        <script>
                            const greeting = document.getElementById("greeting");
                            const dropdown = document.getElementById("user-dropdown");
                            greeting.addEventListener("click", function (e) {
                                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                            });
                            document.addEventListener("click", function (e) {
                                if (!greeting.contains(e.target) && !dropdown.contains(e.target)) {
                                    dropdown.style.display = "none";
                                }
                            });
                        </script>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary">Đăng ký</a>
                        <a href="login.php" class="btn btn-secondary" style="margin-right: 10px;">Đăng nhập</a>
                    <?php endif; ?>
                    </div>


            </div>
        </div>
        </div>

    </header>

    <main>
        <!-- hero -->
        <section class="hero" style="background-image: url('./assets/images/about-top.webp'); background-size: cover; background-position: top; height: 500px; display: flex; align-items: center; justify-content: center; color: white;">
            <div class="hero-content" style="text-align: center;">
                <h1 class="h1 hero-title">Khám Phá Những Chuyến Đi Tuyệt Vời Cùng TravelWorld</h1>
                <p class="hero-text">Chúng tôi giúp bạn tìm thấy những chuyến du lịch lý tưởng cho những trải nghiệm đáng nhớ!</p>
            </div>
        </section>

        <section class="about">
            <div class="container">
                <h1 class="h2 section-title" style="margin-top: 50px;">Giới Thiệu Về TravelWorld</h1>

                <!-- doan chu va hinh 1 -->
                <div style="display: flex; align-items: center; margin-top: 40px;">
                    <img src="assets/images/about1.jpeg" alt="TravelWorld Image 1" style="width: 600px; margin-right: 20px; border-radius: var(--radius-15);">
                    <p class="section-text">TravelWorld là nền tảng du lịch hàng đầu Việt Nam, cho phép người dùng khám phá, đặt phòng và tận hưởng một loạt các sản phẩm du lịch đa dạng. Nền tảng này cung cấp các lựa chọn vận chuyển, bao gồm máy bay, xe buýt, tàu hỏa, cho thuê xe ô tô và đưa đón sân bay. Các lựa chọn chỗ ở của TravelWorld cũng rất đa dạng, bao gồm khách sạn, căn hộ, nhà nghỉ, homestay, khu nghỉ dưỡng và biệt thự. Ngoài ra, nền tảng còn nâng cao trải nghiệm du lịch bằng cách cung cấp các gói du lịch tàu biển và truy cập vào các điểm tham quan địa phương khác nhau như công viên giải trí, bảo tàng, tour du lịch trong ngày và hơn thế nữa.</p>
                </div>

                <!-- doan chu va hinh 2 -->
                <div style="display: flex; align-items: center; margin-top: 80px;">
                    <p class="section-text">Được thành lập tại Việt Nam vào năm 2030, TravelWorld đã trở thành nền tảng du lịch uy tín, phục vụ nhu cầu du lịch và đặt nơi lưu trú. TravelWorld cam kết cung cấp dịch vụ khách hàng xuất sắc với hỗ trợ 24/7 bằng tiếng Việt và tiếng Anh, chấp nhận nhiều phương thức thanh toán phổ biến tại Việt Nam cũng như thanh toán quốc tế cho khách du lịch ngoài nước. Với hàng triệu lượt tải ứng dụng và số lượng người dùng ngày càng tăng, TravelWorld là một trong những ứng dụng du lịch phổ biến nhất tại Việt Nam. Để biết thêm thông tin, vui lòng truy cập TravelWorld.</p>
                    <img src="assets/images/about2.jpg" alt="TravelWorld Image 2" style="width: 600px; margin-left: 20px; border-radius: var(--radius-15);">
                </div>
                <h2 class="h2 section-title" style="margin-top: 50px; margin-bottom: 20px;">Sản Phẩm</h2>
                <div style="display: flex; justify-content: space-between; gap: 20px; margin-top: 30px; margin-bottom: 30px; align-items: stretch;">
                    <!-- box1 -->
                    <div style="text-align: center; flex: 1;">
                        <div style="background: var(--bright-navy-blue); padding: 20px; border-radius: var(--radius-15); color: var(--white);">
                            <ion-icon name="airplane-outline" style="font-size: 50px; display: block; margin: 0 auto; margin-bottom: 10px;"></ion-icon>
                            <h3>Vé máy bay</h3>
                            <p style="font-size: 2.5rem;">200+</p>
                            <p>Hãng bay hạng sang & Hãng bay giá rẻ</p>
                        </div>
                    </div>

                    <!-- box2 -->
                    <div style="text-align: center; flex: 1;">
                        <div style="background: var(--bright-navy-blue); padding: 20px; border-radius: var(--radius-15); color: var(--white);">
                            <ion-icon name="home-outline" style="font-size: 50px; display: block; margin: 0 auto; margin-bottom: 10px;"></ion-icon>
                            <h3>Lưu trú</h3>
                            <p style="font-size: 2.5rem;">5K+</p>
                            <p>Khách sạn, căn hộ, resort & villa</p>
                        </div>
                    </div>

                    <!-- box3 -->
                    <div style="text-align: center; flex: 1;">
                        <div style="background: var(--bright-navy-blue); padding: 20px; border-radius: var(--radius-15); color: var(--white);">
                            <ion-icon name="ticket-outline" style="font-size: 50px; display: block; margin: 0 auto; margin-bottom: 10px;"></ion-icon>
                            <h3>Vé vui chơi</h3>
                            <p style="font-size: 2.5rem;">1K+</p>
                            <p>Hoạt động vui chơi giải trí ở khắp nơi trên thế giới</p>
                        </div>
                    </div>

                    <!-- box4 -->
                    <div style="text-align: center; flex: 1;">
                        <div style="background: var(--bright-navy-blue); padding: 20px; border-radius: var(--radius-15); color: var(--white);">
                            <div style="display: flex; justify-content: center; gap: 20px;">
                                <ion-icon name="car-outline" style="font-size: 50px; margin-bottom: 10px;"></ion-icon>
                                <ion-icon name="paper-plane-outline" style="font-size: 50px; margin-bottom: 10px;"></ion-icon>
                            </div>
                            <h3>Thuê xe & Đưa đón đến sân bay</h3>
                            <p style="font-size: 2.5rem;">200+</p>
                            <p>Nhà cung cấp dịch vụ</p>
                        </div>
                    </div>
                </div>
            </div>
</section>


    </main>


    <!--
      - #FOOTER
    -->

    <footer class="footer">

        <div class="footer-top">
            <div class="container">

                <div class="footer-brand">

                    <a href="#" class="logo">
                        <img src="./assets/images/logo.svg" alt="TravelWorld logo">
                    </a>

                    <p class="footer-text">
                        Web du lịch được thực hiện bởi Nhóm 6
                    </p>

                </div>

                <div class="footer-contact">

                    <h4 class="contact-title">Liên hệ chúng tôi</h4>

                    <p class="contact-text">
                        Chúng tôi luôn sẵn sàng hỗ trợ, dù bạn ở bất cứ nơi đâu!
                    </p>

                    <ul>

                        <li class="contact-item">
                            <ion-icon name="call-outline"></ion-icon>

                            <a href="tel:+84889013678" class="contact-link">+84889013678</a>
                        </li>

                        <li class="contact-item">
                            <ion-icon name="mail-outline"></ion-icon>

                            <a href="mailto:haidangphan052@gmail.com" class="contact-link">haidangphan052@gmail.com</a>
                        </li>

                        <li class="contact-item">
                            <ion-icon name="location-outline"></ion-icon>

                            <address>170 An Dương Vương, Tp. Quy Nhơn</address>
                        </li>

                    </ul>

                </div>

                <div class="footer-form">

                    <p class="form-text">
                        Đăng ký để nhận thêm thông tin
                    </p>

                    <form action="" class="form-wrapper">
                        <input type="email" name="email" class="input-field" placeholder="Nhập email của bạn" required>

                        <button type="submit" class="btn btn-secondary">đăng ký</button>
                    </form>

                </div>

            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">

                <p class="copyright">
                    &copy; 2025 <a href="">Nhóm 6</a>
                </p>

                <ul class="footer-bottom-list">

                    <li>
                        <a href="#" class="footer-bottom-link">Chính sách</a>
                    </li>

                    <li>
                        <a href="#" class="footer-bottom-link">Điều khoản & Điều kiện</a>
                    </li>

                    <li>
                        <a href="#" class="footer-bottom-link">Câu hỏi thường gặp</a>
                    </li>

                </ul>

            </div>
        </div>

    </footer>
</body>

</html>
