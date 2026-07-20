<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Design Plus - Admin Panel</title>
    <!-- Use a simple CDN for bootstrap -->
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
                    <a href="index.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="hero.php" class="list-group-item list-group-item-action">Hero Section Images</a>
                    <a href="team.php" class="list-group-item list-group-item-action">Our Team & Partners</a>
                    <a href="gallery.php" class="list-group-item list-group-item-action">Projects / At Work Gallery</a>
                    <a href="awards.php" class="list-group-item list-group-item-action">Awards</a>
                    <a href="applications.php" class="list-group-item list-group-item-action">Job / Intern Applications</a>
                    <a href="notices.php" class="list-group-item list-group-item-action">Important Notices</a>
                    <a href="testimonials.php" class="list-group-item list-group-item-action">Testimonials</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Welcome to the Admin Dashboard</h2>
                <p>Select an option from the sidebar to manage your content.</p>
            </div>
        </div>
    </div>
</body>
</html>
