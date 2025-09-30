<?php
session_start();

// Kết nối database
include 'connect.php';

// Lấy slug từ URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    echo "<h2>Không tìm thấy địa điểm.</h2>";
    exit;
}

// Truy vấn dữ liệu
$stmt = $conn->prepare("SELECT * FROM destinations WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$destination = $result->fetch_assoc();

if (!$destination) {
    echo "<h2>Địa điểm không tồn tại.</h2>";
    exit;
}

function render_images($data, $slider_id) {
    if (empty(trim($data))) return '';
    
    // Thêm một wrapper lớn nhất cho slider và các nút bấm
    $html = "<div class='slider-wrapper'>";
    
    // Container để cuộn ngang, thêm ID để JavaScript nhận diện
    $html .= "<div class='horizontal-scroll-container' id='slider-{$slider_id}'>";
    
    // Tách chuỗi bằng dấu ;;
    $blocks = explode(";;", trim($data));

    foreach ($blocks as $block) {
        $trimmed_block = trim($block);
        if (strpos($trimmed_block, '||') !== false) {
            list($caption, $image) = explode('||', $trimmed_block, 2);
            $caption = htmlspecialchars(trim($caption));
            $image_url = htmlspecialchars(trim($image));
            $image_path = (strpos($image_url, 'http') === 0 || strpos($image_url, 'https') === 0) ? $image_url : $image_url;

            if ($image_path) {
                $html .= "<div class='scroll-item'>
                            <img src='$image_path' alt='$caption' loading='lazy'>
                            <p class='caption'>$caption</p>
                          </div>";
            }
        }
    }
    $html .= '</div>'; // Đóng container cuộn

    // Thêm các nút mũi tên, với data-slider trỏ đến ID của container
    $html .= "<button class='slider-arrow prev-arrow' data-slider='slider-{$slider_id}'>&#10094;</button>";
    $html .= "<button class='slider-arrow next-arrow' data-slider='slider-{$slider_id}'>&#10095;</button>";

    $html .= '</div>'; // Đóng wrapper lớn
    return $html;
}
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
        .detail-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .detail-container img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 40px;
        }
        h1, h2 {
            color: #003b95;
        }
		.horizontal-scroll-container {
			display: flex;
			overflow-x: auto; /* Cho phép cuộn ngang */
			overflow-y: hidden; /* Ẩn thanh cuộn dọc */
			padding-bottom: 15px; /* Tạo không gian cho thanh cuộn */
			white-space: nowrap; /* Ngăn các item xuống dòng */
		}

		.scroll-item {
			flex: 0 0 80%; /* Mỗi item chiếm 80% chiều rộng của container */
			max-width: 450px; /* Giới hạn chiều rộng tối đa của item */
			margin-right: 20px;
			text-align: center;
		}

		.scroll-item img {
			width: 100%;
			border-radius: 8px;
			box-shadow: 0 4px 8px rgba(0,0,0,0.1);
			aspect-ratio: 16 / 10;
			object-fit: cover;
		}

		.scroll-item .caption {
			margin-top: 10px;
			font-style: italic;
			font-size: 14px;
			white-space: normal; /* Cho phép caption dài có thể xuống dòng */
		}
		.slider-wrapper {
			position: relative; /* Quan trọng để định vị các nút mũi tên */
		}

		.horizontal-scroll-container {
			scroll-behavior: smooth; /* Tạo hiệu ứng cuộn mượt mà */
			/* Ẩn thanh cuộn mặc định của trình duyệt */
			scrollbar-width: none; /* Firefox */
			-ms-overflow-style: none;  /* IE and Edge */
		}
		.horizontal-scroll-container::-webkit-scrollbar {
			display: none; /* Chrome, Safari and Opera */
		}

		.slider-arrow {
			position: absolute;
			top: 37%; /* Nằm ở giữa chiều cao của ảnh */
			transform: translateY(-10%);
			background-color: rgba(0, 0, 0, 0.4);
			color: white;
			border: none;
			cursor: pointer;
			padding: 8px 14px;
			border-radius: 50%;
			font-size: 18px;
			line-height: 1;
			z-index: 10;
			transition: background-color 0.3s;
		}

		.slider-arrow:hover {
			background-color: rgba(0, 0, 0, 0.7);
		}

		.prev-arrow {
			left: 15px;
		}

		.next-arrow {
			right: 15px;
		}
    </style>
    </head>

    <body id="top" style="background: url('./assets/images/login.jpg') no-repeat center center/cover;">
    <!-- #HEADER -->

   <header class="header" data-header style="background-color: var(--bright-navy-blue);">

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

                <a href="#" class="logo">
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

                <a href="book.php">
                    <button class="btn btn-primary">Đặt ngay</button>
                </a>

            </div>
        </div>

    </header>
