<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php'); die();
}
require_once '../connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_contacts.php?error=ID không hợp lệ'); exit();
}
$contact_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM contacts WHERE contact_id = ?");
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: manage_contacts.php?error=Không tìm thấy tin nhắn'); exit();
}
$contact = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xem tin nhắn - Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .main-content { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; margin-left: 280px; }
        .contact-info { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .contact-info p { margin: 5px 0; }
        .contact-message { white-space: pre-wrap; line-height: 1.7; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; }
    </style>
</head>
<body>
    <aside class="sidebar">
        </aside>
    <main class="main-content">
        <a href="manage_contacts.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại Hòm thư</a>
        <h1>Chi tiết tin nhắn</h1>
        <div class="contact-info">
            <p><strong>Từ:</strong> <?= htmlspecialchars($contact['name']) ?></p>
            <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a></p>
            <p><strong>Ngày gửi:</strong> <?= date("d/m/Y H:i:s", strtotime($contact['created_at'])) ?></p>
        </div>
        <div class="contact-message">
            <p><?= nl2br(htmlspecialchars($contact['message'])) // nl2br để giữ lại các dấu xuống dòng ?></p>
        </div>
    </main>
</body>
</html>