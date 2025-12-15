<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 0 && $_SESSION['user_role'] != 2)) {
    header('Location: ../index.php');
    die('Bạn không có quyền truy cập trang này.');
}

require_once '../connect.php';

// Hàm tạo slug (ví dụ: "Đà Nẵng" -> "da-nang")
function create_slug($string){
    $search = array('á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ','ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự','ý','ỳ','ỷ','ỹ','ỵ');
    $replace = array('a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y');
    $string = str_replace($search, $replace, strtolower($string));
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/([\s-]+)/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

$edit_mode = false;
$destination = ['destination_id' => '', 'name' => '', 'country' => '', 'description' => '', 'image_url' => '', 'section_1' => '', 'section_2' => '', 'best_time_to_visit' => '', 'map_embed' => '', 'image_gallery' => '', 'slug' => ''];
$message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $edit_mode = true;
    $destination_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) { $destination = $result->fetch_assoc(); } else { header('Location: manage_destinations.php?error=Không tìm thấy địa điểm'); exit(); }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu
    $name = $_POST['name'];
    $country = $_POST['country'];
    $description = $_POST['description'];
    $section_1 = $_POST['section_1'];
    $section_2 = $_POST['section_2'];
    $best_time_to_visit = $_POST['best_time_to_visit'];
    $map_embed = $_POST['map_embed'];
    $slug = !empty($_POST['slug']) ? create_slug($_POST['slug']) : create_slug($name);
    $current_image = $_POST['current_image'];
    $current_gallery = !empty($_POST['current_gallery']) ? explode(',', $_POST['current_gallery']) : [];

    // Xử lý xóa ảnh trong gallery
    if(isset($_POST['delete_gallery'])) {
        foreach($_POST['delete_gallery'] as $image_to_delete) {
            if (($key = array_search($image_to_delete, $current_gallery)) !== false) {
                if(file_exists('../' . $image_to_delete)) unlink('../' . $image_to_delete);
                unset($current_gallery[$key]);
            }
        }
    }

    // Xử lý ảnh đại diện
    $image_path_for_db = $current_image;
    if (($_POST['image_method'] ?? 'upload') === 'url' && !empty($_POST['image_url_input'])) {
        $image_path_for_db = $_POST['image_url_input'];
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name)) {
            $image_path_for_db = 'assets/images/' . $image_name;
        }
    }

    // Xử lý upload ảnh gallery mới
    $new_gallery_paths = [];
    if (isset($_FILES['gallery_images'])) {
        $target_dir = "../assets/images/destinations_gallery/";
        foreach ($_FILES['gallery_images']['name'] as $key => $name) {
            if ($_FILES['gallery_images']['error'][$key] == 0) {
                $file_name = time() . '_' . $name;
                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $target_dir . $file_name)) {
                    $new_gallery_paths[] = 'assets/images/destinations_gallery/' . $file_name;
                }
            }
        }
    }

    // Gộp gallery cũ và mới, chuyển thành chuỗi
    $final_gallery = implode(',', array_merge($current_gallery, $new_gallery_paths));

    // Update hoặc Insert
    if ($edit_mode) {
        $sql = "UPDATE destinations SET name=?, country=?, description=?, image_url=?, section_1=?, section_2=?, best_time_to_visit=?, map_embed=?, image_gallery=?, slug=? WHERE destination_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $name, $country, $description, $image_path_for_db, $section_1, $section_2, $best_time_to_visit, $map_embed, $final_gallery, $slug, $destination_id);
    } else {
        $sql = "INSERT INTO destinations (name, country, description, image_url, section_1, section_2, best_time_to_visit, map_embed, image_gallery, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $name, $country, $description, $image_path_for_db, $section_1, $section_2, $best_time_to_visit, $map_embed, $final_gallery, $slug);
    }
    
    if ($stmt->execute()) {
        header('Location: manage_destinations.php?success=Thao tác thành công');
        exit();
    } else { $message = "<div class='message error'>Lỗi khi lưu vào CSDL: " . $stmt->error . "</div>"; }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Sửa Địa điểm' : 'Thêm Địa điểm' ?></title>
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        .main-content { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 8px; margin-left: 280px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: 600; display: block; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .image-preview img, .gallery-preview-item img { max-width: 200px; max-height: 200px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover; }
        .gallery-preview { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px; }
        .gallery-preview-item { position: relative; }
        .gallery-preview-item .delete-check { position: absolute; top: 5px; right: 5px; }
        .form-actions { margin-top: 20px; text-align: right; }
        .btn-submit { padding: 12px 25px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; }
        .back-link { display: inline-block; margin-bottom: 20px; color: var(--primary-color); text-decoration: none; }
        .message.error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h2><i class="fa-solid fa-plane-up"></i> TravelWorld</h2></div>
        <ul class="sidebar-nav">
             <li><a href="index.php"><i class="fa-solid fa-house"></i> Tổng Quan</a></li>
            <li><a href="manage_tours.php"><i class="fa-solid fa-map-marked-alt"></i> Quản lý Tour</a></li>
            <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> Quản lý Booking</a></li>
            <li><a href="manage_destinations.php" class="active"><i class="fa-solid fa-mountain-city"></i> Quản lý Địa điểm</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a></li>
            <li><a href="manage_contacts.php"><i class="fa-solid fa-envelope"></i> Quản lý Liên hệ</a></li>
        </ul>
        <div class="sidebar-footer"><a href="../php/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></div>
    </aside>

    <main class="main-content">
        <a href="manage_destinations.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
        <h1><?= $edit_mode ? 'Sửa Địa điểm' : 'Thêm Địa điểm Mới' ?></h1>
        <?php if(!empty($message)) echo $message; ?>
        <form action="edit_destination.php<?= $edit_mode ? '?id=' . $destination_id : '' ?>" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="name">Tên Địa điểm</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($destination['name']) ?>" required>
            </div>
             <div class="form-group">
                <label for="country">Quốc gia</label>
                <input type="text" id="country" name="country" value="<?= htmlspecialchars($destination['country']) ?>">
            </div>
            <div class="form-group">
                <label for="slug">Slug (URL thân thiện, để trống sẽ tự tạo)</label>
                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($destination['slug']) ?>">
            </div>
            <div class="form-group">
                <label for="description">Mô tả chi tiết</label>
                <textarea id="description" name="description"><?= htmlspecialchars($destination['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="best_time_to_visit">Thời điểm đẹp nhất để tham quan</label>
                <input type="text" id="best_time_to_visit" name="best_time_to_visit" value="<?= htmlspecialchars($destination['best_time_to_visit']) ?>">
            </div>

            <div class="form-group">
                 <label>Ảnh đại diện</label>
                 <input type="hidden" name="current_image" value="<?= htmlspecialchars($destination['image_url']) ?>">
                 <div class="image-method-choice">
                     <input type="radio" name="image_method" value="upload" id="method_upload" checked> <label for="method_upload">Tải ảnh lên</label>
                     <input type="radio" name="image_method" value="url" id="method_url"> <label for="method_url">Dùng link</label>
                 </div>
                 <div id="upload_field"><input type="file" name="image" accept="image/*"></div>
                 <div id="url_field" style="display: none;"><input type="text" name="image_url_input" placeholder="https://..."></div>
                 <?php if ($edit_mode && !empty($destination['image_url'])): ?>
                    <div class="image-preview"><p>Ảnh hiện tại:</p><img src="<?= strpos($destination['image_url'], 'http') === 0 ? htmlspecialchars($destination['image_url']) : '../' . htmlspecialchars($destination['image_url']) ?>"></div>
                 <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gallery_images">Thư viện ảnh (có thể chọn nhiều ảnh)</label>
                <input type="file" id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                <?php if ($edit_mode && !empty($destination['image_gallery'])): 
                    $gallery_paths = explode(',', $destination['image_gallery']);
                ?>
                    <p>Các ảnh hiện tại trong thư viện (chọn để xóa):</p>
                    <div class="gallery-preview">
                        <input type="hidden" name="current_gallery" value="<?= htmlspecialchars($destination['image_gallery']) ?>">
                        <?php foreach($gallery_paths as $path): ?>
                            <div class="gallery-preview-item">
                                <img src="../<?= htmlspecialchars($path) ?>">
                                <input type="checkbox" name="delete_gallery[]" value="<?= htmlspecialchars($path) ?>" class="delete-check" title="Chọn để xóa ảnh này">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="map_embed">Mã nhúng Google Map</label>
                <textarea id="map_embed" name="map_embed"><?= htmlspecialchars($destination['map_embed']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="section_1">Nội dung Section 1 (định dạng: caption||image.jpg)</label>
                <textarea id="section_1" name="section_1"><?= htmlspecialchars($destination['section_1']) ?></textarea>
            </div>
             <div class="form-group">
                <label for="section_2">Nội dung Section 2 (định dạng: caption||image.jpg)</label>
                <textarea id="section_2" name="section_2"><?= htmlspecialchars($destination['section_2']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $edit_mode ? 'Cập nhật' : 'Thêm Địa điểm' ?></button>
            </div>
        </form>
    </main>
    <script>
        // JS để chuyển đổi giữa Tải ảnh và dùng Link
        document.getElementById('method_upload').addEventListener('change', (e) => {
            if(e.target.checked) {
                document.getElementById('upload_field').style.display = 'block';
                document.getElementById('url_field').style.display = 'none';
            }
        });
        document.getElementById('method_url').addEventListener('change', (e) => {
            if(e.target.checked) {
                document.getElementById('upload_field').style.display = 'none';
                document.getElementById('url_field').style.display = 'block';
            }
        });
    </script>
</body>
</html>