<?php
$conn = new mysqli("localhost", "root", "", "travelworldweb");
session_start();
$user_id = $_SESSION['user_id'];

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$old_password = $_POST['old_password'];
if (!empty($old_password)) {
    $sql = "SELECT password_hash FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($old_password, $user['password_hash'])) {
        die("❌ Mật khẩu cũ sai.");
        header("Location: profile.php");
        exit;
    }
}

$email_check_query = "SELECT * FROM users WHERE email = ? AND user_id != ?";
$stmt = $conn->prepare($email_check_query);
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
$email_check_result = $stmt->get_result();

if ($email_check_result->num_rows > 0) {
    $_SESSION['error_message'] = "❌ Email đã tồn tại, vui lòng nhập email khác.";
    header("Location: profile.php");
    exit;
}

if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password_hash = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $password_hash, $user_id);
} else {
    $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
}

$stmt->execute();

$_SESSION['success_message'] = "Thông tin đã được cập nhật thành công!";

header("Location: profile.php");
?>
