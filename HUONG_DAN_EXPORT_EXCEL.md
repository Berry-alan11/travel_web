# HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG CHỨC NĂNG XUẤT EXCEL/CSV

## ✅ ĐÃ TẠO CÁC FILE SAU:

### 1. **export_excel.php** (Xuất file Excel .xlsx)
- Đường dẫn: `c:\xampp\htdocs\travel_web\admin\export_excel.php`
- Tính năng:
  - ✅ Xuất toàn bộ danh sách booking ra file Excel
  - ✅ Có tiêu đề, header màu xanh, border đẹp
  - ✅ Format số tiền, ngày tháng chuẩn Việt Nam
  - ✅ Tự động điều chỉnh độ rộng cột
  - ✅ Hỗ trợ tiếng Việt 100%

### 2. **export_csv.php** (Xuất file CSV)
- Đường dẫn: `c:\xampp\htdocs\travel_web\admin\export_csv.php`
- Tính năng:
  - ✅ Xuất ra file CSV đơn giản
  - ✅ Mở được bằng Excel
  - ✅ UTF-8 BOM để hiển thị đúng tiếng Việt
  - ✅ Không cần cài thư viện gì

### 3. **manage_bookings.php** (Đã thêm 2 nút)
- ✅ Nút "Xuất Excel" (màu xanh lá)
- ✅ Nút "Xuất CSV" (màu xanh lá)
- Vị trí: Góc phải toolbar, cạnh ô tìm kiếm

---

## 📦 CÀI ĐẶT THƯ VIỆN PHPSPREADSHEET

### **Bước 1**: Kiểm tra Composer đã cài chưa

```bash
php -v
composer -V
```

Nếu chưa có Composer, tải tại: https://getcomposer.org/download/

---

### **Bước 2**: Cài đặt PhpSpreadsheet

Mở **Command Prompt** hoặc **PowerShell**, chạy:

```bash
cd c:\xampp\htdocs\travel_web
composer install
```

Hoặc nếu chưa có `composer.json`:

```bash
composer require phpoffice/phpspreadsheet
```

Thời gian cài: ~2-3 phút

---

### **Bước 3**: Kiểm tra đã cài thành công

Kiểm tra xem thư mục `vendor` đã xuất hiện chưa:

```
c:\xampp\htdocs\travel_web\vendor\
```

Nếu thấy thư mục này → Cài thành công! ✅

---

## 🚀 CÁCH SỬ DỤNG

### **1. Xuất file CSV** (Không cần cài gì)

1. Đăng nhập vào Admin
2. Vào trang "Quản lý Booking"
3. Nhấn nút **"Xuất CSV"** (màu xanh lá)
4. File `danh_sach_booking_2025-12-09.csv` sẽ tự động tải về
5. Mở bằng Excel → Xong!

---

### **2. Xuất file Excel** (Cần cài PhpSpreadsheet trước)

**Nếu ĐÃ cài PhpSpreadsheet**:
1. Đăng nhập vào Admin
2. Vào trang "Quản lý Booking"
3. Nhấn nút **"Xuất Excel"** (màu xanh lá)
4. File `danh_sach_booking_2025-12-09.xlsx` sẽ tự động tải về
5. Mở bằng Excel → File có format đẹp, màu sắc!

**Nếu CHƯA cài PhpSpreadsheet**:
- Sẽ hiện thông báo: "❌ Chưa cài đặt thư viện..."
- → Làm theo Bước 2 ở trên để cài

---

## 📊 NỘI DUNG FILE EXCEL/CSV

File xuất ra sẽ có **13 cột**:

| Cột | Tên Cột | Dữ liệu |
|-----|---------|---------|
| A | Mã Đơn | #1, #2, #3... |
| B | Tên Khách Hàng | Nguyễn Văn A |
| C | Email | test@example.com |
| D | SĐT | 0912345678 |
| E | Tour | Vịnh Hạ Long |
| F | Số Người | 2 |
| G | Ngày Đi | 20/12/2025 |
| H | Ngày Về | 23/12/2025 |
| I | Tổng Tiền | 10.000.000 vnđ |
| J | Đặt Cọc | 3.000.000 vnđ |
| K | Còn Lại | 7.000.000 vnđ |
| L | Thanh Toán | Chuyển khoản |
| M | Trạng Thái | Đã xác nhận |

