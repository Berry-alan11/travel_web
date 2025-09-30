<?php
session_start();

// --- BẢO MẬT ---
// THAY ĐỔI 1: Cập nhật điều kiện kiểm tra vai trò
// Kiểm tra xem người dùng đã đăng nhập và có phải là admin (0) hoặc nhà cung cấp dịch vụ (2) không
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], [0, 2])) {
    // Nếu không phải, chuyển hướng về trang chủ
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}

// --- KẾT NỐI DATABASE VÀ TRUY VẤN DỮ LIỆU THỐNG KÊ ---
// Giả định tệp connect.php nằm ở thư mục gốc
require_once '../connect.php';

// 1. Thống kê tổng doanh thu (chỉ tính các đơn đã thanh toán xong)
$result_revenue = $conn->query("SELECT SUM(total_price) as total_revenue FROM bookings");
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];
if (is_null($total_revenue)) {
    $total_revenue = 0;
}

// 2. Thống kê tổng số đơn đặt tour
$result_bookings = $conn->query("SELECT COUNT(*) as total_bookings FROM bookings");
$total_bookings = $result_bookings->fetch_assoc()['total_bookings'];

// 3. Thống kê tổng số người dùng
$result_users = $conn->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $result_users->fetch_assoc()['total_users'];

// 4. Thống kê tổng số tour
$result_tours = $conn->query("SELECT COUNT(*) as total_tours FROM tours");
$total_tours = $result_tours->fetch_assoc()['total_tours'];

// 5. Lấy 5 đơn đặt tour gần đây nhất
$recent_bookings_sql = "
    SELECT b.booking_id, u.name as customer_name, t.name as tour_name, b.checkin, b.total_price
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.booking_id DESC
    LIMIT 5
";
$recent_bookings_result = $conn->query($recent_bookings_sql);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TravelWorld</title>
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="stylesheet" href="admin_style.css">

</head>
<body>
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php" class="active"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
            <li><a href="manage_tours.php"><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
            <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
            <li><a href="manage_destinations.php"><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>
            
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0): ?>
                <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
            <?php endif; ?>

            <li><a href="manage_contacts.php"><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Trang Tổng Quan</h1>
            <div>
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="icon revenue">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
                <div class="info">
                    <h3>Tổng Doanh Thu</h3>
                    <p><?= number_format($total_revenue, 0, ',', '.') ?> vnđ</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon bookings">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="info">
                    <h3>Tổng Đơn Tour</h3>
                    <p><?= $total_bookings ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon users">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <div class="info">
                    <h3>Tổng Người Dùng</h3>
                    <p><?= $total_users ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon tours">
                    <i class="fa-solid fa-umbrella-beach"></i>
                </div>
                <div class="info">
                    <h3>Tổng Số Tour</h3>
                    <p><?= $total_tours ?></p>
                </div>
            </div>
        </section>

        <section class="recent-bookings">
            <h2>Các đơn đặt tour gần đây</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Tên Khách Hàng</th>
                        <th>Tên Tour</th>
                        <th>Ngày Đi</th>
                        <th>Tổng Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($recent_bookings_result && $recent_bookings_result->num_rows > 0) {
                            while($row = $recent_bookings_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>#" . htmlspecialchars($row['booking_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
                                echo "<td>" . date("d/m/Y", strtotime($row['checkin'])) . "</td>";
                                echo "<td>" . number_format($row['total_price'], 0, ',', '.') . " vnđ</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>Chưa có đơn đặt tour nào.</td></tr>";
                        }
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>