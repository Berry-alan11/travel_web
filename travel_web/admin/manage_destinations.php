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
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql_where = '';
$params = [];
$param_types = '';

if (!empty($search_term)) {
    // Tìm kiếm theo tên địa điểm hoặc quốc gia
    $sql_where = "WHERE name LIKE ? OR country LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term];
    $param_types = "ss";
}

// Lấy tổng số địa điểm
$total_records_sql = "SELECT COUNT(*) FROM destinations " . $sql_where;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();

// Lấy dữ liệu cho trang hiện tại
$data_sql = "
    SELECT destination_id, name, country, image_url, best_time_to_visit
    FROM destinations
    $sql_where
    ORDER BY destination_id DESC
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
$destinations_result = $stmt_data->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Địa điểm - Admin</title>
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">

    <style>
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-form { display: flex; }
        .search-form input[type="text"] { padding: 10px; border: 1px solid var(--border-color); border-radius: 5px 0 0 5px; width: 300px; }
        .search-form button, .btn-add { padding: 10px 15px; border: none; background-color: var(--primary-color); color: white; cursor: pointer; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; }
        .search-form button { border-radius: 0 5px 5px 0; }
        .btn-add { border-radius: 5px; }
        .btn-add i { margin-right: 5px; }
        .actions a { margin-right: 10px; color: var(--primary-color); text-decoration: none; }
        .actions a.delete { color: #e74c3c; }
        .pagination { display: flex; justify-content: center; margin-top: 20px; }
        .pagination a { color: var(--primary-color); padding: 8px 16px; text-decoration: none; border: 1px solid var(--border-color); margin: 0 4px; border-radius: 5px; }
        .pagination a.current { background-color: var(--primary-color); color: white; border: 1px solid var(--primary-color); }
        .pagination a:hover:not(.current) { background-color: #f4f4f4; }
        .dest-image { width: 100px; height: 60px; object-fit: cover; border-radius: 5px; }
        .data-table-container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { font-weight: 600; }
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
            <h1>Quản lý Địa điểm</h1>
            <div>
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            </div>
        </header>

        <div class="toolbar">
            <a href="edit_destination.php" class="btn-add"><i class="fa-solid fa-plus"></i> Thêm Địa điểm Mới</a>
            <form action="manage_destinations.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Tìm theo tên địa điểm, quốc gia..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <section class="data-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh đại diện</th>
                        <th>Tên Địa điểm</th>
                        <th>Quốc gia</th>
                        <th>Thời điểm đẹp nhất</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($destinations_result && $destinations_result->num_rows > 0) {
                            while($row = $destinations_result->fetch_assoc()) {
                                $image_url = htmlspecialchars($row['image_url']);
                                $image_path = (strpos($image_url, 'http') === 0 || strpos($image_url, 'https') === 0) ? $image_url : '../' . $image_url;
                                
                                echo "<tr>";
                                echo "<td>" . $row['destination_id'] . "</td>";
                                echo "<td><img src='" . $image_path . "' alt='" . htmlspecialchars($row['name']) . "' class='dest-image'></td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['best_time_to_visit']) . "</td>";
                                echo "<td class='actions'>";
                                echo "<a href='edit_destination.php?id=" . $row['destination_id'] . "' title='Sửa'><i class='fa-solid fa-pen-to-square'></i></a>";
                                echo "<a href='delete_destination.php?id=" . $row['destination_id'] . "' class='delete' title='Xóa' onclick='return confirm(\"Bạn có chắc chắn muốn xóa địa điểm này? Thao tác này không thể hoàn tác.\")'><i class='fa-solid fa-trash'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>Không tìm thấy địa điểm nào.</td></tr>";
                        }
                        $stmt_data->close();
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </section>

        <div class="pagination">
            <?php
            if ($total_pages > 1) {
                if ($page > 1) { echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search_term) . "'>&laquo; Trước</a>"; }
                for ($i = 1; $i <= $total_pages; $i++) {
                    $class = ($i == $page) ? "current" : "";
                    echo "<a href='?page=$i&search=" . urlencode($search_term) . "' class='$class'>$i</a>";
                }
                if ($page < $total_pages) { echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search_term) . "'>Sau &raquo;</a>"; }
            }
            ?>
        </div>
    </main>

</body>
</html>