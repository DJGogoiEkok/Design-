<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'];
        $content = $_POST['content'] ?? '';
        $stmt = $db->prepare("INSERT INTO notices (title, content) VALUES (?, ?)");
        $stmt->execute([$title, $content]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM notices WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'] ?? '';
        $stmt = $db->prepare("UPDATE notices SET title = ?, content = ? WHERE id = ?");
        $stmt->execute([$title, $content, $id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $id = $_POST['id'];
        $new_status = $_POST['new_status'];
        $stmt = $db->prepare("UPDATE notices SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
    }
    header("Location: notices.php");
    exit;
}

$stmt = $db->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Notices</title>
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
                    <a href="gallery.php" class="list-group-item list-group-item-action">Projects / At Work Gallery</a>
                    <a href="awards.php" class="list-group-item list-group-item-action">Awards</a>
                    <a href="applications.php" class="list-group-item list-group-item-action">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action active">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Notices</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label>Title</label>
                                <input class="form-control" type="text" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label>Content</label>
                                <textarea class="form-control" name="content"></textarea>
                            </div>
                            <button class="btn btn-primary">Add Notice</button>
                        </form>
                    </div>
                </div>
                <table class="table table-bordered mb-4">
                    <thead><tr><th>Date</th><th>Title</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($notices as $item): ?>
                        <tr>
                            <td><?= $item['created_at'] ?></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>
                                <?php if ($item['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Archived</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">Edit</button>
                                
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <?php if ($item['status'] === 'active'): ?>
                                        <input type="hidden" name="new_status" value="archived">
                                        <button class="btn btn-sm btn-warning">Archive</button>
                                    <?php else: ?>
                                        <input type="hidden" name="new_status" value="active">
                                        <button class="btn btn-sm btn-success">Unarchive</button>
                                    <?php endif; ?>
                                </form>

                                <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $item['id'] ?>"><button class="btn btn-sm btn-danger">X</button></form>
                            </td>
                        </tr>
                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Edit Notice</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Content</label>
                                        <textarea class="form-control" name="content"><?= htmlspecialchars($item['content']) ?></textarea>
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
</body>
</html>
