<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? 'projects';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/gallery_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/gallery_uploads/' . $file_name;
                $stmt = $db->prepare("INSERT INTO gallery (title, image_path, category) VALUES (?, ?, ?)");
                $stmt->execute([$title, $image_path, $category]);
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? 'projects';
        
        $image_query_part = "";
        $params = [$title, $category];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/gallery_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/gallery_uploads/' . $file_name;
                $image_query_part = ", image_path = ?";
                $params[] = $image_path;
            }
        }
        
        $params[] = $id;
        
        $stmt = $db->prepare("UPDATE gallery SET title = ?, category = ? $image_query_part WHERE id = ?");
        $stmt->execute($params);
    }
}

$stmt = $db->query("SELECT * FROM gallery WHERE category = 'projects'");
$projects = $stmt->fetchAll();

$stmt = $db->query("SELECT * FROM gallery WHERE category = 'team_at_work'");
$team_work = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Gallery</title>
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
                    <a href="team.php" class="list-group-item list-group-item-action">Our Team & Partners</a>
                    <a href="gallery.php" class="list-group-item list-group-item-action active">Projects / At Work Gallery</a>
                    <a href="awards.php" class="list-group-item list-group-item-action">Awards</a>
                    <a href="applications.php" class="list-group-item list-group-item-action">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Gallery</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label>Title (Optional)</label>
                                <input class="form-control" type="text" name="title">
                            </div>
                            <div class="mb-3">
                                <label>Category</label>
                                <select class="form-select" name="category">
                                    <option value="projects">Projects</option>
                                    <option value="team_at_work">Team At Work</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Image</label>
                                <input class="form-control" type="file" name="image" required>
                            </div>
                            <button class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
                <!-- Tables for projects and team at work ... -->
                <h4>Projects</h4>
                <table class="table table-bordered mb-4">
                    <thead><tr><th>Title</th><th>Image</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($projects as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><img class="img-thumbnail-popup" src="../<?= htmlspecialchars($item['image_path']) ?>" height="40"></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">Edit</button>
                                <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $item['id'] ?>"><button class="btn btn-sm btn-danger">X</button></form>
                            </td>
                        </tr>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Edit Project</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Title (Optional)</label>
                                        <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category">
                                            <option value="projects" <?= $item['category'] === 'projects' ? 'selected' : '' ?>>Projects</option>
                                            <option value="team_at_work" <?= $item['category'] === 'team_at_work' ? 'selected' : '' ?>>Team At Work</option>
                                        </select>
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
                <h4>Team At Work</h4>
                <table class="table table-bordered">
                    <thead><tr><th>Title</th><th>Image</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($team_work as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><img class="img-thumbnail-popup" src="../<?= htmlspecialchars($item['image_path']) ?>" height="40"></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">Edit</button>
                                <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $item['id'] ?>"><button class="btn btn-sm btn-danger">X</button></form>
                            </td>
                        </tr>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Edit Team At Work</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Title (Optional)</label>
                                        <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category">
                                            <option value="projects" <?= $item['category'] === 'projects' ? 'selected' : '' ?>>Projects</option>
                                            <option value="team_at_work" <?= $item['category'] === 'team_at_work' ? 'selected' : '' ?>>Team At Work</option>
                                        </select>
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
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>
</body>
</html>
