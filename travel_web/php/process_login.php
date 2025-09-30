<?php
session_start();

// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'TravelWorldWeb');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$email = $_POST['email'];
$password = $_POST['password'];

// Tìm user theo email, **lấy thêm cột user_role**
$stmt = $conn->prepare("SELECT user_id, name, email, password_hash, user_role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Xác thực mật khẩu
    if (password_verify($password, $user['password_hash'])) {
        // Mật khẩu đúng, lưu thông tin vào session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name']; // Thay vì user_name
        $_SESSION['user_role'] = $user['user_role']; // **DÒNG QUAN TRỌNG NHẤT**

        // Chuyển hướng về trang chủ
        header('Location: ../index.php');
        exit(); // Thêm exit() để dừng script ngay sau khi chuyển hướng
    } else {
        // Mật khẩu không đúng
        header('Location: ../login.php?error=' . urlencode('Mật khẩu không đúng.'));
        exit();
    }
} else {
    // Email không tồn tại
    header('Location: ../login.php?error=' . urlencode('Email không tồn tại.'));
    exit();
}

$conn->close();
?>