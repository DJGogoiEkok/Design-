<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $type = $_POST['type'];
        $stmt = $db->prepare("UPDATE applications SET name = ?, email = ?, phone = ?, type = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $type, $id]);
    }
}

$stmt = $db->query("SELECT * FROM applications ORDER BY submitted_at DESC");
$apps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applications</title>
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
                    <a href="applications.php" class="list-group-item list-group-item-action active">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>View Applications</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table table-bordered mb-0">
                            <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Type</th><th>Resume</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php if(empty($apps)): ?>
                                    <tr><td colspan="7" class="text-center">No applications yet.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($apps as $item): ?>
                                <tr>
                                    <td><?= $item['submitted_at'] ?></td>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><a href="mailto:<?= htmlspecialchars($item['email']) ?>"><?= htmlspecialchars($item['email']) ?></a></td>
                                    <td><?= htmlspecialchars($item['phone']) ?></td>
                                    <td><?= htmlspecialchars($item['type']) ?></td>
                                    <td><?php if($item['resume_path']): ?><a href="../<?= htmlspecialchars($item['resume_path']) ?>" target="_blank">Download</a><?php endif; ?></td>
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
                                        <h5 class="modal-title">Edit Application</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($item['email']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone</label>
                                                <input class="form-control" type="text" name="phone" value="<?= htmlspecialchars($item['phone']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Type</label>
                                                <select class="form-select" name="type">
                                                    <option value="job" <?= $item['type'] === 'job' ? 'selected' : '' ?>>Job</option>
                                                    <option value="internship" <?= $item['type'] === 'internship' ? 'selected' : '' ?>>Internship</option>
                                                </select>
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
</body>
</html>
