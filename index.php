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
                            <a href="#destination" class="navbar-link" data-nav-link>địa điểm</a>
                        </li>

                        <li>
                            <a href="#package" class="navbar-link" data-nav-link>tour</a>
                        </li>

                        <li>
                            <a href="#gallery" class="navbar-link" data-nav-link>Bộ sưu tập ảnh</a>
                        </li> 

                        <li>
                            <a href="#contact" class="navbar-link" data-nav-link>Liên hệ</a>
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
                            
							<?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 0 || $_SESSION['user_role'] == 2)): ?>
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
        <article>

            <section class="hero-video" id="home">
                <div class="background-container">
                    <video autoplay muted loop class="background-video" style="filter: blur(0px);">
                        <source src="./assets/videos/hero-banner.mp4" type="video/mp4">
                    </video>
                </div>

                <div class="container">
                    <h2 class="h1 hero-title">Hành trình khám phá thế giới</h2>

                    <p class="hero-text">
                        Chúng tôi cung cấp những tour du lịch độc đáo và trải nghiệm tuyệt vời khi đến với những địa điểm đẹp trên thế giới
                    </p>

                    <div class="btn-group">
                        <a href="about.php">
                            <button class="btn btn-primary">Tìm hiểu thêm</button>
                        </a>
                        <a href="book.php">
                            <button class="btn btn-secondary">Đặt ngay</button>
                        </a>
                    </div>
                </div>
            </section>





            <section class="tour-search">
	    <div class="container">
	        <form method="post" class="tour-search-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
	            <div class="input-wrapper" style="flex: 1;">
	                <label for="keyword" class="input-label">Tìm kiếm địa điểm*</label>
	                <input type="text" name="keyword" id="keyword"
	                       placeholder="Nhập từ khóa tìm kiếm"
	                       class="input-field"
	                       style="width: 100%; padding: 10px;"
	                       value="<?php echo isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : ''; ?>">
	            </div>

	            <div class="button-wrapper" style="display: flex; align-items: flex-end;">
	                <button type="submit" name="search" class="btn btn-secondary" style="padding: 10px 20px;">
	                    Tìm kiếm
	                </button>
	            </div>
	        </form>
	    </div>
	</section>

            <?php
            // Xử lý tìm kiếm
            if(isset($_POST['search'])) {
                $keyword = trim($_POST['keyword']);
    
                if(!empty($keyword)) {
                    include "connect.php";
        
                    // Tìm kiếm cả trong tours và destinations
                    $sql = "SELECT tour_id as id, name, description, image_url, 'tour' as type FROM tours 
                WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%' OR location LIKE '%$keyword%'
                UNION 
                SELECT destination_id as id, name, description, image_url, 'destination' as type FROM destinations 
                WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%'";
            $result = mysqli_query($conn, $sql);
        
            if(mysqli_num_rows($result) > 0) {
            echo '<div class="container search-results">';
            echo '<h3>Kết quả tìm kiếm: "'.htmlspecialchars($keyword).'"</h3>';
            echo '<div class="results-grid">';
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="result-card">';
                            echo '<figure class="card-img">';
                            echo '<img src="'.htmlspecialchars($row['image_url']).'" alt="'.htmlspecialchars($row['name']).'" loading="lazy">';
                            echo '</figure>';
                            echo '<div class="card-content">';
                            echo '<h4>'.htmlspecialchars($row['name']).'</h4>';
                            echo '<p>'.htmlspecialchars(substr($row['description'], 0, 100)).'...</p>';
                            if($row['type'] == 'tour'){
                                echo '<a href="tour.php?id ='.$row['type'].'.php?id='.$row['id'].'" class="btn btn-primary">Đặt tour ngay</a>';
                            } else{
                            echo '<a href="destination.php?id='.$row['type'].'.php?id='.$row['id'].'" class="btn btn-primary">Khám phá điểm đên</a>';
                            }
                            echo '</div></div>';
                        }
                        }
            
                        echo '</div></div>';
                    } else {
                        echo '<div class="container no-results"><p>Không tìm thấy kết quả nào phù hợp với "'.htmlspecialchars($keyword).'"</p></div>';
                    }
                    mysqli_close($conn);
            }
            ?>





            <section class="popular" id="destination">
                <div class="container">

                    <p class="section-subtitle">Khám phá</p>

                    <h2 class="h2 section-title">Địa Điểm Nổi Bật</h2>

                    <p class="section-text">
                        Khám phá những địa điểm nổi bật, nơi mỗi bước chân là một câu chuyện và mỗi góc nhìn là một kỷ niệm không thể quên
                    </p>

                <ul class="popular-list">
                  <?php
                    include 'connect.php';
                    $sql = "SELECT * FROM tours ORDER BY reviews DESC LIMIT 3";
                    $result = $conn->query($sql);

                    while ($row = $result->fetch_assoc()) {
                      $country = htmlspecialchars($row['location']);
                      $title = htmlspecialchars($row['name']);
                      $desc = htmlspecialchars($row['description']);
                      $img = htmlspecialchars($row['image_url']);
                      $rating = round($row['rating']);
                      echo '<li>
                              <div class="popular-card">
                                <figure class="card-img">
                                  <img src="' . $img . '" alt="' . $title . '" loading="lazy">
                                </figure>
                                <div class="card-content">
                                  <div class="card-rating">';
                                  for ($i = 0; $i < 5; $i++) {
                                    echo '<ion-icon name="' . ($i < $rating ? 'star' : 'star-outline') . '"></ion-icon>';
                                  }
                      echo     '</div>
                                  <p class="card-subtitle"><a href="#">' . $country . '</a></p>
                                  <h3 class="h3 card-title"><a href="#">' . $title . '</a></h3>
                                  <p class="card-text">' . $desc . '</p>
                                </div>
                              </div>
                            </li>';
                    }
                  ?>
                </ul>

                    <a href="destination.php">
                        <button class="btn btn-primary">Thêm địa điểm</button>
                    </a>

                </div>
            </section>





            <section class="package" id="package">
                <div class="container">

                    <p class="section-subtitle">Các tour phổ biến</p>

                    <h2 class="h2 section-title">Hãy thử các tour phổ biến của chúng tôi</h2>

                    <p class="section-text">
                        Chinh phục những địa danh tuyệt đẹp qua mỗi hành trình, mang đến trải nghiệm tuyệt vời cho mọi du khách
                    </p>

                        <ul class="package-list">
                          <?php
                            include 'connect.php';
                            $sql = "SELECT * FROM tours LIMIT 3";
                            $result = $conn->query($sql);

                            while ($row = $result->fetch_assoc()) {
                              $title = htmlspecialchars($row['name']);
                              $desc = htmlspecialchars($row['description']);
                              $duration = htmlspecialchars($row['duration']);
                              $pax = (int)$row['pax'];
                              $location = htmlspecialchars($row['location']);
                              $img = htmlspecialchars($row['image_url']);
                              $price = number_format($row['price'], 0, ',', '.') . ' vnđ';
                              $reviews = (int)$row['reviews'];
                              $rating = round($row['rating']);
      
                              echo '<li>
                                <div class="package-card">
                                  <figure class="card-banner">
                                    <img src="' . $img . '" alt="' . $title . '" loading="lazy">
                                  </figure>
                                  <div class="card-content">
                                    <h3 class="h3 card-title">' . $title . '</h3>
                                    <p class="card-text">' . $desc . '</p>
                                    <ul class="card-meta-list">
                                      <li class="card-meta-item">
                                        <div class="meta-box"><ion-icon name="time"></ion-icon><p class="text">' . $duration . '</p></div>
                                      </li>
                                      <li class="card-meta-item">
                                        <div class="meta-box"><ion-icon name="people"></ion-icon><p class="text">pax: ' . $pax . '</p></div>
                                      </li>
                                      <li class="card-meta-item">
                                        <div class="meta-box"><ion-icon name="location"></ion-icon><p class="text">' . $location . '</p></div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="card-price">
                                    <div class="wrapper">
                                      <p class="reviews">(' . $reviews . ' reviews)</p>
                                      <div class="card-rating">';
                                        for ($i = 0; $i < 5; $i++) {
                                          echo '<ion-icon name="' . ($i < $rating ? 'star' : 'star-outline') . '"></ion-icon>';
                                        }
                              echo   '</div></div>
                                    <p class="price">' . $price . '<span>/ 1 người</span></p>
                                    <button class="btn btn-secondary">Đặt Ngay</button>
                                  </div>
                                </div>
                              </li>';
                            }
                          ?>
                        </ul>

                    <a href="tour.php">
                        <button class="btn btn-primary">Tất cả các tour</button>
                    </a>

                </div>
            </section>





            <section class="gallery" id="gallery">
                <div class="container">

                    <p class="section-subtitle">Bộ sưu tập</p>

                    <h2 class="h2 section-title">Những bức ảnh của khách du lịch</h2>

                    <p class="section-text">
                        Những bức ảnh không chỉ ghi lại khoảnh khắc đẹp, mà còn là những câu chuyện sống động về hành trình khám phá và những trải nghiệm khó quên
                    </p>

                    <ul class="gallery-list">

                        <li class="gallery-item">
                            <figure class="gallery-image">
                                <img src="./assets/images/gallery-1.jpg" alt="Gallery image">
                            </figure>
                        </li>

                        <li class="gallery-item">
                            <figure class="gallery-image">
                                <img src="./assets/images/gallery-2.jpg" alt="Gallery image">
                            </figure>
                        </li>

                        <li class="gallery-item">
                            <figure class="gallery-image">
                                <img src="./assets/images/gallery-3.jpg" alt="Gallery image">
                            </figure>
                        </li>

                        <li class="gallery-item">
                            <figure class="gallery-image">
                                <img src="./assets/images/gallery-4.jpg" alt="Gallery image">
                            </figure>
                        </li>

                        <li class="gallery-item">
                            <figure class="gallery-image">
                                <img src="./assets/images/gallery-5.jpg" alt="Gallery image">
                            </figure>
                        </li>

                    </ul>

                </div>
            </section>





            <section class="cta" id="contact">
                <div class="container">

                    <div class="cta-content">
                        <p class="section-subtitle">Liên hệ hỗ trợ</p>

                        <h2 class="h2 section-title">Sẵn sàng cho những chuyến du lịch khó quên. Hãy nhớ đến chúng tôi!</h2>

                        <p class="section-text">
                            Chuẩn bị cho những hành trình đầy kỷ niệm, nơi mỗi khoảnh khắc sẽ mãi in sâu trong trái tim bạn. Hãy để chúng tôi đồng hành cùng bạn trên mỗi bước đi
                        </p>
                    </div>

                    <a href="contact.php"
                    <button class="btn btn-secondary">Liên hệ ngay !</button>
                    </a>

                </div>
            </section>

        </article>
    </main>





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