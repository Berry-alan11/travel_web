# HƯỚNG DẪN PASTE CODE VÀO BÁO CÁO

## 📋 Phần VII. TRIỂN KHAI CODE

> **Lưu ý**: Copy đúng format code dưới đây (bao gồm số dòng nếu cần). Paste vào báo cáo Word bằng font **Consolas** hoặc **Courier New** size 9-10.

---

## 1. CODE THỐNG KÊ DOANH SỐ (Hưng)

### 📍 Vị trí: `admin/index.php` (File đầy đủ: `c:\xampp\htdocs\travel_web\admin\index.php`)

**Mô tả**: Code này thực hiện 4 loại thống kê cho trang Admin Dashboard:
1. Tổng doanh thu
2. Tổng số đơn đặt tour
3. Tổng số người dùng  
4. Tổng số tour

---

### 📝 **Code SQL thống kê** 
**File**: `admin/index.php` | **Dòng**: 17-34

```php
// 1. Thống kê tổng doanh thu (chỉ tính các đơn đã thanh toán xong)
$result_revenue = $conn->query("SELECT SUM(total_price) as total_revenue FROM bookings");
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];
if (is_null($total_revenue)) {
    $total_revenue = 0;
}

// 2. Thống kê tổng số đơn đặt tour
$result_bookings = $conn->query("SELECT COUNT(*) as total_bookings FROM bookings");
$total_bookings = $result_bookings->fetch_assoc()['total_bookings'];

// 3. Thống kê tổng số người dùng
$result_users = $conn->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $result_users->fetch_assoc()['total_users'];

// 4. Thống kê tổng số tour
$result_tours = $conn->query("SELECT COUNT(*) as total_tours FROM tours");
$total_tours = $result_tours->fetch_assoc()['total_tours'];
```

**Giải thích**:
- `SUM(total_price)`: Tính tổng doanh thu từ tất cả đơn hàng
- `COUNT(*)`: Đếm số lượng bản ghi trong các bảng
- Kiểm tra `is_null()` để xử lý trường hợp chưa có đơn hàng nào
- `number_format()`: Format số tiền theo chuẩn Việt Nam (1.000.000 vnđ)
---

### 📝 **Code lấy đơn hàng gần nhất**
**File**: `admin/index.php` | **Dòng**: 36-45

```php
// 5. Lấy 5 đơn đặt tour gần đây nhất
$recent_bookings_sql = "
    SELECT b.booking_id, u.name as customer_name, t.name as tour_name, 
           b.checkin, b.total_price
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.booking_id DESC
    LIMIT 5
";
$recent_bookings_result = $conn->query($recent_bookings_sql);
```

**Giải thích**:
- Sử dụng `JOIN` để liên kết 3 bảng: `bookings`, `users`, `tours`
- `ORDER BY booking_id DESC`: Sắp xếp giảm dần (mới nhất trước)
- `LIMIT 5`: Chỉ lấy 5 bản ghi đầu tiên

---

### 📝 **Code hiển thị thống kê**
**File**: `admin/index.php` | **Dòng**: 96-133

```html
<section class="stats-grid">
    <div class="stat-card">
        <div class="icon revenue">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div class="info">
            <h3>Tổng Doanh Thu</h3>
            <p><?= number_format($total_revenue, 0, ',', '.') ?> vnđ</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="icon bookings">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div class="info">
            <h3>Tổng Đơn Tour</h3>
            <p><?= $total_bookings ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="icon users">
            <i class="fa-solid fa-user-group"></i>
        </div>
        <div class="info">
            <h3>Tổng Người Dùng</h3>
            <p><?= $total_users ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="icon tours">
            <i class="fa-solid fa-umbrella-beach"></i>
        </div>
        <div class="info">
            <h3>Tổng Số Tour</h3>
            <p><?= $total_tours ?></p>
        </div>
    </div>
</section>
```

**Giải thích**:
- `number_format()`: Format số tiền theo chuẩn Việt Nam (1.000.000 vnđ)
- Sử dụng Font Awesome icons để hiển thị biểu tượng đẹp mắt
- Layout grid 4 cột responsive

