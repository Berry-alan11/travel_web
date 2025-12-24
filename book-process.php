<?php
// FILE: book-process.php
// Đóng vai trò là Entry Point (Điểm tiếp nhận yêu cầu)
// Lấy dữ liệu từ form và xử lý logic
// sau đó giao cho Model lưu vào DB(file: BookingModel.php đảm nhiệm việc lưu vào DB)
// sau đó giao cho Controller xử lý logic(file: BookingController.php đảm nhiệm việc xử lý logic)
require_once 'BookingController.php';

// Khởi tạo Controller và xử lý yêu cầu
$controller = new BookingController();
$controller->handleRequest();

?>