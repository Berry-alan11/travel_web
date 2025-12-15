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
    <!-- #HEADER -->

    <header class="header" data-header>

        <div class="overlay" data-overlay></div>

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

                <a href="index.php" class="logo">
                    <img src="./assets/images/logo.svg" alt="TravelWorld logo">
                </a>

                <div class="header-btn-group">

                    <button class="nav-open-btn" aria-label="Open Menu" data-nav-open-btn>
                        <ion-icon name="menu-outline"></ion-icon>
                    </button>

                </div>

            </div>
        </div>

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
                            <a href="#destination" class="navbar-link" data-nav-link>địa điểm</a>
                        </li>

                        <li>
                            <a href="tour.php" class="navbar-link" data-nav-link>tour</a>
                        </li>

                    </ul>

                </nav>

                <a href="book.php">
                    <button class="btn btn-primary">Đặt ngay</button>
                </a>

            </div>
        </div>
    </header>

    <main>
        <!-- hero -->
        <section class="hero-small" style="background-image: url('./assets/images/contact-hero.png'); background-size: cover; background-position: top; display: flex; align-items: center; justify-content: center; color: white;">
            <div class="hero-content" style="text-align: center;">
                <h1 class="h1 hero-small-title" style="font-size: 40px;">Liên Hệ Với Chúng Tôi</h1>
            </div>
        </section>


        <body>
            <div class="container" style="margin-top: 190px;">
                <div class="form-container">
                    <form id="contactForm" class="footer-form">
                        <input type="text" name="name" placeholder="Họ và tên" class="input-field" required>

                        <input type="email" name="email" placeholder="Email" class="input-field" style="margin-top: 10px;" required>

                        <textarea id="message" name="message" placeholder="Nội dung tin nhắn" class="input-field" rows="10" style="width: 100%; border-radius: var(--radius-25); padding: 10px; margin-top: 10px;" required></textarea>

                        <button type="submit" class="btn btn-primary">Gửi Tin Nhắn</button>
                    </form>
                </div>
            </div>

                <script>
                    document.getElementById("contactForm").addEventListener("submit", function (event) {
                        event.preventDefault();
                        alert("Tin nhắn của bạn đã được gửi!");
                    });
                </script>
</main>
</body>
</html>