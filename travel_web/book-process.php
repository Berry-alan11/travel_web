<?php
session_start();
$conn = new mysqli("localhost", "root", "", "travelworldweb");

// Kiểm tra phương thức
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        die("❌ Bạn cần đăng nhập để đặt tour.");
    }

    // Lấy dữ liệu từ form
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $people = (int)($_POST['people'] ?? 1);
    $tour_id = (int)($_POST['tour_id'] ?? 0);
    $checkin = $_POST['checkin'] ?? '';
    $discount_code = $_POST['discount_code'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? '';
    $note = $_POST['note'] ?? '';
    $services = isset($_POST['services']) ? implode(',', $_POST['services']) : '';
    $deposit = (float)$_POST['deposit'];  

    // Lấy thông tin tour
    $stmt = $conn->prepare("SELECT price, duration FROM tours WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("❌ Không tìm thấy tour với ID đã chọn.");
    }

    $tour = $result->fetch_assoc();
    $price = $tour['price'];
    $duration_text = $tour['duration'];

    preg_match('/(\d+)\s*ngày/', $duration_text, $matches);
    $duration_days = isset($matches[1]) ? (int)$matches[1] : 1;

    try {
        $checkin_date = new DateTime($checkin);
        $checkout_date = clone $checkin_date;
        $checkout_date->modify("+{$duration_days} days");
        $checkout = $checkout_date->format('Y-m-d');
    } catch (Exception $e) {
        die("❌ Ngày khởi hành không hợp lệ.");
    }

    $total_price = $price * $people;

    $remaining_price = $total_price - $deposit;

   $insert = $conn->prepare("INSERT INTO bookings (user_id, name, email, phone, tour_id, people, checkin, checkout, total_price, note, payment_method, ticket_type, discount_code, services, deposit, remaining_price)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // ===== SỬA LỖI TẠI ĐÂY =====
    // Thay đổi kiểu dữ liệu của $payment_method từ 'i' (integer) thành 's' (string)
    $insert->bind_param("isssiissdsssssdd", $user_id, $name, $email, $phone, $tour_id, $people, $checkin, $checkout, $total_price, $note, $payment_method, $ticket_type, $discount_code, $services, $deposit, $remaining_price);


    if ($insert->execute()) {
        echo "<script>alert('✅ Đặt tour thành công!'); window.location.href='profile/profile.php';</script>";
    } else {
        echo "❌ Lỗi khi thêm đơn đặt: " . $insert->error;
    }

    $insert->close();
    $stmt->close();
    $conn->close();
} else {
    echo "❌ Phương thức truy cập không hợp lệ.";
}
?>