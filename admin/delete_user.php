<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}
require_once '../connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id_to_delete = (int)$_GET['id'];

    // Ngăn admin tự xóa chính mình
    if ($user_id_to_delete == $_SESSION['user_id']) {
        header('Location: manage_users.php?error=Bạn không thể tự xóa chính mình.');
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id_to_delete);
    
    try {
        if ($stmt->execute()) {
            header('Location: manage_users.php?success=Xóa người dùng thành công.');
        } else {
            header('Location: manage_users.php?error=Có lỗi xảy ra khi xóa.');
        }
    } catch (mysqli_sql_exception $e) {
        // Bắt lỗi khóa ngoại (foreign key) nếu người dùng đã có booking
        if ($e->getCode() == 1451) { 
            header('Location: manage_users.php?error=Không thể xóa người dùng này vì họ đã có đơn đặt tour.');
        } else {
            header('Location: manage_users.php?error=Lỗi không xác định.');
        }
    }
    $stmt->close();
    $conn->close();
} else {
    header('Location: manage_users.php?error=ID người dùng không hợp lệ.');
}
exit();
?>