<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php'); die();
}
require_once '../connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php?error=ID không hợp lệ'); exit();
}
$user_id_to_edit = (int)$_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $id_card = $_POST['id_card'];
    $user_role = (int)$_POST['user_role'];

    // Ngăn admin tự tước quyền của chính mình
    if ($user_id_to_edit == $_SESSION['user_id'] && $user_role != 0) {
        $message = "<div class='message error'>Bạn không thể tự tước quyền Admin của chính mình.</div>";
    } else {
        $sql = "UPDATE users SET name=?, email=?, phone=?, id_card=?, user_role=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $name, $email, $phone, $id_card, $user_role, $user_id_to_edit);
        if ($stmt->execute()) {
            $message = "<div class='message success'>Cập nhật thông tin thành công!</div>";
        } else {
            $message = "<div class='message error'>Cập nhật thất bại: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: manage_users.php?error=Không tìm thấy người dùng'); exit();
}
$user = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Người dùng - Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .main-content { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; margin-left: 280px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: 600; display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-actions { margin-top: 20px; text-align: right; }
        .btn-submit { padding: 12px 25px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <aside class="sidebar">
        </aside>
    <main class="main-content">
        <a href="manage_users.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
        <h1>Sửa thông tin người dùng</h1>
        <?php if(!empty($message)) echo $message; ?>
        <form action="edit_user.php?id=<?= $user_id_to_edit ?>" method="POST">
            <div class="form-group">
                <label>User ID</label>
                <input type="text" value="<?= $user['user_id'] ?>" disabled>
            </div>
            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="form-group">
                <label for="id_card">CCCD</label>
                <input type="text" id="id_card" name="id_card" value="<?= htmlspecialchars($user['id_card']) ?>">
            </div>
            <div class="form-group">
                <label for="user_role">Vai trò</label>
                <select name="user_role" id="user_role">
                    <option value="1" <?= $user['user_role'] == 1 ? 'selected' : '' ?>>Người dùng</option>
					<option value="2" <?= $user['user_role'] == 2 ? 'selected' : '' ?>>Nhà cung cấp dịch vụ</option>
                    <option value="0" <?= $user['user_role'] == 0 ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit">Cập nhật</button>
            </div>
        </form>
    </main>
</body>
</html>