---

## 2. CODE XUẤT EXCEL & CSV (Minh Phát)

### 📍 Code thực tế đã tạo sẵn!

**Đã tạo 2 file**:
1. ✅ `admin/export_excel.php` - Xuất Excel chuyên nghiệp
2. ✅ `admin/export_csv.php` - Xuất CSV đơn giản

---

### 📝 **Code xuất file CSV**
**File**: `admin/export_csv.php` (File đầy đủ: `c:\xampp\htdocs\travel_web\admin\export_csv.php`) | **Dòng**: 8-76

```php
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
fputcsv($output, ['Mã Đơn', 'Tên Khách Hàng', 'Email', 'SĐT', 'Tour', 
                   'Số Người', 'Ngày Đi', 'Ngày Về', 'Tổng Tiền', 
                   'Đặt Cọc', 'Còn Lại', 'Thanh Toán', 'Trạng Thái']);

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
exit;
```

**Giải thích**:
- `CASE b.status`: Chuyển đổi số (0,1,2,3) thành text dễ hiểu
- `fputcsv()`: Hàm PHP native để ghi CSV, không cần thư viện
- `fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF))`: UTF-8 BOM giúp Excel hiển thị đúng tiếng Việt
- `header('Content-Disposition: attachment')`: Tự động tải file về
- `date('Y-m-d')`: Đặt tên file theo ngày xuất

---

### 📝 **Code xuất file Excel (PhpSpreadsheet)**
**File**: `admin/export_excel.php` (File đầy đủ: `c:\xampp\htdocs\travel_web\admin\export_excel.php`)

**Phần 1: Import thư viện và chuẩn bị dữ liệu** (Dòng 14-48)

```php
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
            END as status_text
        FROM bookings b
        JOIN tours t ON b.tour_id = t.tour_id
        ORDER BY b.booking_id DESC";

$result = $conn->query($sql);
```

**Phần 2: Tạo tiêu đề và header** (Dòng 50-93)

```php
// Tạo Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Danh Sách Booking');

// Tiêu đề chính
$sheet->mergeCells('A1:M1');
$sheet->setCellValue('A1', 'DANH SÁCH ĐƠN ĐẶT TOUR');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header cột
$headers = [
    'A4' => 'Mã Đơn', 'B4' => 'Tên Khách Hàng', 'C4' => 'Email',
    'D4' => 'SĐT', 'E4' => 'Tour', 'F4' => 'Số Người',
    'G4' => 'Ngày Đi', 'H4' => 'Ngày Về', 'I4' => 'Tổng Tiền (vnđ)',
    'J4' => 'Đặt Cọc (vnđ)', 'K4' => 'Còn Lại (vnđ)',
    'L4' => 'Thanh Toán', 'M4' => 'Trạng Thái'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Style header: màu xanh, chữ trắng, bold
$headerRange = 'A4:M4';
$sheet->getStyle($headerRange)->getFont()->setBold(true);
$sheet->getStyle($headerRange)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF4472C4'); // Màu xanh dương
$sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
```

**Phần 3: Điền dữ liệu** (Dòng 95-119)

```php
// Điền dữ liệu từ database
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

// Auto-size cột
foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
```

**Phần 4: Xuất file** (Dòng 128-137)

```php
// Xuất file Excel
$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="danh_sach_booking_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
```

**Giải thích**:
- **PhpSpreadsheet**: Thư viện chuẩn công nghiệp cho Excel
- `mergeCells()`: Gộp ô để tạo tiêu đề lớn
- `setFillType()` + `setARGB()`: Tô màu nền cho header
- `setAutoSize(true)`: Tự động điều chỉnh độ rộng cột
- `save('php://output')`: Ghi trực tiếp ra trình duyệt

---

### 📝 **Code thêm nút Export vào trang Admin**
**File**: `admin/manage_bookings.php` | **Dòng**: 194-202

