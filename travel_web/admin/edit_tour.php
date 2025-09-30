<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}

require_once '../connect.php';

// Khởi tạo tour với các giá trị rỗng
$edit_mode = false;
$tour = [
    'tour_id' => '', 'name' => '', 'description' => '', 'duration' => '', 'pax' => '',
    'location' => '', 'price' => '', 'image_url' => ''
];
$message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $edit_mode = true;
    $tour_id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();
    } else {
        header('Location: manage_tours.php?error=Không tìm thấy tour');
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form (đã bỏ rating và reviews)
    $name = $_POST['name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $pax = (int)$_POST['pax'];
    $location = $_POST['location'];
    $price = (float)$_POST['price'];
    $current_image = $_POST['current_image'];
    $image_method = $_POST['image_method'] ?? 'upload';

    $image_path_for_db = $current_image;

    if ($image_method === 'url') {
        $image_url_input = $_POST['image_url_input'] ?? '';
        if (!empty($image_url_input) && filter_var($image_url_input, FILTER_VALIDATE_URL)) {
            $image_path_for_db = $image_url_input;
            if ($edit_mode && !empty($current_image) && strpos($current_image, 'assets/images/') === 0 && file_exists('../' . $current_image)) {
                unlink('../' . $current_image);
            }
        }
    } else if ($image_method === 'upload') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../assets/images/";
            if (!is_dir($target_dir) || !is_writable($target_dir)) {
                 $message = "<div class='message error'>Lỗi: Thư mục `{$target_dir}` không tồn tại hoặc không có quyền ghi.</div>";
            } else {
                $image_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path_for_db = 'assets/images/' . $image_name;
                    if ($edit_mode && !empty($current_image) && strpos($current_image, 'assets/images/') === 0 && file_exists('../' . $current_image)) {
                        unlink('../' . $current_image);
                    }
                } else {
                    $message = "<div class='message error'>Có lỗi khi tải file lên.</div>";
                }
            }
        }
    }

    if (empty($message)) {
        if ($edit_mode) {
            $sql = "UPDATE tours SET name=?, description=?, duration=?, pax=?, location=?, price=?, image_url=? WHERE tour_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisdsi", $name, $description, $duration, $pax, $location, $price, $image_path_for_db, $tour_id);
        } else {
            $sql = "INSERT INTO tours (name, description, duration, pax, location, price, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisds", $name, $description, $duration, $pax, $location, $price, $image_path_for_db);
        }

        if ($stmt->execute()) {
            header('Location: manage_tours.php?success=Thao tác thành công');
            exit();
        } else {
            $message = "<div class='message error'>Lỗi khi lưu vào CSDL: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Sửa Tour' : 'Thêm Tour Mới' ?> - Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .main-content { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; margin-left: 280px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .full-width { grid-column: 1 / -1; }
        .form-group label { font-weight: 600; color: #555; display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .image-preview { margin-top: 10px; }
        .image-preview img { max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover; }
        .form-actions { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; text-align: right; }
        .btn-submit { padding: 12px 25px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem;}
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary-color); text-decoration: none; }
        .image-method-choice label { display: inline-block; margin-right: 15px; font-weight: normal; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fa-solid fa-plane-up"></i> TravelWorldAdmin</h2></div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
            <li><a href="manage_tours.php" class="active"><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
            <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
            <li><a href="manage_destinations.php"><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
            <li><a href="manage_contacts.php"><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
        </ul>
        <div class="sidebar-footer"><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></div>
    </aside>

    <main class="main-content">
        <a href="manage_tours.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách Tour</a>
        <h1><?= $edit_mode ? 'Sửa thông tin Tour' : 'Thêm Tour Mới' ?></h1>

        <?php if(!empty($message)) echo $message; ?>

        <form action="edit_tour.php<?= $edit_mode ? '?id=' . $tour_id : '' ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($tour['image_url']) ?>">
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name">Tên Tour</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($tour['name']) ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" required><?= htmlspecialchars($tour['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="location">Địa điểm</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($tour['location']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="duration">Thời gian (ví dụ: 3 ngày 2 đêm)</label>
                    <input type="text" id="duration" name="duration" value="<?= htmlspecialchars($tour['duration']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Giá (VNĐ)</label>
                    <input type="number" id="price" name="price" value="<?= htmlspecialchars($tour['price']) ?>" step="1000" required>
                </div>

                <div class="form-group">
                    <label for="pax">Số khách tối đa (pax)</label>
                    <input type="number" id="pax" name="pax" value="<?= htmlspecialchars($tour['pax']) ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Ảnh đại diện</label>
                    <div class="image-method-choice">
                        <input type="radio" name="image_method" value="upload" id="method_upload" checked>
                        <label for="method_upload">Tải ảnh lên</label>
                        <input type="radio" name="image_method" value="url" id="method_url">
                        <label for="method_url">Dùng liên kết ảnh (URL)</label>
                    </div>
                </div>

                <div class="form-group full-width" id="upload_field">
                    <label for="image_upload_input">Chọn tệp ảnh (để trống nếu không muốn thay đổi)</label>
                    <input type="file" id="image_upload_input" name="image" accept="image/*">
                </div>

                <div class="form-group full-width" id="url_field" style="display: none;">
                    <label for="image_url_input">Dán liên kết ảnh vào đây</label>
                    <input type="text" id="image_url_input" name="image_url_input" placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-group full-width">
                    <div class="image-preview" id="image_preview_container" style="<?php echo ($edit_mode && !empty($tour['image_url'])) ? '' : 'display: none;'; ?>">
                        <p>Xem trước ảnh:</p>
                        <img id="image_preview_img" 
                             src="<?php echo ($edit_mode && !empty($tour['image_url'])) ? (strpos($tour['image_url'], 'http') === 0 ? htmlspecialchars($tour['image_url']) : '../' . htmlspecialchars($tour['image_url'])) : '#'; ?>" 
                             alt="Ảnh xem trước">
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $edit_mode ? 'Cập nhật Tour' : 'Thêm Tour' ?></button>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy các element cần thiết
            const methodUpload = document.getElementById('method_upload');
            const methodUrl = document.getElementById('method_url');
            const uploadField = document.getElementById('upload_field');
            const urlField = document.getElementById('url_field');
            const uploadInput = document.getElementById('image_upload_input');
            const urlInput = document.getElementById('image_url_input');
            const previewContainer = document.getElementById('image_preview_container');
            const previewImg = document.getElementById('image_preview_img');

            // Hàm ẩn/hiện ô nhập liệu
            function toggleImageMethod() {
                if (methodUpload.checked) {
                    uploadField.style.display = 'block';
                    urlField.style.display = 'none';
                } else {
                    uploadField.style.display = 'none';
                    urlField.style.display = 'block';
                }
            }
            methodUpload.addEventListener('change', toggleImageMethod);
            methodUrl.addEventListener('change', toggleImageMethod);
            toggleImageMethod(); // Chạy lần đầu

            // Hàm xem trước ảnh khi chọn file
            uploadInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Hàm xem trước ảnh khi dán link
            urlInput.addEventListener('input', function(event) {
                const url = event.target.value;
                if (url) {
                    previewImg.src = url;
                    previewContainer.style.display = 'block';
                } else {
                     previewContainer.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>