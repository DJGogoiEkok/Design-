<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $order_index = $_POST['order_index'] ?? 0;
        
        // Handle file upload
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/hero_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['hero_image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/hero_uploads/' . $file_name;
                $stmt = $db->prepare("INSERT INTO hero_images (image_path, order_index) VALUES (?, ?)");
                $stmt->execute([$image_path, $order_index]);
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM hero_images WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_text') {
        $update_stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $update_stmt->execute([$_POST['hero_kicker'], 'hero_kicker']);
        $update_stmt->execute([$_POST['hero_title'], 'hero_title']);
        $update_stmt->execute([$_POST['hero_lede'], 'hero_lede']);
        $update_stmt->execute([$_POST['hero_btn_text'], 'hero_btn_text']);
        $update_stmt->execute([$_POST['hero_btn_link'], 'hero_btn_link']);
        $settings_updated = true;
    }
}

// Fetch settings
$settings = [];
$settings_query = $db->query("SELECT * FROM site_settings");
while ($row = $settings_query->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch all hero images
$stmt = $db->query("SELECT * FROM hero_images ORDER BY order_index ASC");
$hero_images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hero Media</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">D+ Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="hero.php" class="list-group-item list-group-item-action active">Hero Section Media</a>
                    <a href="team.php" class="list-group-item list-group-item-action">Our Team & Partners</a>
                    <a href="gallery.php" class="list-group-item list-group-item-action">Projects / At Work Gallery</a>
                    <a href="awards.php" class="list-group-item list-group-item-action">Awards</a>
                    <a href="applications.php" class="list-group-item list-group-item-action">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Hero Section</h2>
                
                <?php if (!empty($settings_updated)): ?>
                    <div class="alert alert-success">Hero text settings updated successfully!</div>
                <?php endif; ?>

                <!-- Edit Hero Texts Form -->
                <div class="card mb-4">
                    <div class="card-header">Edit Hero Texts</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_text">
                            <div class="mb-3">
                                <label class="form-label">Subtitle (Kicker)</label>
                                <input class="form-control" type="text" name="hero_kicker" value="<?= htmlspecialchars($settings['hero_kicker'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Main Title</label>
                                <input class="form-control" type="text" name="hero_title" value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Paragraph Text (Lede)</label>
                                <textarea class="form-control" name="hero_lede" rows="4" required><?= htmlspecialchars($settings['hero_lede'] ?? '') ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input class="form-control" type="text" name="hero_btn_text" value="<?= htmlspecialchars($settings['hero_btn_text'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Button Link</label>
                                    <input class="form-control" type="text" name="hero_btn_link" value="<?= htmlspecialchars($settings['hero_btn_link'] ?? '') ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Texts</button>
                        </form>
                    </div>
                </div>
                <!-- Add New Image Form -->
                <div class="card mb-4">
                    <div class="card-header">Add New Hero Media</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label class="form-label">Upload Media (Image or MP4 Video)</label>
                                <input class="form-control" type="file" name="hero_image" accept="image/*,video/mp4,video/webm,video/ogg" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Order Index</label>
                                <input class="form-control" type="number" name="order_index" value="0">
                                <small class="text-muted">Lower numbers appear first.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Media</button>
                        </form>
                    </div>
                </div>

                <!-- List Current Images -->
                <div class="card">
                    <div class="card-header">Current Images</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Order</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hero_images as $img): ?>
                                <tr>
                                    <td><?= $img['id'] ?></td>
                                    <td>
                                        <?php 
                                        $ext = strtolower(pathinfo($img['image_path'], PATHINFO_EXTENSION));
                                        $is_video = in_array($ext, ['mp4', 'webm', 'ogg']);
                                        ?>
                                        <?php if ($is_video): ?>
                                            <video src="../<?= htmlspecialchars($img['image_path']) ?>" style="height: 50px;" muted autoplay loop></video>
                                        <?php else: ?>
                                            <img class="img-thumbnail-popup" src="../<?= htmlspecialchars($img['image_path']) ?>" alt="Hero" style="height: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $img['order_index'] ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $img['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this media?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>
</body>
</html>
