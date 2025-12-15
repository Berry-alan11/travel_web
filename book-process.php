<?php
// FILE: book-process.php
// Đóng vai trò là Entry Point (Điểm tiếp nhận yêu cầu)

require_once 'BookingController.php';

// Khởi tạo Controller và xử lý yêu cầu
$controller = new BookingController();
$controller->handleRequest();

?>