```html
<div style="display: flex; gap: 10px;">
    <a href="export_excel.php" class="btn-export" title="Xuất Excel">
        <i class="fa-solid fa-file-excel"></i> Xuất Excel
    </a>
    <a href="export_csv.php" class="btn-export" title="Xuất CSV">
        <i class="fa-solid fa-file-csv"></i> Xuất CSV
    </a>
</div>
```

**CSS cho nút** (Dòng 155-171):

```css
.btn-export {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background-color: #27ae60;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}
.btn-export:hover {
    background-color: #229954;
}
```

**Giải thích**:
- 2 nút màu xanh lá cây (#27ae60) nằm cạnh ô tìm kiếm
- Khi hover, màu đậm hơn (#229954)
- Icon từ Font Awesome (fa-file-excel, fa-file-csv)

---

### 🎯 **So sánh CSV vs Excel**

| Tính năng | CSV | Excel |
|-----------|-----|-------|
| **Cần thư viện** | ❌ Không | ✅ PhpSpreadsheet |
| **Kích thước** | ~50KB | ~200KB |
| **Màu sắc** | ❌ | ✅ Header xanh |
| **Border** | ❌ | ✅ |
| **Auto-size** | ❌ | ✅ |
| **Tiêu đề lớn** | ❌ | ✅ |
| **Format số** | Có (trong text) | Có (native Excel) |
| **Khuyến nghị** | Nhanh, đơn giản | Chuyên nghiệp, đẹp |

---

### 📦 **Cài đặt PhpSpreadsheet** (Nếu chưa có)

**Bước 1**: Tải Composer tại https://getcomposer.org/

**Bước 2**: Chạy lệnh:
```bash
cd c:\xampp\htdocs\travel_web
composer require phpoffice/phpspreadsheet
```

**Bước 3**: Kiểm tra thư mục `vendor` đã xuất hiện → Cài thành công! ✅

---

## 3. CODE HỦY ĐƠN HÀNG (Hải Đăng)

### 📍 Vị trí: `admin/delete_booking.php` (File đầy đủ: `c:\xampp\htdocs\travel_web\admin\delete_booking.php`)

**Mô tả**: Code xử lý yêu cầu xóa (hủy) đơn đặt tour từ Admin

---

### 📝 **Code kiểm tra quyền**
**File**: `admin/delete_booking.php` | **Dòng**: 1-7

```php
<?php
session_start();

// Bảo mật: Chỉ admin mới có quyền
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    die('Bạn không có quyền truy cập.');
}
```

**Giải thích**:
- Kiểm tra session trước khi cho phép xóa
- Chỉ role 0 (Admin) và 2 (Service Provider) mới được xóa
- `die()`: Dừng script nếu không có quyền

---

### 📝 **Code xóa booking**
**File**: `admin/delete_booking.php` | **Dòng**: 11-28

```php
require_once '../connect.php';

// Kiểm tra xem ID có được cung cấp và là số không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $booking_id = (int)$_GET['id'];

    // Chuẩn bị câu lệnh xóa
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);

    // Thực thi và kiểm tra kết quả
    if ($stmt->execute()) {
        // Nếu thành công, chuyển hướng về trang quản lý với thông báo
        header('Location: manage_bookings.php?success=Xóa đơn hàng thành công');
    } else {
        // Nếu thất bại
        header('Location: manage_bookings.php?error=Có lỗi xảy ra khi xóa');
    }
    $stmt->close();
    $conn->close();
} else {
    // Nếu ID không hợp lệ
    header('Location: manage_bookings.php?error=ID đơn hàng không hợp lệ');
}
exit();
?>
```

**Giải thích**:
- `isset($_GET['id'])`: Kiểm tra có tham số ID không
- `is_numeric()`: Validate ID phải là số
- `prepare()`: Sử dụng Prepared Statement để tránh SQL Injection
- `header('Location: ...')`: Redirect với thông báo success/error

---

### 📝 **Code gọi chức năng xóa**
**File**: `admin/manage_bookings.php` | **Dòng**: 227

```php
echo "<a href='delete_booking.php?id=" . $row['booking_id'] . "' 
         class='delete' 
         title='Xóa' 
         onclick='return confirm(\"Bạn có chắc chắn muốn xóa đơn hàng #" . $row['booking_id'] . "?\")'>
         <i class='fa-solid fa-trash'></i>
      </a>";
```

**Giải thích**:
- Link truyền `booking_id` qua GET
- `onclick='return confirm()'`: Hiển thị popup xác nhận trước khi xóa
- Icon thùng rác từ Font Awesome

---

## 4. CODE ĐÁNG CHÚ Ý KHÁC

### 📝 **Code xử lý đặt tour**

**Tính toán ngày kết thúc tour**
**File**: `book-process.php` (File đầy đủ: `c:\xampp\htdocs\travel_web\book-process.php`) | **Dòng**: 40-50

```php
// Trích xuất số ngày từ chuỗi "X ngày Y đêm"
preg_match('/(\d+)\s*ngày/', $duration_text, $matches);
$duration_days = isset($matches[1]) ? (int)$matches[1] : 1;

try {
    $checkin_date = new DateTime($checkin);
    $checkout_date = clone $checkin_date;
    $checkout_date->modify("+{$duration_days} days");
    $checkout = $checkout_date->format('Y-m-d');
} catch (Exception $e) {
    die("❌ Ngày khởi hành không hợp lệ.");
}
```

**Giải thích**:
- `preg_match()`: Regex để tách số ngày từ chuỗi
- `DateTime`: Class PHP xử lý ngày tháng
- `modify()`: Cộng thêm số ngày vào ngày checkin

---

**Tính tổng tiền và tiền còn lại**
**File**: `book-process.php` | **Dòng**: 52-54

```php
$total_price = $price * $people;
$remaining_price = $total_price - $deposit;
```

**Giải thích**:
- `total_price`: Giá tour × Số người
- `remaining_price`: Tổng tiền - Tiền đặt cọc

---

**Insert booking vào database**
**File**: `book-process.php` | **Dòng**: 56-68

```php
$insert = $conn->prepare("INSERT INTO bookings 
    (user_id, name, email, phone, tour_id, people, checkin, checkout, 
     total_price, note, payment_method, ticket_type, discount_code, 
     services, deposit, remaining_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$insert->bind_param("isssiissdsssssdd", 
    $user_id, $name, $email, $phone, $tour_id, $people, 
    $checkin, $checkout, $total_price, $note, $payment_method, 
    $ticket_type, $discount_code, $services, $deposit, $remaining_price
);

if ($insert->execute()) {
    echo "<script>alert('✅ Đặt tour thành công!'); 
          window.location.href='profile/profile.php';</script>";
}
```

**Giải thích**:
- `bind_param()`: 
  - `i` = integer
  - `s` = string
  - `d` = double (float)
- `execute()`: Thực thi câu lệnh INSERT
- JavaScript `alert()` + `window.location.href`: Hiển thị thông báo và chuyển trang

---

## 📌 CÁCH PASTE VÀO BÁO CÁO WORD

### **Bước 1**: Tạo heading

```
VII. TRIỂN KHAI CODE

7.1. Code Thống Kê Doanh Số
```

### **Bước 2**: Paste code

1. Chọn font **Consolas** hoặc **Courier New**
2. Size **9** hoặc **10**
3. Background màu xám nhạt `#F5F5F5`
4. Indent trái: 0.5cm

### **Bước 3**: Thêm giải thích

Dưới mỗi đoạn code, thêm 2-3 dòng giải thích bằng **font thường** (Times New Roman 13).

---

## ✅ CHECKLIST

- [ ] Copy code thống kê vào phần 7.1
- [ ] Copy code xuất Excel/CSV vào phần 7.2
- [ ] Copy code hủy đơn hàng vào phần 7.3
- [ ] Thêm giải thích cho từng đoạn code
- [ ] Format code đúng font và màu
- [ ] Kiểm tra căn lề

---

**Lưu ý cuối**: Nếu cần file code thực tế để chạy thử, tôi có thể tạo file `export_bookings.php` hoàn chỉnh!

📞 **Contact**: Hỏi tôi nếu cần hỗ trợ thêm! 😊