---

## 🎨 KHÁC BIỆT GIỮA CSV VÀ EXCEL

| Tiêu chí | CSV | Excel |
|----------|-----|-------|
| **Cần cài thư viện** | ❌ Không | ✅ Có (PhpSpreadsheet) |
| **Kích thước file** | Nhỏ (~50KB) | Lớn hơn (~200KB) |
| **Format đẹp** | ❌ Không có | ✅ Có màu, border, bold |
| **Tiêu đề trang** | ❌ Không | ✅ Có |
| **Tự động rộng cột** | ❌ Không | ✅ Có |
| **Khuyến nghị** | Dùng nếu chưa cài LibraryMặ | Dùng để báo cáo chính thức |

---

## 💡 KHUYẾN NGHỊ CHO BÁO CÁO

### **Trong báo cáo Word**:

**Phần VII.2 - Code Xuất Excel/CSV**, paste cả 2 đoạn code:

1. **Code xuất CSV** → Chỉ rõ "Dùng hàm PHP native không cần thư viện"
2. **Code xuất Excel** → Chỉ rõ "Dùng thư viện PhpSpreadsheet chuyên nghiệp"

**Chụp ảnh**:
- Screenshot nút "Xuất Excel" và "Xuất CSV" trên trang Admin
- Screenshot file Excel đã mở (có màu sắc đẹp)
- Screenshot file CSV đã mở

---

## 🔧 TROUBLESHOOTING

### **Lỗi: "composer: The term 'composer' is not recognized"**

**Nguyên nhân**: Chưa cài Composer

**Giải pháp**:
1. Tải Composer: https://getcomposer.org/download/
2. Chạy file `Composer-Setup.exe`
3. Restart Command Prompt
4. Chạy lại: `composer install`

---

### **Lỗi: "❌ Chưa cài đặt thư viện PhpSpreadsheet"**

**Nguyên nhân**: Chưa chạy `composer install`

**Giải pháp**:
```bash
cd c:\xampp\htdocs\travel_web
composer require phpoffice/phpspreadsheet
```

---

### **Lỗi: "Fatal error: Allowed memory size..."**

**Nguyên nhân**: Dữ liệu quá nhiều

**Giải pháp**: Tăng `memory_limit` trong `php.ini`:
```
memory_limit = 512M
```

---

## 📝 PASTE VÀO BÁO CÁO

### **Phần VII.2 - Code Xuất Excel**

Copy code từ file `export_excel.php` (dòng 14-145):

```php
require_once '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
...
```

**Giải thích**:
- Sử dụng thư viện PhpSpreadsheet (chuẩn công nghiệp)
- Tạo file .xlsx với format đẹp mắt
- Có header màu xanh, border, tự động điều chỉnh cột

---

### **Phần VII.2 - Code Xuất CSV**

Copy code từ file `export_csv.php` (dòng 8-76):

```php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=...');
...
```

**Giải thích**:
- Dùng hàm PHP native `fputcsv()`
- UTF-8 BOM để Excel đọc đúng tiếng Việt
- Đơn giản, nhanh, không cần thư viện

---

## ✅ CHECKLIST

- [x] Tạo file `export_excel.php`
- [x] Tạo file `export_csv.php`
- [x] Thêm 2 nút vào `manage_bookings.php`
- [x] Tạo file `composer.json`
- [ ] Cài đặt PhpSpreadsheet (chạy `composer install`)
- [ ] Test xuất CSV
- [ ] Test xuất Excel (sau khi cài lib)
- [ ] Chụp ảnh kết quả
- [ ] Paste code vào báo cáo

---

**Chúc may mắn! 🎉**

Nếu gặp lỗi gì, hỏi tôi nhé! 😊
