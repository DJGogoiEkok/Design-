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
        $name = $_POST['name'] ?? '';
        $role = $_POST['role'] ?? '';
        $type = $_POST['type'] ?? 'team';
        
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/team_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $orig_name = basename($_FILES['image']['name']);
            $orig_name = preg_replace('/[^a-zA-Z0-9._-]/', '', $orig_name);
            $orig_name = preg_replace('/\.+/', '.', $orig_name);
            $file_name = time() . '_' . $orig_name;
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/team_uploads/' . $file_name;
            }
        }
        
        $stmt = $db->prepare("INSERT INTO team_members (name, role, image_path, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $role, $image_path, $type]);
        
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'] ?? '';
        $role = $_POST['role'] ?? '';
        
        $image_query_part = "";
        $params = [$name, $role];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/team_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $orig_name = basename($_FILES['image']['name']);
            $orig_name = preg_replace('/[^a-zA-Z0-9._-]/', '', $orig_name);
            $orig_name = preg_replace('/\.+/', '.', $orig_name);
            $file_name = time() . '_' . $orig_name;
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/team_uploads/' . $file_name;
                $image_query_part = ", image_path = ?";
                $params[] = $image_path;
            }
        }
        
        $params[] = $id;
        
        $stmt = $db->prepare("UPDATE team_members SET name = ?, role = ? $image_query_part WHERE id = ?");
        $stmt->execute($params);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'upload_background') {
        // Handle background image upload
        if (!isset($_FILES['background_image'])) {
            $_SESSION['error'] = 'No file uploaded';
        } elseif ($_FILES['background_image']['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds server limit (php.ini upload_max_filesize)',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
                UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Server error: no temp directory',
                UPLOAD_ERR_CANT_WRITE => 'Server error: cannot write file',
            ];
            $_SESSION['error'] = $upload_errors[$_FILES['background_image']['error']] ?? 'Unknown upload error';
        } else {
            $upload_dir = '../images/gallery/backgrounds/';

            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Validate file type (images and videos)
            $file_type = mime_content_type($_FILES['background_image']['tmp_name']);
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm', 'video/ogg'];
            if (!in_array($file_type, $allowed_types)) {
                $_SESSION['error'] = 'Only JPG, PNG, WebP images or MP4, WebM, OGG videos are allowed. Received: ' . $file_type;
            } else if ($_FILES['background_image']['size'] > 52428800) { // 50MB for videos
                $_SESSION['error'] = 'File must be less than 50MB';
            } else {
                // Generate safe filename
                $ext = pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION);
                $file_name = 'background_' . time() . '.' . $ext;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['background_image']['tmp_name'], $target_file)) {
                    $image_path = 'images/gallery/backgrounds/' . $file_name;
                    $stmt = $db->prepare("UPDATE gallery_settings SET background_image_path = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
                    $stmt->execute([$image_path]);
                    $_SESSION['success'] = 'Background image updated successfully';
                } else {
                    $_SESSION['error'] = 'Error uploading file. Check directory permissions.';
                }
            }
        }
        header('Location: team.php');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'remove_background') {
        // Remove background image
        $stmt = $db->prepare("UPDATE gallery_settings SET background_image_path = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
        $stmt->execute();
        $_SESSION['success'] = 'Background image removed';
        header('Location: team.php');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_gallery') {
        // Handle gallery photo updates
        $photo_ids = $_POST['photo_ids'] ?? [];
        $labels = $_POST['labels'] ?? [];
        $positions_x = $_POST['positions_x'] ?? [];
        $positions_y = $_POST['positions_y'] ?? [];
        $orders = $_POST['orders'] ?? [];

        // Check if data received
        if (empty($photo_ids)) {
            $_SESSION['error'] = 'No photos found in form. Please reload the page.';
            header('Location: team.php');
            exit;
        }

        $error = false;

        // Validate inputs
        foreach ($photo_ids as $i => $id) {
            $label = trim($labels[$i] ?? '');
            $x = intval($positions_x[$i] ?? 0);
            $y = intval($positions_y[$i] ?? 0);
            $order = intval($orders[$i] ?? 0);

            if (empty($label) || strlen($label) > 50) {
                $_SESSION['error'] = 'Labels are required and must be under 50 characters';
                $error = true;
                break;
            }

            if ($x < 0 || $x > 1000 || $y < 0 || $y > 800) {
                $_SESSION['error'] = 'Positions must be X: 0-1000, Y: 0-800';
                $error = true;
                break;
            }

            if ($order < 1 || $order > 8) {
                $_SESSION['error'] = 'Order must be between 1 and 8';
                $error = true;
                break;
            }
        }

        // Check for duplicate orders
        if (!$error) {
            $orders_array = array_map('intval', $orders);
            if (count($orders_array) !== count(array_unique($orders_array))) {
                $_SESSION['error'] = 'Each photo must have a unique order number';
                $error = true;
            }
        }

        // Update database if no errors
        if (!$error) {
            try {
                $photo_upload_dir = '../images/team/';
                if (!is_dir($photo_upload_dir)) {
                    mkdir($photo_upload_dir, 0755, true);
                }

                foreach ($photo_ids as $i => $id) {
                    $new_image_path = null;

                    // Check if a new photo was uploaded for this slot
                    if (isset($_FILES['photo_images']['name'][$i]) && $_FILES['photo_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_type = mime_content_type($_FILES['photo_images']['tmp_name'][$i]);
                        if (in_array($file_type, ['image/jpeg', 'image/png', 'image/webp'])) {
                            $ext = pathinfo($_FILES['photo_images']['name'][$i], PATHINFO_EXTENSION);
                            $file_name = 'gallery_' . $id . '_' . time() . '.' . $ext;
                            $target = $photo_upload_dir . $file_name;
                            if (move_uploaded_file($_FILES['photo_images']['tmp_name'][$i], $target)) {
                                $new_image_path = 'images/team/' . $file_name;
                            }
                        }
                    }

                    if ($new_image_path) {
                        $stmt = $db->prepare("
                            UPDATE gallery_photos
                            SET photo_image = ?, photo_label = ?, position_x = ?, position_y = ?, gallery_order = ?
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            $new_image_path,
                            trim($labels[$i] ?? ''),
                            intval($positions_x[$i] ?? 0),
                            intval($positions_y[$i] ?? 0),
                            intval($orders[$i] ?? 0),
                            $id
                        ]);
                    } else {
                        $stmt = $db->prepare("
                            UPDATE gallery_photos
                            SET photo_label = ?, position_x = ?, position_y = ?, gallery_order = ?
                            WHERE id = ?
                        ");
                        $stmt->execute([
                            trim($labels[$i] ?? ''),
                            intval($positions_x[$i] ?? 0),
                            intval($positions_y[$i] ?? 0),
                            intval($orders[$i] ?? 0),
                            $id
                        ]);
                    }
                }
                $_SESSION['success'] = 'Gallery photos updated successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error updating photos: ' . $e->getMessage();
            }
        }

        header('Location: team.php');
        exit;
    }
}

// Fetch team members
$stmt = $db->query("SELECT * FROM team_members WHERE type = 'team'");
$team = $stmt->fetchAll();

// Fetch partners
$stmt = $db->query("SELECT * FROM team_members WHERE type = 'partner'");
$partners = $stmt->fetchAll();

// Fetch gallery settings
$stmt = $db->query("SELECT * FROM gallery_settings WHERE id = 1");
$gallery_settings = $stmt->fetch();

// Fetch gallery photos
$stmt = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Team & Partners</title>
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
                    <a href="hero.php" class="list-group-item list-group-item-action">Hero Section Images</a>
                    <a href="team.php" class="list-group-item list-group-item-action active">Our Team & Partners</a>
                    <a href="gallery.php" class="list-group-item list-group-item-action">Projects / At Work Gallery</a>
                    <a href="awards.php" class="list-group-item list-group-item-action">Awards</a>
                    <a href="applications.php" class="list-group-item list-group-item-action">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Team & Partners</h2>
                
                <div class="card mb-4">
                    <div class="card-header">Add New Member / Partner</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input class="form-control" type="text" name="role">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type">
                                    <option value="team">Our Team</option>
                                    <option value="partner">Partner Associate</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Photo (Optional)</label>
                                <input class="form-control" type="file" name="image" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h4>Our Team</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Name/Role</th><th>Photo</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($team as $m): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($m['name']) ?></strong><br><small><?= htmlspecialchars($m['role']) ?></small></td>
                                    <td><?php if($m['image_path']): ?><img class="img-thumbnail-popup" src="../<?= htmlspecialchars($m['image_path']) ?>" height="40"><?php endif; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $m['id'] ?>">Edit</button>
                                        <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-sm btn-danger">X</button></form>
                                    </td>
                                </tr>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Team Member</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <input class="form-control" type="text" name="role" value="<?= htmlspecialchars($m['role']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Photo (Leave blank to keep current)</label>
                                                <input class="form-control" type="file" name="image" accept="image/*">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Partner Associates</h4>
                        <table class="table table-bordered">
                            <thead><tr><th>Name/Role</th><th>Photo</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($partners as $m): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($m['name']) ?></strong><br><small><?= htmlspecialchars($m['role']) ?></small></td>
                                    <td><?php if($m['image_path']): ?><img class="img-thumbnail-popup" src="../<?= htmlspecialchars($m['image_path']) ?>" height="40"><?php endif; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $m['id'] ?>">Edit</button>
                                        <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-sm btn-danger">X</button></form>
                                    </td>
                                </tr>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Partner Associate</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <input class="form-control" type="text" name="role" value="<?= htmlspecialchars($m['role']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Photo (Leave blank to keep current)</label>
                                                <input class="form-control" type="file" name="image" accept="image/*">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Gallery Background</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <div id="background-preview" class="mb-3">
                                    <?php if ($gallery_settings && !empty($gallery_settings['background_image_path'])): ?>
                                        <?php $bg_path = '../' . htmlspecialchars($gallery_settings['background_image_path']); ?>
                                        <?php $is_video = preg_match('/\.(mp4|webm|ogg)$/i', $bg_path); ?>
                                        <?php if ($is_video): ?>
                                            <video style="max-width: 300px; max-height: 200px; border-radius: 8px; background: #000;" muted autoplay>
                                                <source src="<?php echo $bg_path; ?>" type="video/<?php echo pathinfo($bg_path, PATHINFO_EXTENSION); ?>">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <img src="<?php echo $bg_path; ?>"
                                                 alt="Gallery Background" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
                                        <?php endif; ?>
                                        <p class="mt-2 text-muted">Current background (<?php echo $is_video ? 'Video' : 'Image'; ?>)</p>
                                    <?php else: ?>
                                        <p class="text-muted">No background image or video set</p>
                                    <?php endif; ?>
                                </div>

                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="upload_background">
                                    <div class="mb-3">
                                        <label for="background_image" class="form-label">Upload New Background</label>
                                        <input type="file" class="form-control" id="background_image" name="background_image"
                                               accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg" required>
                                        <small class="form-text text-muted">Images: JPG, PNG, WebP | Videos: MP4, WebM, OGG. Max 50MB.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload Background</button>

                                    <?php if ($gallery_settings && !empty($gallery_settings['background_image_path'])): ?>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeBackground()">Remove Background</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Gallery Photos (8 Total)</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="gallery-photos-form" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_gallery">

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 80px;">Thumbnail</th>
                                                    <th style="width: 200px;">Label</th>
                                                    <th style="width: 120px;">Position X</th>
                                                    <th style="width: 120px;">Position Y</th>
                                                    <th style="width: 100px;">Order</th>
                                                    <th style="width: 180px;">Change Photo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($gallery_photos as $index => $photo): ?>
                                                <tr>
                                                    <td>
                                                        <img src="../<?php echo htmlspecialchars($photo['photo_image']); ?>"
                                                             alt="Photo <?php echo $index + 1; ?>"
                                                             style="max-width: 50px; max-height: 50px; border-radius: 4px;">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="labels[]"
                                                               value="<?php echo htmlspecialchars($photo['photo_label']); ?>"
                                                               maxlength="50" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                               name="positions_x[]"
                                                               value="<?php echo $photo['position_x']; ?>"
                                                               min="0" max="1000" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                               name="positions_y[]"
                                                               value="<?php echo $photo['position_y']; ?>"
                                                               min="0" max="800" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                               name="orders[]"
                                                               value="<?php echo $photo['gallery_order']; ?>"
                                                               min="1" max="8" required>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="photo_ids[]" value="<?php echo $photo['id']; ?>">
                                                        <input type="file" class="form-control form-control-sm"
                                                               name="photo_images[]"
                                                               accept="image/jpeg,image/png,image/webp">
                                                        <small class="text-muted">Leave empty to keep current</small>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success">Save Gallery</button>
                                        <button type="reset" class="btn btn-outline-secondary ms-2">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <form id="remove-background-form" method="POST" style="display: none;">
            <input type="hidden" name="action" value="remove_background">
        </form>

        <script>
        function removeBackground() {
            if (confirm('Are you sure you want to remove the background image?')) {
                document.getElementById('remove-background-form').submit();
            }
        }
        </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>
</body>
</html>
