<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 0) {
    die('Bạn không có quyền truy cập.');
}

require_once '../connect.php';

// Kiểm tra xem ID có được cung cấp và là số không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $destination_id = (int)$_GET['id'];

    // --- XÓA CÁC FILE ẢNH LIÊN QUAN ---
    // 1. Lấy đường dẫn ảnh từ database trước khi xóa
    $stmt_img = $conn->prepare("SELECT image_url, image_gallery FROM destinations WHERE destination_id = ?");
    $stmt_img->bind_param("i", $destination_id);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        
        // 2. Xóa ảnh đại diện (nếu là file upload)
        $main_image = $row_img['image_url'];
        if (!empty($main_image) && strpos($main_image, 'assets/images') === 0 && file_exists('../' . $main_image)) {
            unlink('../' . $main_image);
        }

        // 3. Xóa các ảnh trong thư viện ảnh (nếu có)
        $gallery_images = $row_img['image_gallery'];
        if (!empty($gallery_images)) {
            $gallery_paths = explode(',', $gallery_images); // Tách chuỗi thành mảng các đường dẫn
            foreach ($gallery_paths as $path) {
                if (!empty($path) && file_exists('../' . $path)) {
                    unlink('../' . $path);
                }
            }
        }
    }
    $stmt_img->close();


    // --- XÓA BẢN GHI ĐỊA ĐIỂM TRONG DATABASE ---
    $stmt_delete = $conn->prepare("DELETE FROM destinations WHERE destination_id = ?");
    $stmt_delete->bind_param("i", $destination_id);

    if ($stmt_delete->execute()) {
        header('Location: manage_destinations.php?success=Xóa địa điểm thành công');
    } else {
        header('Location: manage_destinations.php?error=Có lỗi xảy ra khi xóa địa điểm');
    }
    $stmt_delete->close();
    $conn->close();

} else {
    // Nếu ID không hợp lệ
    header('Location: manage_destinations.php?error=ID địa điểm không hợp lệ');
}
exit();
?>