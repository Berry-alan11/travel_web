<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelWorld - Nền tảng du lịch cho người Việt</title>
    <!-- logo web -->
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

    <!-- duong dan css -->
    <link rel="stylesheet" href="./assets/css/style.css">
	<link rel="stylesheet" href="./assets/css/header.css">
	<link rel="stylesheet" href="./assets/css/footer.css">
	<link rel="stylesheet" href="./assets/css/color.css">
	<link rel="stylesheet" href="./assets/css/package.css">
	<link rel="stylesheet" href="./assets/css/popular.css">
	<link rel="stylesheet" href="./assets/css/booking.css">

    <!-- phong chu -->
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
        <section class="hero-video" id="home">
            <div class="background-container">
                <video autoplay muted loop class="background-video" style="filter: blur(0px);">
                    <source src="./assets/videos/tour.mp4" type="video/mp4">
                </video>
            </div>
            <div class="hero-content" style="text-align: center;">
                <h1 class="h1 hero-title">Các tour phổ biến</h1>
                <p class="hero-text"> Mỗi chuyến đi là một lần sống trọn vẹn với vẻ đẹp của thế giới</p>

            </div>
        </section>
        <!-- cac tour du lich -->

        <section class="package" id="package">
            <div class="container">

                <p class="section-subtitle">Các tour phổ biến</p>

                <h2 class="h2 section-title">Tất Cả Các Tour của chúng tôi</h2>

                <p class="section-text">
                    Khám phá những vùng đất tuyệt mỹ qua từng chặng đường, mở ra hành trình đầy cảm xúc và trải nghiệm đáng nhớ cho mỗi du khách
                </p>

                <ul class="package-list">

                <?php
                //Kết nối database
                include 'connect.php';

                //Query danh sách tour
                $sql = "SELECT * FROM tours";
                $result = $conn->query($sql);

                //In ra từng tour
                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                    <li>
                        <div class="package-card">

                            <figure class="card-banner">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" loading="lazy">
                            </figure>

                            <div class="card-content">

                                <h3 class="h3 card-title"><?php echo htmlspecialchars($row['name']); ?></h3>

                                <p class="card-text">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>

                                <ul class="card-meta-list">

                                    <li class="card-meta-item">
                                        <div class="meta-box">
                                            <ion-icon name="time"></ion-icon>
                                            <p class="text"><?php echo htmlspecialchars($row['duration']); ?></p>
                                        </div>
                                    </li>

                                    <li class="card-meta-item">
                                        <div class="meta-box">
                                            <ion-icon name="people"></ion-icon>
                                            <p class="text">pax: <?php echo htmlspecialchars($row['pax']); ?></p>
                                        </div>
                                    </li>

                                    <li class="card-meta-item">
                                        <div class="meta-box">
                                            <ion-icon name="location"></ion-icon>
                                            <p class="text"><?php echo htmlspecialchars($row['location']); ?></p>
                                        </div>
                                    </li>

                                </ul>

                            </div>

                            <div class="card-price">

                                <div class="wrapper">

                                    <p class="reviews">(<?php echo htmlspecialchars($row['reviews']); ?> reviews)</p>

                                    <div class="card-rating">
                                        <?php
                                        $fullStars = floor($row['rating']);
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<ion-icon name="star"></ion-icon>';
                                        }
                                        ?>
                                    </div>

                                </div>

                                <p class="price">
                                    <?php echo number_format($row['price'], 0, ',', '.'); ?> vnđ
                                    <span>/ 1 người</span>
                                </p>

                                <button class="btn btn-secondary">Đặt Ngay</button>

                            </div>

                        </div>
                    </li>
                <?php
                    endwhile;
                else:
                    echo "Hiện chưa có tour nào.";
                endif;
                ?>
                </ul>

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
