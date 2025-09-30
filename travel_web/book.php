<?php
session_start();
$conn = new mysqli("localhost", "root", "", "travelworldweb");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if the user is not logged in
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $user_result->fetch_assoc();

// Fetch available tours
$tour_result = $conn->query("SELECT tour_id, name, price, duration FROM tours");
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
		<section>
		  <div class="booking-container" style="margin-top: 190px;">
			<h2>Đặt Vé Du Lịch</h2>
			<form id="booking-form" action="book-process.php" method="POST">
                    <!-- Pre-fill user data if available -->
                    <label for="name">Họ và Tên:</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>

                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" required>

                     <label for="id_card">Căn cước công dân (CCCD):</label>
                     <input type="text" id="id_card" name="id_card" value="<?php echo isset($user['id_card']) ? htmlspecialchars($user['id_card']) : ''; ?>" required>

                     <label for="deposit">Số tiền đặt cọc:</label>
                     <input type="number" id="deposit" name="deposit" placeholder="Nhập số tiền đặt cọc" min="1000000" step="1000" required>


			  <label for="people">Số lượng người:</label>
			  <input type="number" id="people" name="people" min="1" required>

			  <label for="tour_id">Chọn tour:</label>
	<select name="tour_id" id="tour_id" required>
	  <?php while ($row = $tour_result->fetch_assoc()): ?>
		<option 
		  value="<?php echo $row['tour_id']; ?>" 
		  data-duration="<?php echo htmlspecialchars($row['duration']); ?>">
		  <?php echo htmlspecialchars($row['name']); ?> - 
		  <?php echo number_format($row['price']); ?>đ - 
		  <?php echo htmlspecialchars($row['duration']); ?>
		</option>
	  <?php endwhile; ?>
	</select>


			  <fieldset>
				<legend>Dịch vụ đi kèm:</legend>
				<div style="display: flex; gap: 15px; align-items: center;">
				  <label><input type="checkbox" name="services[]" value="insurance"> Bảo hiểm</label>
				  <label><input type="checkbox" name="services[]" value="transport"> Xe đưa đón</label>
				  <label><input type="checkbox" name="services[]" value="meal"> Ăn uống</label>
				</div>
			  </fieldset>

			  <label>Chọn loại vé:</label>
			  <select name="ticket_type">
				<option value="maybay">Vé máy bay</option>
				<option value="xekhach">Vé xe khách</option>
				<option value="tauhoa">Vé tàu hỏa</option>
				<option value="tour">Tour du lịch</option>
			  </select>

			  <label>Ngày khởi hành:</label>
			  <input type="date" name="checkin" required>
			  <label for="discount-code">Mã giảm giá:</label>
			  <input type="text" id="discount-code" name="discount_code">

			  <label for="payment-method">Phương thức thanh toán:</label>
			  <select id="payment-method" name="payment_method" required>
				<option value="bank">Chuyển khoản</option>
				<option value="e-wallet">Ví điện tử</option>
				<option value="credit-card">Thẻ tín dụng</option>
			  </select>

			  <label>Ghi chú:</label>
			  <textarea name="note" placeholder="Nhập ghi chú (nếu có)"></textarea>

			  <button type="submit">Đặt Vé Ngay</button>
			</form>
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
							<img src="./assets/images/logo.svg" alt="Tourly logo">
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