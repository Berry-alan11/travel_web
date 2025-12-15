<?php
session_start();
require_once 'connect.php';

// Bảo mật: Bắt buộc người dùng phải đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=Vui lòng đăng nhập để xem lịch sử đặt hàng.');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy tất cả các đơn hàng của người dùng này, sắp xếp mới nhất lên trên
$stmt = $conn->prepare("
    SELECT b.booking_id, t.name AS tour_name, b.checkin, b.checkout, b.total_price, b.status
    FROM bookings b
    JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.user_id = ?
    ORDER BY b.booking_id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mảng định nghĩa trạng thái (giống trang admin)
$status_map = [
    0 => ['text' => 'Mới', 'color' => '#3498db'],
    1 => ['text' => 'Đã xác nhận', 'color' => '#27ae60'],
    2 => ['text' => 'Đã hoàn thành', 'color' => '#8e44ad'],
    3 => ['text' => 'Đã hủy', 'color' => '#e74c3c']
];

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt hàng - TravelWorld</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <style>
        .history-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .history-container h1 {
            color: #003b95;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th, .order-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        .order-table th {
            background-color: #f2f2f2;
        }
        .status-badge {
            padding: 5px 10px;
            color: white;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
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

    <main>
        <div class="history-container" style="margin-top: 190px; margin-bottom: 50px;">
            <h2 class="h2 section-title" style="font-size: 35px;">lỊCH SỬ ĐẶT HÀNG</h2>
            <?php if ($result->num_rows > 0): ?>
                <div style="overflow-x:auto;">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th class="h3 card-title" style="font-size: 20px;">mã đơn</th>
                                <th class="h3 card-title" style="font-size: 20px;">tên tour</th>
                                <th class="h3 card-title" style="font-size: 20px;">ngày đi</th>
                                <th class="h3 card-title" style="font-size: 20px;">ngày về</th>
                                <th class="h3 card-title" style="font-size: 20px;">tổng tiền</th>
                                <th class="h3 card-title" style="font-size: 20px;">trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): 
                                $status_info = $status_map[$row['status']] ?? ['text' => 'Không rõ', 'color' => '#7f8c8d'];
                            ?>
                                <tr>
                                    <td>#<?= $row['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($row['tour_name']) ?></td>
                                    <td><?= date("d/m/Y", strtotime($row['checkin'])) ?></td>
                                    <td><?= date("d/m/Y", strtotime($row['checkout'])) ?></td>
                                    <td><?= number_format($row['total_price'], 0, ',', '.') ?> vnđ</td>
                                    <td><span class="status-badge" style="background-color: <?= $status_info['color'] ?>;"><?= $status_info['text'] ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 40px 0;">Bạn chưa có đơn đặt hàng nào. Hãy bắt đầu <a href="tour.php" style="color: var(--bright-navy-blue); text-decoration: underline;">khám phá các tour</a> của chúng tôi!</p>
            <?php endif; ?>
        </div>
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
                    <li><a href="#" class="footer-bottom-link">Chính sách</a></li>
                    <li><a href="#" class="footer-bottom-link">Điều khoản & Điều kiện</a></li>
                    <li><a href="#" class="footer-bottom-link">Câu hỏi thường gặp</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>