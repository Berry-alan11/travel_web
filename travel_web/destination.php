<?php
session_start();

// Kết nối CSDL
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelWorld - Điểm Đến</title>
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <!-- duong dan css -->
    <link rel="stylesheet" href="./assets/css/style.css">
	<link rel="stylesheet" href="./assets/css/header.css">
	<link rel="stylesheet" href="./assets/css/footer.css">
	<link rel="stylesheet" href="./assets/css/color.css">
	<link rel="stylesheet" href="./assets/css/package.css">
	<link rel="stylesheet" href="./assets/css/popular.css">
	<link rel="stylesheet" href="./assets/css/booking.css">
	
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <style>
    .destination-section {
      padding: 60px 15px;
    }
    .destination-title {
      text-align: center;
      margin-bottom: 40px;
      font-size: 28px;
      color: var(--bright-navy-blue);
      text-transform: uppercase;
    }
    .destination-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      padding: 0 20px;
    }
    .destination-card {
      position: relative;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .destination-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      display: block;
    }
    .destination-name {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 10px;
      background: rgba(0, 0, 0, 0.5);
      color: white;
      font-weight: bold;
      text-align: center;
    }
    </style>
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

<section class="hero-video" id="home">
    <div class="background-container">
        <video autoplay muted loop class="background-video">
            <source src="./assets/videos/tour.mp4" type="video/mp4">
        </video>
    </div>
    <div class="hero-content" style="text-align: center;">
        <h1 class="h1 hero-title">Các địa điểm của chúng tôi</h1>
        <p class="hero-text">Cẩm nang du lịch và thông tin các địa điểm</p>
    </div>
</section>

<section class="destination-section">
            <p class="section-subtitle">Khám phá</p>

            <h2 class="h2 section-title">Các địa điểm trong nước</h2>

            <p class="section-text">
                Từ núi non hùng vĩ phía Bắc đến miền Tây sông nước hữu tình, dải đất hình chữ S luôn ẩn chứa những điều kỳ diệu. Hãy cùng nhau khám phá vẻ đẹp Việt Nam!
            </p>
    <div class="destination-grid">
        <?php
        $sql_vn = "SELECT name, image_url, slug FROM destinations WHERE country = 'Việt Nam'";
        $result_vn = $conn->query($sql_vn);
        while ($row = $result_vn->fetch_assoc()):
        ?>
        <div class="destination-card">
            <a href="destination-detail.php?slug=<?= urlencode($row['slug']) ?>">
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="destination-name"><?= htmlspecialchars($row['name']) ?></div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<section class="destination-section">
            <p class="section-subtitle">Khám phá</p>

            <h2 class="h2 section-title">Các địa điểm ngoài nước</h2>

            <p class="section-text">
                Từ những thành phố không ngủ sôi động đến những kỳ quan thiên nhiên ngoạn mục, thế giới đang chờ bạn với vô vàn trải nghiệm đáng giá.
            </p>
    <div class="destination-grid">
        <?php
        $sql_world = "SELECT name, image_url, slug FROM destinations WHERE country != 'Việt Nam'";
        $result_world = $conn->query($sql_world);
        while ($row = $result_world->fetch_assoc()):
        ?>
        <div class="destination-card">
            <a href="destination-detail.php?slug=<?= urlencode($row['slug']) ?>">
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <div class="destination-name"><?= htmlspecialchars($row['name']) ?></div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

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

<?php $conn->close(); ?>
</body>
</html>
