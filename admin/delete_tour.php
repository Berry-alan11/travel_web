<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 0) {
    die('Bạn không có quyền truy cập.');
}

require_once '../connect.php';

// Kiểm tra xem ID có được cung cấp và là số không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tour_id = (int)$_GET['id'];

    // --- XÓA FILE ẢNH CŨ TRÊN SERVER ---
    // 1. Lấy đường dẫn ảnh từ database trước khi xóa bản ghi
    $stmt_img = $conn->prepare("SELECT image_url FROM tours WHERE tour_id = ?");
    $stmt_img->bind_param("i", $tour_id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        $image_path_from_db = $row_img['image_url'];
        $full_image_path = '../' . $image_path_from_db; // Đường dẫn đầy đủ từ thư mục admin ra ngoài rồi vào uploads

        // 2. Kiểm tra file tồn tại và xóa nó
        if (!empty($image_path_from_db) && file_exists($full_image_path)) {
            unlink($full_image_path);
        }
    }
    $stmt_img->close();


    // --- XÓA BẢN GHI TOUR TRONG DATABASE ---
    $stmt_delete = $conn->prepare("DELETE FROM tours WHERE tour_id = ?");
    $stmt_delete->bind_param("i", $tour_id);

    if ($stmt_delete->execute()) {
        header('Location: manage_tours.php?success=Xóa tour thành công');
    } else {
        header('Location: manage_tours.php?error=Có lỗi xảy ra khi xóa tour');
    }
    $stmt_delete->close();
    $conn->close();

} else {
    // Nếu ID không hợp lệ
    header('Location: manage_tours.php?error=ID tour không hợp lệ');
}
exit();
?>