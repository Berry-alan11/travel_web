<?php
session_start();
$conn = new mysqli("localhost", "root", "", "travelworldweb");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {

    die("❌ Không tìm thấy người dùng với user_id = $user_id");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelWorld - Nền tảng du lịch cho người Việt</title>
    <!-- logo web -->
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">

    <!-- duong dan css -->
    <link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../assets/css/header.css">
	<link rel="stylesheet" href="../assets/css/footer.css">
	<link rel="stylesheet" href="../assets/css/color.css">
	<link rel="stylesheet" href="../assets/css/package.css">
	<link rel="stylesheet" href="../assets/css/popular.css">
	<link rel="stylesheet" href="../assets/css/booking.css">

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
                    <img src="../assets/images/logo.svg" alt="TravelWorld logo">
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
                            <a href="../index.php" class="navbar-link" data-nav-link>Trang chủ</a>
                        </li>

                        <li>
                            <a href="../about.php" class="navbar-link" data-nav-link>giới thiệu</a>
                        </li>

                        <li>
                            <a href="../destination.php" class="navbar-link" data-nav-link>địa điểm</a>
                        </li>

                        <li>
                            <a href="../tour.php" class="navbar-link" data-nav-link>tour</a>
                        </li>

                        <li>
                            <a href="../contact.php" class="navbar-link" data-nav-link>Liên hệ</a>
                        </li>

                    </ul>

                </nav>

                <a href="../book.php">
                    <button class="btn btn-primary">Đặt ngay</button>
                </a>

            </div>
        </div>

    </header>

  <!-- PROFILE -->
  <div class="container" style="max-width: 600px; margin: 120px auto 50px auto; background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 20px;">Thông tin tài khoản</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        <?= $_SESSION['success_message']; ?>
      </div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form action="update-profile.php" method="post" class="info-section">
      <label>Họ và tên:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
      </label><br><br>

      <label>Email:<br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </label><br><br>

      <label>Số điện thoại:<br>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
      </label><br><br>
	  
         <label>Căn Cước Công Dân (ID Card):<br>
           <input type="text" name="id_card" value="<?= htmlspecialchars($user['id_card']) ?>" required>
         </label><br><br>

             <label>Mật khẩu cũ:<br>
               <input type="password" name="old_password" required placeholder="Mật khẩu cũ">
            </label><br><br>

      <label>Mật khẩu mới (nếu muốn đổi):<br>
        <input type="password" name="password" placeholder="********">
      </label><br><br>

      <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
    </form>
  </div>

</body>
</html>
