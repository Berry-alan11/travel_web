<?php
require_once 'connect.php'; // Sử dụng file kết nối có sẵn

class BookingModel {
    private $conn;

    public function __construct() {
        global $conn; // Lấy biến $conn từ connect.php
        if (!isset($conn)) {
             $this->conn = new mysqli("localhost", "root", "", "travelworldweb");
        } else {
            $this->conn = $conn;
        }
    }

    // Lấy thông tin tour theo ID
    public function getTourInfo($tour_id) {
        $stmt = $this->conn->prepare("SELECT price, duration FROM tours WHERE tour_id = ?");
        $stmt->bind_param("i", $tour_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lưu đơn đặt tour mới
    public function saveBooking($data) {
        $sql = "INSERT INTO bookings (user_id, name, email, phone, tour_id, people, checkin, checkout, total_price, note, payment_method, ticket_type, discount_code, services, deposit, remaining_price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        // Chuẩn bị các biến để bind param (tránh lỗi tham chiếu)
        $user_id = $data['user_id'];
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $tour_id = $data['tour_id'];
        $people = $data['people'];
        $checkin = $data['checkin'];
        $checkout = $data['checkout'];
        $total_price = $data['total_price'];
        $note = $data['note'];
        $payment_method = $data['payment_method'];
        $ticket_type = $data['ticket_type'];
        $discount_code = $data['discount_code'];
        $services = $data['services'];
        $deposit = $data['deposit'];
        $remaining_price = $data['remaining_price'];

        $stmt->bind_param("isssiissdsssssdd", 
            $user_id, $name, $email, $phone, $tour_id, 
            $people, $checkin, $checkout, $total_price, 
            $note, $payment_method, $ticket_type, $discount_code, 
            $services, $deposit, $remaining_price
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return "Lỗi SQL: " . $stmt->error;
        }
    }
}
?>
