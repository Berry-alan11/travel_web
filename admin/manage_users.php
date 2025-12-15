<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 0) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}
require_once '../connect.php';

// Logic phân trang và tìm kiếm
$records_per_page = 10;
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

// Lấy tổng số người dùng
$total_records_sql = "SELECT COUNT(*) FROM users " . $sql_where;
$stmt_total = $conn->prepare($total_records_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$stmt_total->close();

// Lấy dữ liệu người dùng cho trang hiện tại
$data_sql = "SELECT user_id, name, email, phone, user_role FROM users $sql_where ORDER BY user_id DESC LIMIT ? OFFSET ?";
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
$users_result = $stmt_data->get_result();

// Mảng định nghĩa vai trò
$role_map = [
    0 => ['text' => 'Admin', 'color' => '#e74c3c'],
    1 => ['text' => 'Người dùng', 'color' => '#3498db'],
    2 => ['text' => 'Nhà cung cấp dịch vụ', 'color' => '#3aa832']
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        .pagination a:hover:not(.current) { background-color: #f4f4f4; }
        .data-table-container { background-color: white; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .role-badge { padding: 5px 10px; color: white; border-radius: 15px; font-size: 0.8rem; font-weight: 600; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2></div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
            <li><a href="manage_tours.php"><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
            <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
            <li><a href="manage_destinations.php"><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>
            <li><a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
            <li><a href="manage_contacts.php"><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
        </ul>
        <div class="sidebar-footer"><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></div>
    </aside>

    <main class="main-content">
        <header class="header"><h1>Quản lý Người dùng</h1></header>
        <div class="toolbar">
            <form action="manage_users.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Tìm theo tên, email..." value="<?= htmlspecialchars($search_term) ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <section class="data-table-container">
            <table>
                <thead>
                    <tr><th>ID</th><th>Tên</th><th>Email</th><th>Điện thoại</th><th>Vai trò</th><th>Hành Động</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $users_result->fetch_assoc()): 
                        $role_info = $role_map[$row['user_role']] ?? ['text' => 'Không rõ', 'color' => '#7f8c8d'];
                    ?>
                        <tr>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><span class="role-badge" style="background-color: <?= $role_info['color'] ?>;"><?= $role_info['text'] ?></span></td>
                            <td class="actions">
                                <a href="edit_user.php?id=<?= $row['user_id'] ?>" title="Sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php if ($_SESSION['user_id'] != $row['user_id']): // Không cho phép admin tự xóa mình ?>
                                    <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="delete" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
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