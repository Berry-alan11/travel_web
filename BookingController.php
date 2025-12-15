<?php
require_once 'BookingModel.php';

class BookingController {
    private $model;

    public function __construct() {
        $this->model = new BookingModel();
    }

    public function handleRequest() {
        // 1. Kiểm tra phương thức POST
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            die("❌ Phương thức truy cập không hợp lệ.");
        }

        // 2. Validate Session
        $this->validateSession();

        // 3. Lấy dữ liệu và Xử lý logic
        $data = $this->prepareBookingData();

        // 4. Gọi Model lưu vào DB
        $result = $this->model->saveBooking($data);

        // 5. Phản hồi kết quả
        if ($result === true) {
            echo "<script>alert('✅ Đặt tour thành công!'); window.location.href='profile/profile.php';</script>";
        } else {
            echo "❌ Lỗi khi thêm đơn đặt: " . $result;
        }
    }

    private function validateSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            die("❌ Bạn cần đăng nhập để đặt tour.");
        }
    }

    private function prepareBookingData() {
        // Lấy dữ liệu Raw từ Form
        $data = [
            'user_id' => $_SESSION['user_id'],
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'people' => (int)($_POST['people'] ?? 1),
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'checkin' => $_POST['checkin'] ?? '',
            'deposit' => (float)$_POST['deposit'],
            'payment_method' => $_POST['payment_method'] ?? '', // String
            'note' => $_POST['note'] ?? '',
            'ticket_type' => $_POST['ticket_type'] ?? '',
            'discount_code' => $_POST['discount_code'] ?? '',
            'services' => isset($_POST['services']) ? implode(',', $_POST['services']) : '' 
        ];

        // Lấy thông tin Tour từ DB để tính toán
        $tourInfo = $this->model->getTourInfo($data['tour_id']);
        if (!$tourInfo) {
            die("❌ Không tìm thấy tour với ID đã chọn.");
        }

        // Logic tính ngày Checkout
        $data['checkout'] = $this->calculateCheckoutDate($data['checkin'], $tourInfo['duration']);

        // Logic tính tiền
        $price = $tourInfo['price'];
        $data['total_price'] = $price * $data['people'];
        $data['remaining_price'] = $data['total_price'] - $data['deposit'];

        return $data;
    }

    private function calculateCheckoutDate($checkin, $duration_text) {
        preg_match('/(\d+)\s*ngày/', $duration_text, $matches);
        $days = isset($matches[1]) ? (int)$matches[1] : 1;
        
        try {
            $checkin_date = new DateTime($checkin);
            $checkout_date = clone $checkin_date;
            $checkout_date->modify("+{$days} days");
            return $checkout_date->format('Y-m-d');
        } catch (Exception $e) {
            die("❌ Ngày khởi hành không hợp lệ.");
        }
    }
}
?>
