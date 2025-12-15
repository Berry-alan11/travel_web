<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}
require_once '../connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $contact_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM contacts WHERE contact_id = ?");
    $stmt->bind_param("i", $contact_id);
    
    if ($stmt->execute()) {
        header('Location: manage_contacts.php?success=Xóa tin nhắn thành công.');
    } else {
        header('Location: manage_contacts.php?error=Có lỗi xảy ra khi xóa.');
    }
    $stmt->close();
    $conn->close();
} else {
    header('Location: manage_contacts.php?error=ID không hợp lệ.');
}
exit();
?>