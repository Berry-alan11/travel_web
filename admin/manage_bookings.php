<?php
session_start();

// --- BẢO MẬT ---
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}

// --- KẾT NỐI DATABASE ---
require_once '../connect.php';

// --- LOGIC PHÂN TRANG VÀ TÌM KIẾM ---

// 1. Cấu hình phân trang
$records_per_page = 10; // Số đơn hàng trên mỗi trang
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// 2. Xử lý tìm kiếm
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql_where = '';
$params = [];
$param_types = '';

if (!empty($search_term)) {
    // Tìm kiếm theo tên khách hàng, email khách hàng, hoặc tên tour
    $sql_where = "WHERE u.name LIKE ? OR u.email LIKE ? OR t.name LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term, $like_term];
    $param_types = "sss";
}

// 3. Lấy tổng số bản ghi (để tính tổng số trang)
$total_records_sql = "SELECT COUNT(*) FROM bookings b JOIN users u ON b.user_id = u.user_id JOIN tours t ON b.tour_id = t.tour_id " . $sql_where;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();


// 4. Lấy dữ liệu cho trang hiện tại (lấy thêm cột STATUS)
$data_sql = "
    SELECT b.booking_id, b.status, u.name as customer_name, t.name as tour_name, b.checkin, b.total_price
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    $sql_where
    ORDER BY b.booking_id DESC
    LIMIT ? OFFSET ?
";

$stmt_data = $conn->prepare($data_sql);
$final_params = $params;
$final_param_types = $param_types . "ii";
$final_params[] = $records_per_page;
$final_params[] = $offset;

if (!empty($params)) {
    $stmt_data->bind_param($final_param_types, ...$final_params);
} else {
    $stmt_data->bind_param("ii", $records_per_page, $offset);
}

$stmt_data->execute();
$bookings_result = $stmt_data->get_result();

// Mảng định nghĩa trạng thái và màu sắc
$status_map = [
    0 => ['text' => 'Mới', 'color' => '#3498db'],
    1 => ['text' => 'Đã xác nhận', 'color' => '#27ae60'],
    2 => ['text' => 'Đã hoàn thành', 'color' => '#8e44ad'],
    3 => ['text' => 'Đã hủy', 'color' => '#e74c3c']
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Booking - Admin</title>
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="stylesheet" href="admin_style.css">

    <style>
        /* CSS cho các thành phần riêng của trang này */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-form {
            display: flex;
        }
        .search-form input[type="text"] {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px 0 0 5px;
            width: 300px;
        }
        .search-form button {
            padding: 10px 15px;
            border: none;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }

        .actions a {
            margin-right: 10px;
            color: var(--primary-color);
            text-decoration: none;
        }
        .actions a.delete {
            color: #e74c3c;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            color: var(--primary-color);
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid var(--border-color);
            margin: 0 4px;
            border-radius: 5px;
        }
        .pagination a.current {
            background-color: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
        }
        .pagination a:hover:not(.current) {
            background-color: #f4f4f4;
        }
        
        /* CSS cho nút Export */
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn-export:hover {
            background-color: #229954;
        }
        .btn-export i {
            font-size: 16px;
        }
    </style>
</head>
<body>
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2>
        </div>
        <ul class="sidebar-nav">
			<li><a href="index.php"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
			<li><a href="manage_tours.php" <?php if(basename($_SERVER['PHP_SELF']) == 'manage_tours.php') echo 'class="active"'; ?>><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
			<li><a href="manage_bookings.php" <?php if(basename($_SERVER['PHP_SELF']) == 'manage_bookings.php') echo 'class="active"'; ?>><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
			<li><a href="manage_destinations.php" <?php if(basename($_SERVER['PHP_SELF']) == 'manage_destinations.php') echo 'class="active"'; ?>><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>

			<?php // Chỉ hiển thị mục này nếu là Admin (role 0)
			if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 0): ?>
				<li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
			<?php endif; ?>

			<li><a href="manage_contacts.php" <?php if(basename($_SERVER['PHP_SELF']) == 'manage_contacts.php') echo 'class="active"'; ?>><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
		</ul>
        <div class="sidebar-footer">
            <a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Quản lý Đơn Đặt Tour</h1>
            <div>
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            </div>
        </header>


        <div class="toolbar">
            <form action="manage_bookings.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Tìm theo tên khách, email, tên tour..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
            
            <div style="display: flex; gap: 10px;">
                <a href="export_excel.php" class="btn-export" title="Xuất Excel">
                    <i class="fa-solid fa-file-excel"></i> Xuất Excel
                </a>
                <a href="export_csv.php" class="btn-export" title="Xuất CSV">
                    <i class="fa-solid fa-file-csv"></i> Xuất CSV
                </a>
            </div>
        </div>


        <section class="data-table-container">
            <table>
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Tên Khách Hàng</th>
                        <th>Tên Tour</th>
                        <th>Ngày Đi</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
				<tbody>
					<?php
						if ($bookings_result && $bookings_result->num_rows > 0) {
							while($row = $bookings_result->fetch_assoc()) {
								// Lấy thông tin trạng thái từ mảng status_map
								$status_info = $status_map[$row['status']] ?? ['text' => 'Không rõ', 'color' => '#7f8c8d'];
								
								echo "<tr>";
								echo "<td>#" . htmlspecialchars($row['booking_id']) . "</td>";
								echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
								echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
								echo "<td>" . date("d/m/Y", strtotime($row['checkin'])) . "</td>";
								echo "<td>" . number_format($row['total_price'], 0, ',', '.') . " vnđ</td>";

								// Hiển thị trạng thái với màu sắc tương ứng
								echo "<td><span class='status-badge' style='background-color: " . $status_info['color'] . ";'>" . $status_info['text'] . "</span></td>";
								
								// Sửa lại các nút để trỏ đến tệp xử lý và truyền ID
								echo "<td class='actions'>";
								echo "<a href='edit_booking.php?id=" . $row['booking_id'] . "' title='Xem & Sửa'><i class='fa-solid fa-pen-to-square'></i></a>";
								echo "<a href='delete_booking.php?id=" . $row['booking_id'] . "' class='delete' title='Xóa' onclick='return confirm(\"Bạn có chắc chắn muốn xóa đơn hàng #" . $row['booking_id'] . "?\")'><i class='fa-solid fa-trash'></i></a>";
								echo "</td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='7' style='text-align:center;'>Không tìm thấy đơn đặt tour nào.</td></tr>";
						}
						$stmt_data->close();
						$conn->close();
					?>
				</tbody>
            </table>
        </section>

        <div class="pagination">
            <?php
            // Hiển thị các nút phân trang
            if ($total_pages > 1) {
                // Nút Trang trước
                if ($page > 1) {
                    echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search_term) . "'>&laquo; Trước</a>";
                }

                // Các nút số trang
                for ($i = 1; $i <= $total_pages; $i++) {
                    $class = ($i == $page) ? "current" : "";
                    echo "<a href='?page=$i&search=" . urlencode($search_term) . "' class='$class'>$i</a>";
                }

                // Nút Trang sau
                if ($page < $total_pages) {
                    echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search_term) . "'>Sau &raquo;</a>";
                }
            }
            ?>
        </div>
    </main>

</body>
</html>