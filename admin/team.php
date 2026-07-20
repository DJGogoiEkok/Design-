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
            $file_name = time() . '_' . basename($_FILES['image']['name']);
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
            $file_name = time() . '_' . basename($_FILES['image']['name']);
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
    }
}

// Fetch team members
$stmt = $db->query("SELECT * FROM team_members WHERE type = 'team'");
$team = $stmt->fetchAll();

// Fetch partners
$stmt = $db->query("SELECT * FROM team_members WHERE type = 'partner'");
$partners = $stmt->fetchAll();
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
                
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>
</body>
</html>
