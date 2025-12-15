<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}

require_once '../connect.php';

// Kiểm tra ID hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_bookings.php?error=ID không hợp lệ');
    exit();
}
$booking_id = (int)$_GET['id'];

// Xử lý khi admin nhấn nút "Cập nhật"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = (int)$_POST['status'];
    
    $update_stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $update_stmt->bind_param("ii", $status, $booking_id);
    if ($update_stmt->execute()) {
        $message = "<div class='message success'>Cập nhật trạng thái thành công!</div>";
    } else {
        $message = "<div class='message error'>Cập nhật thất bại.</div>";
    }
    $update_stmt->close();
}


// Lấy thông tin chi tiết của đơn hàng
$sql = "
    SELECT b.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone, t.name as tour_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    WHERE b.booking_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: manage_bookings.php?error=Không tìm thấy đơn hàng');
    exit();
}
$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Mảng định nghĩa trạng thái
$status_map = [
    0 => 'Mới',
    1 => 'Đã xác nhận',
    2 => 'Đã hoàn thành',
    3 => 'Đã hủy'
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $booking_id ?></title>
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    
    <style>
        .main-content { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; margin-left: 280px; }
        .booking-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .detail-group { margin-bottom: 15px; }
        .detail-group label { font-weight: 600; color: #555; display: block; margin-bottom: 5px; }
        .detail-group p { padding: 8px; background-color: #f4f7fc; border-radius: 4px; margin: 0; min-height: 38px; }
        .full-width { grid-column: 1 / -1; }
        .full-width p { white-space: pre-wrap; }
        .update-form { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .update-form select, .update-form button { padding: 10px; font-size: 1rem; border-radius: 5px; }
        .update-form button { background-color: var(--primary-color); color: white; border: none; cursor: pointer; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary-color); text-decoration: none; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2></div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
            <li><a href="manage_tours.php"><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
            <li><a href="manage_bookings.php" class="active"><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
            <li><a href="manage_destinations.php"><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
            <li><a href="manage_contacts.php"><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
        </ul>
        <div class="sidebar-footer"><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></div>
    </aside>

    <main class="main-content">
        <a href="manage_bookings.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
        <h1>Chi tiết đơn hàng #<?= htmlspecialchars($booking['booking_id']) ?></h1>
        
        <?php if(isset($message)) echo $message; ?>

        <div class="booking-details">
            <div class="detail-group">
                <label>Tên khách hàng</label>
                <p><?= htmlspecialchars($booking['customer_name']) ?></p>
            </div>
            <div class="detail-group">
                <label>Email</label>
                <p><?= htmlspecialchars($booking['customer_email']) ?></p>
            </div>
            <div class="detail-group">
                <label>Số điện thoại</label>
                <p><?= htmlspecialchars($booking['customer_phone']) ?></p>
            </div>
            <div class="detail-group">
                <label>Tên Tour</label>
                <p><?= htmlspecialchars($booking['tour_name']) ?></p>
            </div>
            <div class="detail-group">
                <label>Ngày đi (Check-in)</label>
                <p><?= date("d/m/Y", strtotime($booking['checkin'])) ?></p>
            </div>

            <div class="detail-group">
                <label>Ngày về (Check-out)</label>
                <p><?= date("d/m/Y", strtotime($booking['checkout'])) ?></p>
            </div>
            <div class="detail-group">
                <label>Số người</label>
                <p><?= htmlspecialchars($booking['people']) ?></p>
            </div>
            <div class="detail-group">
                <label>Loại vé</label>
                <p><?= htmlspecialchars($booking['ticket_type'] ?: 'Không có') ?></p>
            </div>
            <div class="detail-group">
                <label>Phương thức thanh toán</label>
                <p><?= htmlspecialchars($booking['payment_method'] ?: 'Chưa rõ') ?></p>
            </div>
            <div class="detail-group">
                <label>Tiền đặt cọc</label>
                <p><?= number_format($booking['deposit'], 0, ',', '.') ?> vnđ</p>
            </div>
             <div class="detail-group">
                <label>Tổng tiền</label>
                <p><?= number_format($booking['total_price'], 0, ',', '.') ?> vnđ</p>
            </div>
            <div class="detail-group">
                <label>Trạng thái hiện tại</label>
                <p><strong><?= $status_map[$booking['status']] ?></strong></p>
            </div>
            <div class="detail-group full-width">
                <label>Dịch vụ đi kèm</label>
                <p><?= htmlspecialchars($booking['services'] ?: 'Không có') ?></p>
            </div>
            <div class="detail-group full-width">
                <label>Ghi chú của khách hàng</label>
                <p><?= htmlspecialchars($booking['note'] ?: 'Không có') ?></p>
            </div>
        </div>
        
        <form action="" method="POST" class="update-form">
            <label for="status"><strong>Cập nhật trạng thái đơn hàng:</strong></label>
            <select name="status" id="status">
                <?php foreach ($status_map as $code => $text): ?>
                    <option value="<?= $code ?>" <?= ($booking['status'] == $code) ? 'selected' : '' ?>>
                        <?= $text ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Cập nhật</button>
        </form>
    </main>
</body>
</html>