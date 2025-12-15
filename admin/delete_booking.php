<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}

require_once '../connect.php';

// Kiểm tra xem ID có được cung cấp và là số không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = (int)$_GET['id'];

    // Chuẩn bị câu lệnh xóa
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);

    // Thực thi và kiểm tra kết quả
    if ($stmt->execute()) {
        // Nếu thành công, chuyển hướng về trang quản lý với thông báo
        header('Location: manage_bookings.php?success=Xóa đơn hàng thành công');
    } else {
        // Nếu thất bại
        header('Location: manage_bookings.php?error=Có lỗi xảy ra khi xóa');
    }
    $stmt->close();
    $conn->close();
} else {
    // Nếu ID không hợp lệ
    header('Location: manage_bookings.php?error=ID đơn hàng không hợp lệ');
}
exit();
?>