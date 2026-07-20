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
        $author_name = $_POST['author_name'] ?? '';
        $quote = $_POST['quote'] ?? '';
        
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/testimonials_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/testimonials_uploads/' . $file_name;
            }
        }
        
        $stmt = $db->prepare("INSERT INTO testimonials (author_name, quote, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$author_name, $quote, $image_path]);
        
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'];
        $author_name = $_POST['author_name'] ?? '';
        $quote = $_POST['quote'] ?? '';
        
        $image_query_part = "";
        $params = [$author_name, $quote];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/site/testimonials_uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'images/site/testimonials_uploads/' . $file_name;
                $image_query_part = ", image_path = ?";
                $params[] = $image_path;
            }
        }
        
        $params[] = $id;
        
        $stmt = $db->prepare("UPDATE testimonials SET author_name = ?, quote = ? $image_query_part WHERE id = ?");
        $stmt->execute($params);
    }
}

// Fetch testimonials
$stmt = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials</title>
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
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action active">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Testimonials</h2>
                
                <div class="card mb-4">
                    <div class="card-header">Add New Testimonial</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label>Author Name</label>
                                <input type="text" name="author_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Quote</label>
                                <textarea name="quote" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Author Image (optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Testimonial</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Existing Testimonials</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Quote</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testimonials as $t): ?>
                                <tr>
                                    <td>
                                        <?php if ($t['image_path']): ?>
                                            <img src="../<?= htmlspecialchars($t['image_path']) ?>" width="50" height="50" style="object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <div style="width:50px; height:50px; background:#ccc; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold;">
                                                <?= htmlspecialchars(substr($t['author_name'], 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($t['author_name']) ?></td>
                                    <td><?= htmlspecialchars(substr($t['quote'], 0, 50)) ?>...</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-testimonial-btn" 
                                            data-id="<?= $t['id'] ?>" 
                                            data-author="<?= htmlspecialchars($t['author_name']) ?>" 
                                            data-quote="<?= htmlspecialchars($t['quote']) ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editTestimonialModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title">Edit Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" id="edit-testimonial-id">
                  
                  <div class="mb-3">
                      <label>Author Name</label>
                      <input type="text" name="author_name" id="edit-testimonial-author" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label>Quote</label>
                      <textarea name="quote" id="edit-testimonial-quote" class="form-control" rows="4" required></textarea>
                  </div>
                  <div class="mb-3">
                      <label>New Image (optional)</label>
                      <input type="file" name="image" class="form-control" accept="image/*">
                  </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>
</html>
