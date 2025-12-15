<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}

require_once '../connect.php';

// Lấy tất cả booking với thông tin chi tiết
$sql = "SELECT 
            b.booking_id,
            b.name as customer_name,
            b.email,
            b.phone,
            t.name as tour_name,
            b.people,
            b.checkin,
            b.checkout,
            b.total_price,
            b.deposit,
            b.remaining_price,
            b.payment_method,
            CASE b.status
                WHEN 0 THEN 'Mới'
                WHEN 1 THEN 'Đã xác nhận'
                WHEN 2 THEN 'Đã hoàn thành'
                WHEN 3 THEN 'Đã hủy'
                ELSE 'Không rõ'
            END as status_text
        FROM bookings b
        JOIN tours t ON b.tour_id = t.tour_id
        ORDER BY b.booking_id DESC";

$result = $conn->query($sql);

// Thiết lập header CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=danh_sach_booking_' . date('Y-m-d') . '.csv');

// Mở output stream
$output = fopen('php://output', 'w');

// UTF-8 BOM (để Excel đọc được tiếng Việt)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Tiêu đề
fputcsv($output, [
    'Mã Đơn',
    'Tên Khách Hàng',
    'Email',
    'SĐT',
    'Tour',
    'Số Người',
    'Ngày Đi',
    'Ngày Về',
    'Tổng Tiền',
    'Đặt Cọc',
    'Còn Lại',
    'Thanh Toán',
    'Trạng Thái'
]);

// Dữ liệu
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            '#' . $row['booking_id'],
            $row['customer_name'],
            $row['email'],
            $row['phone'],
            $row['tour_name'],
            $row['people'],
            date('d/m/Y', strtotime($row['checkin'])),
            date('d/m/Y', strtotime($row['checkout'])),
            number_format($row['total_price'], 0, ',', '.') . ' vnđ',
            number_format($row['deposit'], 0, ',', '.') . ' vnđ',
            number_format($row['remaining_price'], 0, ',', '.') . ' vnđ',
            $row['payment_method'],
            $row['status_text']
        ]);
    }
}

fclose($output);
$conn->close();
exit;
?>
