<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}
require_once '../connect.php';

// Logic phân trang và tìm kiếm
$records_per_page = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql_where = '';
$params = [];
$param_types = '';
if (!empty($search_term)) {
    $sql_where = "WHERE name LIKE ? OR email LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term];
    $param_types = "ss";
}

// Lấy tổng số tin nhắn
$total_records_sql = "SELECT COUNT(*) FROM contacts " . $sql_where;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();

// Lấy dữ liệu tin nhắn cho trang hiện tại
$data_sql = "SELECT * FROM contacts $sql_where ORDER BY created_at DESC LIMIT ? OFFSET ?";
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
$contacts_result = $stmt_data->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Liên hệ - Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .toolbar { display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px; }
        .search-form { display: flex; }
        .search-form input[type="text"] { padding: 10px; border: 1px solid var(--border-color); border-radius: 5px 0 0 5px; width: 300px; }
        .search-form button { padding: 10px 15px; border: none; background-color: var(--primary-color); color: white; cursor: pointer; border-radius: 0 5px 5px 0; }
        .actions a { margin-right: 10px; color: var(--primary-color); text-decoration: none; }
        .actions a.delete { color: #e74c3c; }
        .pagination { display: flex; justify-content: center; margin-top: 20px; }
        .pagination a { color: var(--primary-color); padding: 8px 16px; text-decoration: none; border: 1px solid var(--border-color); margin: 0 4px; border-radius: 5px; }
        .pagination a.current { background-color: var(--primary-color); color: white; border: 1px solid var(--primary-color); }
        .data-table-container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
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
        <header class="header"><h1>Hòm thư Liên hệ</h1></header>
        <div class="toolbar">
            <form action="manage_contacts.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Tìm theo tên, email..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <section class="data-table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Tên</th><th>Email</th><th>Nội dung</th><th>Ngày gửi</th><th>Hành Động</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $contacts_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['contact_id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars(substr($row['message'], 0, 50)) . '...' ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></td>
                            <td class="actions">
                                <a href="view_contact.php?id=<?= $row['contact_id'] ?>" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                                <a href="delete_contact.php?id=<?= $row['contact_id'] ?>" class="delete" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search_term) ?>" class="<?= $i == $page ? 'current' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </main>
</body>
</html>