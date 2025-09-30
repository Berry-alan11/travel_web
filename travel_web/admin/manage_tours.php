<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}
require_once '../connect.php';

// Logic phân trang và tìm kiếm (không đổi)
$records_per_page = 9;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql_where = '';
$params = [];
$param_types = '';
if (!empty($search_term)) {
    $sql_where = "WHERE name LIKE ? OR location LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term];
    $param_types = "ss";
}
$total_records_sql = "SELECT COUNT(*) FROM tours " . $sql_where;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();
$data_sql = "
    SELECT *
    FROM tours
    $sql_where
    ORDER BY tour_id DESC
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
$tours_result = $stmt_data->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tour - Admin</title>
    <link rel="shortcut icon" href="../favicon.svg" type="image/svg+xml">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="stylesheet" href="tour_card_style.css">
    <link rel="stylesheet" href="admin_style.css"> 
    
    <style>
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-form { display: flex; }
        .search-form input[type="text"] { padding: 10px; border: 1px solid var(--border-color); border-radius: 5px 0 0 5px; width: 300px; }
        .search-form button, .btn-add { padding: 10px 15px; border: none; background-color: var(--primary-color); color: white; cursor: pointer; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; }
        .search-form button { border-radius: 0 5px 5px 0; }
        .btn-add { border-radius: 5px; }
        .btn-add i { margin-right: 5px; }
        .pagination { display: flex; justify-content: center; margin-top: 20px; }
        .pagination a { color: var(--primary-color); padding: 8px 16px; text-decoration: none; border: 1px solid var(--border-color); margin: 0 4px; border-radius: 5px; }
        .pagination a.current { background-color: var(--primary-color); color: white; border: 1px solid var(--primary-color); }
        .pagination a:hover:not(.current) { background-color: #f4f4f4; }
        .admin-actions-in-card { display: flex; gap: 15px; }
        .admin-actions-in-card a { font-size: 1.4rem; color: white; transition: transform 0.2s; }
        .admin-actions-in-card a:hover { transform: scale(1.2); }
        .admin-actions-in-card .delete { color: #ffdddd; }
        .admin-actions-in-card .delete:hover { color: #ff4d4d; }
    </style>
</head>
<body>
    
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2></div>
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
        <div class="sidebar-footer"><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Quản lý Tour</h1>
            <div><span>Xin chào, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span></div>
        </header>

        <div class="toolbar">
            <a href="edit_tour.php" class="btn-add"><i class="fa-solid fa-plus"></i> Thêm Tour Mới</a>
            <form action="manage_tours.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Tìm theo tên tour, địa điểm..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <section class="tour-list-container">
            <ul class="package-list">
            <?php
                if ($tours_result && $tours_result->num_rows > 0) {
                    while($row = $tours_result->fetch_assoc()) {
                        $tour_id = $row['tour_id'];
                        $title = htmlspecialchars($row['name']);
                        $desc = htmlspecialchars($row['description']);
                        if (strlen($desc) > 120) { $desc = substr($desc, 0, 120) . "..."; }
                        $duration = htmlspecialchars($row['duration']);
                        $pax = (int)$row['pax'];
                        $location = htmlspecialchars($row['location']);
                        $image_path = (strpos($row['image_url'], 'http') === 0) ? htmlspecialchars($row['image_url']) : '../' . htmlspecialchars($row['image_url']);
                        $price = number_format($row['price'], 0, ',', '.') . ' vnđ';

                        echo '<li>
                            <div class="package-card">
                                <figure class="card-banner">
                                    <img src="' . $image_path . '" alt="' . $title . '" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <h3 class="h3 card-title">' . $title . '</h3>
                                    <p class="card-text">' . $desc . '</p>
                                    <ul class="card-meta-list">
                                        <li class="card-meta-item">
                                            <div class="meta-box"><ion-icon name="time"></ion-icon><p class="text">' . $duration . '</p></div>
                                        </li>
                                        <li class="card-meta-item">
                                            <div class="meta-box"><ion-icon name="people"></ion-icon><p class="text">pax: ' . $pax . '</p></div>
                                        </li>
                                        <li class="card-meta-item">
                                            <div class="meta-box"><ion-icon name="location"></ion-icon><p class="text">' . $location . '</p></div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-price">
                                    <p class="price" style="margin-bottom: 15px;">' . $price . '<span>/ 1 người</span></p>
                                    <div class="admin-actions-in-card" style="justify-content: center;">
                                        <a href="edit_tour.php?id=' . $tour_id . '" title="Sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="delete_tour.php?id=' . $tour_id . '" class="delete" title="Xóa" onclick="return confirm(\'Bạn có chắc muốn xóa tour &quot;' . $title . '&quot;?\')"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </li>';
                    }
                } else {
                    echo "<p style='text-align:center; width: 100%;'>Không tìm thấy tour nào.</p>";
                }
                $stmt_data->close();
                $conn->close();
            ?>
            </ul>
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