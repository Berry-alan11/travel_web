<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}

// Kiểm tra xem có thư viện PhpSpreadsheet không
if (!file_exists('../vendor/autoload.php')) {
    die('❌ Chưa cài đặt thư viện PhpSpreadsheet. Vui lòng chạy: composer require phpoffice/phpspreadsheet');
}

require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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

// Tạo Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Danh Sách Booking');

// === TIÊU ĐỀ CHÍNH ===
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'DANH SÁCH ĐƠN ĐẶT TOUR');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Ngày xuất
$sheet->mergeCells('A2:M2');
$sheet->setCellValue('A2', 'Ngày xuất: ' . date('d/m/Y H:i:s'));
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// === HEADER CỘT (Dòng 4) ===
$headers = [
    'A4' => 'Mã Đơn',
    'B4' => 'Tên Khách Hàng',
    'C4' => 'Email',
    'D4' => 'SĐT',
    'E4' => 'Tour',
    'F4' => 'Số Người',
    'G4' => 'Ngày Đi',
    'H4' => 'Ngày Về',
    'I4' => 'Tổng Tiền (vnđ)',
    'J4' => 'Đặt Cọc (vnđ)',
    'K4' => 'Còn Lại (vnđ)',
    'L4' => 'Thanh Toán',
    'M4' => 'Trạng Thái'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Style header
$headerRange = 'A4:M4';
$sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(11);
$sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle($headerRange)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF4472C4'); // Màu xanh dương
$sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF'); // Chữ trắng

// Border header
$sheet->getStyle($headerRange)->getBorders()->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

// === ĐIỀN DỮ LIỆU ===
$row = 5; // Bắt đầu từ dòng 5
if ($result && $result->num_rows > 0) {
    while ($booking = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, '#' . $booking['booking_id']);
        $sheet->setCellValue('B' . $row, $booking['customer_name']);
        $sheet->setCellValue('C' . $row, $booking['email']);
        $sheet->setCellValue('D' . $row, $booking['phone']);
        $sheet->setCellValue('E' . $row, $booking['tour_name']);
        $sheet->setCellValue('F' . $row, $booking['people']);
        $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($booking['checkin'])));
        $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($booking['checkout'])));
        $sheet->setCellValue('I' . $row, number_format($booking['total_price'], 0, ',', '.'));
        $sheet->setCellValue('J' . $row, number_format($booking['deposit'], 0, ',', '.'));
        $sheet->setCellValue('K' . $row, number_format($booking['remaining_price'], 0, ',', '.'));
        $sheet->setCellValue('L' . $row, $booking['payment_method']);
        $sheet->setCellValue('M' . $row, $booking['status_text']);
        
        $row++;
    }
}

// === AUTO-SIZE CỘT ===
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Border cho toàn bộ dữ liệu
if ($row > 5) {
    $dataRange = 'A4:M' . ($row - 1);
    $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
}

// === XUẤT FILE EXCEL ===
$writer = new Xlsx($spreadsheet);

// Thiết lập header HTTP
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="danh_sach_booking_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Ghi file ra output
$writer->save('php://output');

$conn->close();
exit;
?>