<body>

<div class="detail-container" style="margin-top: 190px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <h1 style="margin: 0;"><?= htmlspecialchars($destination['name']) ?></h1>
        <?php if (!empty($destination['best_time_to_visit'])): ?>
            <span style="font-size: 16px; color: #555;">
                Thời gian tốt nhất để đi: <?= htmlspecialchars($destination['best_time_to_visit']) ?>
            </span>
        <?php endif; ?>
    </div>
    <p><strong>Quốc gia:</strong> <?= htmlspecialchars($destination['country']) ?></p>

    <?php if (!empty($destination['image_url'])): ?>
        <img src="<?= htmlspecialchars($destination['image_url']) ?>" alt="<?= htmlspecialchars($destination['name']) ?>">
    <?php endif; ?>

    <div class="section">
        <h2>Mô tả</h2>
        <p><?= nl2br(htmlspecialchars($destination['description'])) ?></p>
    </div>

    <div class="section">
        <h2>Điểm tham quan du lịch nổi tiếng</h2>
        <?= render_images($destination['section_1'] ?? '', 'section1') ?>
    </div>

    <div class="section">
        <h2>Các điểm đến nổi tiếng</h2>
        <<?= render_images($destination['section_2'] ?? '', 'section2') ?>
    </div>

    <div class="section">
        <h2>Bản đồ</h2>
        <div style="overflow:hidden; border-radius: 10px;">
            <?= $destination['map_embed'] ?>
        </div>
    </div>

    <div class="section">
        <h2>Thư viện ảnh</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
            <?php
            $gallery = array_filter(array_map('trim', explode(',', $destination['image_gallery'])));
            foreach ($gallery as $img) {
                echo "<img src='" . htmlspecialchars($img) . "' alt='Gallery Image'>";
            }
            ?>
        </div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tìm tất cả các slider có trên trang
    const sliders = document.querySelectorAll('.slider-wrapper');

    sliders.forEach(slider => {
        const scrollContainer = slider.querySelector('.horizontal-scroll-container');
        const prevBtn = slider.querySelector('.prev-arrow');
        const nextBtn = slider.querySelector('.next-arrow');
        
        if (!scrollContainer || !prevBtn || !nextBtn) return;

        let autoplayInterval;

        const slideNext = () => {
            const itemWidth = scrollContainer.querySelector('.scroll-item').offsetWidth + 20; // Lấy chiều rộng 1 item + khoảng cách margin
            // Nếu đã cuộn đến cuối, quay lại ảnh đầu tiên
            if (scrollContainer.scrollLeft + scrollContainer.clientWidth >= scrollContainer.scrollWidth - 1) {
                scrollContainer.scrollLeft = 0;
            } else {
                scrollContainer.scrollLeft += itemWidth;
            }
        };

        const slidePrev = () => {
            const itemWidth = scrollContainer.querySelector('.scroll-item').offsetWidth + 20;
            scrollContainer.scrollLeft -= itemWidth;
        };

        // Bắt đầu tự động cuộn
        const startAutoplay = () => {
            // Xóa interval cũ trước khi tạo cái mới để tránh bị chồng chéo
            clearInterval(autoplayInterval); 
            autoplayInterval = setInterval(slideNext, 2000); // 2000ms = 2 giây
        };

        // Gán sự kiện cho các nút
        nextBtn.addEventListener('click', () => {
            slideNext();
            startAutoplay(); // Reset lại bộ đếm thời gian khi người dùng tự bấm
        });

        prevBtn.addEventListener('click', () => {
            slidePrev();
            startAutoplay(); // Reset lại bộ đếm thời gian
        });

        // Tạm dừng khi di chuột vào và chạy lại khi di chuột ra
        slider.addEventListener('mouseenter', () => {
            clearInterval(autoplayInterval);
        });

        slider.addEventListener('mouseleave', () => {
            startAutoplay();
        });

        // Bắt đầu chạy lần đầu tiên
        startAutoplay();
    });
});
</script>

</body>
</html>

<?php $conn->close(); ?>
