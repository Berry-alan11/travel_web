<?php
// Kết nối database
$conn = new mysqli('localhost', 'root', '', 'TravelWorldWeb');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone']; // Lấy thêm số điện thoại
$id_card = $_POST['id_card']; // Lấy thêm id_card

// Kiểm tra id_card có đủ 12 chữ số không
if (!preg_match("/^\d{12}$/", $id_card)) {
    header('Location: ../register.php?error=' . urlencode('CCCD phải có 12 chữ số.'));
    exit();
}

// Hash mật khẩu
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Kiểm tra email đã tồn tại chưa
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Location: ../register.php?error=' . urlencode('Email đã tồn tại.'));
    exit();
} else {
    // Chèn người dùng mới (thêm phone và id_card)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, phone, id_card) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password_hash, $phone, $id_card);

    if ($stmt->execute()) {
        header('Location: ../login.php?success=' . urlencode('Đăng ký thành công. Hãy đăng nhập!'));
    } else {
        header('Location: ../register.php?error=' . urlencode('Có lỗi xảy ra.'));
    }
}

$conn->close();
?>
