<?php
require_once 'db.php';
$stmt = $db->query("SELECT * FROM awards ORDER BY id DESC");
$awards = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Awards | Design Plus Assam</title>
<link rel="icon" href="images/site/favicon.png">
<link rel="stylesheet" href="css/style.css?v=64">
</head>
<body data-page="award">

<header id="site-header" data-include="header.html"></header>

<section class="hero hero-sm" id="top">
  <div class="hero-slide active" style="background-image:url('images/site/intro-bg.jpg')"></div>
  <div class="hero-content">
    <div class="eyebrow">Recognition</div>
    <h1>Awards</h1>
    <div class="breadcrumb"><a href="index.php">Home</a> / <span>Awards</span></div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-title reveal">
      <div class="eyebrow">National Accreditation</div>
      <h2>Honoured For Our Work</h2>
      <p>Recognised nationally for excellence in architectural design and construction service.</p>
    </div>
    <div class="award-list reveal">
      <?php foreach($awards as $award): ?>
      <div class="award-item">
        <div class="yr"><?= htmlspecialchars($award['description'] ?: 'Award') ?></div>
        <h4><?= htmlspecialchars($award['title']) ?></h4>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</section>

<section class="cta-band">
  <div class="container reveal">
    <h2>Ready to build with an award-winning team?</h2>
    <p>Let's talk about your project and how Design Plus can bring it to life.</p>
    <a href="contact.html" class="btn btn-solid">Get In Touch</a>
  </div>
</section>

<footer id="site-footer" data-include="footer.html"></footer>
<script src="js/main.js?v=31"></script>
</body>
</html>
