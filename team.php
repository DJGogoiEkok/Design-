<?php
// Force no caching at every level
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Pragma: no-cache');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
// Bust HTML cache with timestamp
define('PAGE_VERSION', time());

require_once 'db.php';
$stmt_team = $db->query("SELECT * FROM team_members WHERE type = 'team'");
$team = $stmt_team->fetchAll();

$stmt_partners = $db->query("SELECT * FROM team_members WHERE type = 'partner'");
$partners = $stmt_partners->fetchAll();

// Fetch gallery photos
$stmt_gallery = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt_gallery->fetchAll();

// Fetch gallery background image
$stmt_bg = $db->query("SELECT background_image_path FROM gallery_settings WHERE id = 1");
$bg_result = $stmt_bg->fetch();
$gallery_bg_path = $bg_result['background_image_path'] ?? '';
$gallery_bg_is_video = $gallery_bg_path && preg_match('/\.(mp4|webm|ogg)$/i', $gallery_bg_path);
$gallery_bg_image = $gallery_bg_is_video ? '' : $gallery_bg_path;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Team | Design Plus Assam</title>
<link rel="icon" href="images/site/favicon.png">
<link rel="stylesheet" href="css/style.css?v=64">
<style>
/* ═══════════ TEAM BODY — GEOMETRIC RED CORNER BG ═══════════ */
.team-board {
  position: relative; overflow: hidden;
  background: linear-gradient(135deg, #f5f5f5 0%, #fff 40%, #fff 60%, #f0f0f0 100%);
}
.team-board .container { position: relative; z-index: 2; }

.geo-bg {
  position: absolute; inset: 0; z-index: 0; pointer-events: none;
  overflow: hidden;
}
.geo-bg svg { width: 100%; height: 100%; }

.geo-bg .corner-tl { transform: translate(200px, -80px); opacity: 0; }
.geo-bg .corner-br { transform: translate(300px, 80px); opacity: 0; }
.geo-bg .accent-mr { transform: translateX(200px); opacity: 0; }
.geo-bg .accent-ml { transform: translateX(100px); opacity: 0; }
.geo-bg .accent-dots { opacity: 0; }
.geo-bg .accent-stripe { transform: scaleX(0); transform-origin: left; opacity: 0; }
.geo-bg .accent-diamond { transform: rotate(45deg) scale(0); opacity: 0; }

.geo-bg.animate .corner-tl { animation: cornerSlide 1s cubic-bezier(0.22,1,0.36,1) forwards; }
.geo-bg.animate .corner-br { animation: cornerSlide 1s cubic-bezier(0.22,1,0.36,1) 0.25s forwards; }
.geo-bg.animate .accent-mr { animation: cornerSlide 1s cubic-bezier(0.22,1,0.36,1) 0.4s forwards; }
.geo-bg.animate .accent-ml { animation: cornerSlide 1s cubic-bezier(0.22,1,0.36,1) 0.55s forwards; }
.geo-bg.animate .accent-dots { animation: fadeIn 0.8s ease 0.7s forwards; }
.geo-bg.animate .accent-stripe { animation: stripeGrow 0.8s cubic-bezier(0.22,1,0.36,1) 0.5s forwards; }
.geo-bg.animate .accent-diamond { animation: diamondPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.8s forwards; }

@keyframes cornerSlide { to { transform: translate(0,0); opacity: 1; } }
@keyframes fadeIn { to { opacity: 1; } }
@keyframes stripeGrow { to { transform: scaleX(1); opacity: 1; } }
@keyframes diamondPop { to { transform: rotate(45deg) scale(1); opacity: 1; } }

/* ═══════════ TEAM — BOARD OF DIRECTORS CARDS ═══════════ */
.team-board { padding: 70px 0 80px; background: #fff; text-align: center; }
.tb-title {
  font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800; color: #16151a;
  letter-spacing: -0.02em; margin: 0 0 44px;
}
.tb-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 26px;
}
.bod-card {
  container-type: inline-size; margin: 0;
}
.bod-inner {
  position: relative; aspect-ratio: 3 / 4.3;
  background: linear-gradient(158deg, #2563EB 0%, #1A4FC4 55%, #1239A0 100%);
  border-radius: 72px 14px 18px 18px;
  overflow: hidden;
  box-shadow: 0 22px 40px -22px rgba(26,79,196,.6);
}
.bod-water {
  position: absolute; left: 3%; top: 50%;
  transform: translateY(-50%);
  writing-mode: vertical-rl; text-orientation: mixed;
  font-size: 9cqw; font-weight: 800; letter-spacing: 0.06em;
  color: rgba(255,255,255,.16); white-space: nowrap; z-index: 0;
  user-select: none;
}
/* All photos are 960x720 landscape — photo in top-right, blue L on left + bottom */
.bod-inner img {
  position: absolute; left: 22%; top: 0; width: 78%; height: 76%;
  object-fit: cover; object-position: top center; z-index: 1;
  -webkit-mask-image: linear-gradient(to bottom, #000 90%, transparent 100%);
          mask-image: linear-gradient(to bottom, #000 90%, transparent 100%);
}
.bod-inner figcaption {
  position: absolute; left: 0; right: 0; bottom: 0; z-index: 2;
  padding: 14px 20px 22px; text-align: left;
}
.bod-inner figcaption b { display: block; color: #fff; font-size: 6cqw; font-weight: 800; line-height: 1.1; }
.bod-inner figcaption span { display: block; color: rgba(255,255,255,.82); font-size: 3.4cqw; margin-top: 3px; }
.tb-note {
  max-width: 860px; margin: 40px auto 0; color: #666; font-size: 0.95rem; line-height: 1.6;
}

@media (max-width: 860px) { .tb-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; } }
@media (max-width: 460px) { .tb-grid { grid-template-columns: 1fr 1fr; gap: 14px; } }

/* Per-card object-position to keep everyone's face centred in the L frame */
.tb-grid .bod-card:nth-child(2) .bod-inner img { object-position: top 10% right; }
.tb-grid .bod-card:nth-child(3) .bod-inner img { object-position: top 5% right; }
.tb-grid .bod-card:nth-child(4) .bod-inner img { object-position: top 5% right; }
.tb-grid .bod-card:nth-child(5) .bod-inner img { object-position: top 10% right; }
.tb-grid .bod-card:nth-child(6) .bod-inner img { object-position: top 15% right; }
.tb-grid .bod-card:nth-child(7) .bod-inner img { object-position: top 5% right; }
.tb-grid .bod-card:nth-child(8) .bod-inner img { object-position: top 10% right; }
.tb-grid .bod-card:nth-child(9) .bod-inner img { object-position: top 5% right; }
.tb-grid .bod-card:nth-child(10) .bod-inner img { object-position: top 5% right; }
/* ═══════════ end board of directors ═══════════ */
</style>
</head>
<body data-page="about">

<header id="site-header" data-include="header.html"></header>

<section class="hero hero-sm hero-doodle" id="top">
  <div class="hero-slide active" style="background-image:url('images/site/team-doodle-bg.png')"></div>
  <div class="hero-content">
    <div class="eyebrow"><span class="sketch-highlight">The Design Team</span></div>
    <h1><span class="sketch-highlight">Our Team</span></h1>
    <div class="breadcrumb"><span class="sketch-highlight" style="padding:4px 15px;"><a href="index.php" style="color:inherit;">Home</a> / <span>Our Team</span></span></div>
  </div>
</section>

<section class="team-board">
  <div class="geo-bg">
    <svg viewBox="0 0 1200 1000" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <filter id="geoShadow" x="-10%" y="-10%" width="130%" height="130%">
          <feDropShadow dx="0" dy="6" stdDeviation="10" flood-color="#000" flood-opacity="0.15"/>
        </filter>
        <filter id="geoShadowInner" x="-10%" y="-10%" width="130%" height="130%">
          <feDropShadow dx="0" dy="3" stdDeviation="5" flood-color="#000" flood-opacity="0.1"/>
        </filter>
        <linearGradient id="redGrad" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#c01a1a"/>
          <stop offset="100%" stop-color="#8b0000"/>
        </linearGradient>
        <linearGradient id="whiteGrad" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#fff"/>
          <stop offset="100%" stop-color="#e8e8e8"/>
        </linearGradient>
      </defs>
      <g class="corner-tl">
        <rect x="-120" y="-80" width="380" height="340" rx="50" fill="url(#whiteGrad)" filter="url(#geoShadow)" transform="rotate(-15, 70, 90)"/>
        <path d="M-100,-60 L200,-60 Q240,-60 240,0 L240,100 Q240,140 200,140 L140,140 L140,280 Q140,320 100,320 L-20,320 Q-60,320 -60,280 L-60,140 L-100,140 Q-140,140 -140,100 L-140,0 Q-140,-60 -100,-60Z" fill="url(#redGrad)" filter="url(#geoShadow)" transform="rotate(-15, 70, 130)"/>
        <path d="M-80,-40 L180,-40 Q210,-40 210,10 L210,80 Q210,110 180,110 L120,110 L120,260 Q120,290 90,290 L0,290 Q-30,290 -30,260 L-30,110 L-80,110 Q-110,110 -110,80 L-110,10 Q-110,-40 -80,-40Z" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="2" transform="rotate(-15, 70, 130)"/>
      </g>
      <g class="accent-mr">
        <rect x="1080" y="340" width="160" height="200" rx="30" fill="url(#whiteGrad)" filter="url(#geoShadowInner)" transform="rotate(-10, 1160, 440)"/>
        <path d="M1100,360 L1220,360 Q1240,360 1240,380 L1240,500 Q1240,520 1220,520 L1100,520 Q1080,520 1080,500 L1080,380 Q1080,360 1100,360Z" fill="url(#redGrad)" filter="url(#geoShadowInner)" transform="rotate(-10, 1160, 440)"/>
        <path d="M1115,385 L1205,385 Q1218,385 1218,398 L1218,482 Q1218,495 1205,495 L1115,495 Q1102,495 1102,482 L1102,398 Q1102,385 1115,385Z" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" transform="rotate(-10, 1160, 440)"/>
      </g>
      <g class="accent-stripe">
        <rect x="0" y="485" width="180" height="4" rx="2" fill="#c01a1a" opacity="0.6"/>
        <rect x="0" y="493" width="120" height="3" rx="1.5" fill="#c01a1a" opacity="0.3"/>
      </g>
      <g class="accent-ml">
        <rect x="-60" y="550" width="180" height="160" rx="35" fill="url(#whiteGrad)" filter="url(#geoShadowInner)" transform="rotate(12, 30, 630)"/>
        <rect x="-40" y="570" width="140" height="120" rx="25" fill="url(#redGrad)" filter="url(#geoShadowInner)" transform="rotate(12, 30, 630)"/>
        <rect x="-22" y="590" width="104" height="80" rx="16" fill="none" stroke="rgba(255,255,255,0.18)" stroke-width="1.5" transform="rotate(12, 30, 630)"/>
      </g>
      <g class="accent-dots" opacity="0.35">
        <circle cx="950" cy="220" r="3" fill="#c01a1a"/><circle cx="970" cy="220" r="3" fill="#c01a1a"/><circle cx="990" cy="220" r="3" fill="#c01a1a"/><circle cx="1010" cy="220" r="3" fill="#c01a1a"/>
        <circle cx="950" cy="240" r="3" fill="#c01a1a"/><circle cx="970" cy="240" r="3" fill="#c01a1a"/><circle cx="990" cy="240" r="3" fill="#c01a1a"/><circle cx="1010" cy="240" r="3" fill="#c01a1a"/>
        <circle cx="950" cy="260" r="3" fill="#c01a1a"/><circle cx="970" cy="260" r="3" fill="#c01a1a"/><circle cx="990" cy="260" r="3" fill="#c01a1a"/><circle cx="1010" cy="260" r="3" fill="#c01a1a"/>
        <circle cx="950" cy="280" r="3" fill="#c01a1a"/><circle cx="970" cy="280" r="3" fill="#c01a1a"/><circle cx="990" cy="280" r="3" fill="#c01a1a"/><circle cx="1010" cy="280" r="3" fill="#c01a1a"/>
      </g>
      <g class="accent-diamond">
        <rect x="175" y="430" width="40" height="40" rx="6" fill="url(#redGrad)" filter="url(#geoShadowInner)" transform="rotate(45, 195, 450)"/>
      </g>
      <g class="accent-dots" opacity="0.25">
        <circle cx="200" cy="750" r="3" fill="#c01a1a"/><circle cx="220" cy="750" r="3" fill="#c01a1a"/><circle cx="240" cy="750" r="3" fill="#c01a1a"/><circle cx="260" cy="750" r="3" fill="#c01a1a"/>
        <circle cx="200" cy="770" r="3" fill="#c01a1a"/><circle cx="220" cy="770" r="3" fill="#c01a1a"/><circle cx="240" cy="770" r="3" fill="#c01a1a"/><circle cx="260" cy="770" r="3" fill="#c01a1a"/>
        <circle cx="200" cy="790" r="3" fill="#c01a1a"/><circle cx="220" cy="790" r="3" fill="#c01a1a"/><circle cx="240" cy="790" r="3" fill="#c01a1a"/><circle cx="260" cy="790" r="3" fill="#c01a1a"/>
      </g>
      <g class="accent-stripe" style="transform-origin: right">
        <rect x="1020" y="720" width="180" height="4" rx="2" fill="#c01a1a" opacity="0.5"/>
        <rect x="1060" y="728" width="140" height="3" rx="1.5" fill="#c01a1a" opacity="0.25"/>
      </g>
      <g class="accent-diamond">
        <rect x="1030" y="620" width="30" height="30" rx="5" fill="none" stroke="#c01a1a" stroke-width="2" opacity="0.4" transform="rotate(45, 1045, 635)"/>
      </g>
      <g class="corner-br">
        <rect x="900" y="680" width="380" height="340" rx="50" fill="url(#whiteGrad)" filter="url(#geoShadow)" transform="rotate(-15, 1090, 850)"/>
        <path d="M960,700 L1220,700 Q1260,700 1260,740 L1260,920 Q1260,960 1220,960 L1100,960 L1100,1040 Q1100,1060 1080,1060 L1020,1060 Q1000,1060 1000,1040 L1000,960 L960,960 Q920,960 920,920 L920,740 Q920,700 960,700Z" fill="url(#redGrad)" filter="url(#geoShadow)" transform="rotate(-15, 1090, 850)"/>
        <path d="M980,720 L1200,720 Q1230,720 1230,750 L1230,900 Q1230,930 1200,930 L1080,930 L1080,1020 Q1080,1040 1060,1040 L1040,1040 Q1020,1040 1020,1020 L1020,930 L980,930 Q950,930 950,900 L950,750 Q950,720 980,720Z" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="2" transform="rotate(-15, 1090, 850)"/>
      </g>
    </svg>
  </div>
  <div class="container">
    <h2 class="tb-title reveal"><span class="sketch-highlight">The Design Team</span></h2>

    <div class="tb-grid reveal">
      <?php foreach($team as $index => $member): ?>
      <figure class="bod-card">
        <div class="bod-inner">
          <span class="bod-water"><?= htmlspecialchars(strtok($member['role'], ' ')) ?></span>
          <?php $imgPath = $member['image_path'] ? $member['image_path'] : 'images/team/default.jpg'; $mtime = @filemtime($imgPath) ?: time(); ?>
          <img src="<?= htmlspecialchars($imgPath) ?>?v=<?= $mtime ?>" alt="<?= htmlspecialchars($member['name']) ?>" loading="lazy">
          <figcaption><b><?= htmlspecialchars($member['name']) ?></b><span><?= htmlspecialchars($member['role']) ?></span></figcaption>
        </div>
      </figure>
      <?php endforeach; ?>
    </div>

    <p class="tb-note reveal">The architects, engineers and designers behind Design Plus — a decade of shaping homes, commercial landmarks and institutions across Assam, delivered with honest budgets and meticulous care.</p>
  </div>
</section>

<section class="section-alt">
  <div class="container">
    <div class="section-title reveal">
      <div class="eyebrow">With Us</div>
      <h2>Partner Associates</h2>
    </div>
    <div class="client-grid reveal">
      <?php foreach($partners as $partner): ?>
      <div class="client-card"><h4><?= htmlspecialchars($partner['name']) ?></h4><span><?= htmlspecialchars($partner['role']) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="gallery-section">
  <div class="container">
    <div class="section-title reveal">
      <div class="eyebrow">Behind The Scenes</div>
      <h2>Our Team At Work</h2>
    </div>

    <div class="photo-collage-container reveal">
      <div class="photo-collage-wrapper">
        <?php 
          $total = count($gallery_photos);
          $center_idx = floor(($total - 1) / 2); // Find the exact middle index
        ?>
        <?php foreach ($gallery_photos as $index => $photo): ?>
          <?php 
            $distance = abs($index - $center_idx);
            $z_index = 20 - $distance;
            
            if ($distance == 0) $size_class = 'frame-lg';
            elseif ($distance == 1) $size_class = 'frame-md';
            elseif ($distance == 2) $size_class = 'frame-sm';
            else $size_class = 'frame-xs';
          ?>
          <div class="collage-frame <?php echo $size_class; ?>" style="z-index: <?php echo $z_index; ?>;" data-index="<?php echo $index; ?>">
            <img src="<?php echo htmlspecialchars($photo['photo_image']); ?>" alt="<?php echo htmlspecialchars($photo['photo_label']); ?>" class="gallery-photo-zoomable" data-zoom-src="<?php echo htmlspecialchars($photo['photo_image']); ?>" data-label="<?php echo htmlspecialchars($photo['photo_label']); ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Image Zoom Modal -->
    <div id="gallery-zoom-modal" class="gallery-zoom-modal">
      <div class="gallery-zoom-close">&times;</div>
      <div class="gallery-zoom-container">
        <img id="gallery-zoom-image" src="" alt="">
        <p id="gallery-zoom-label" class="gallery-zoom-label"></p>
      </div>
      <div class="gallery-zoom-nav">
        <button class="gallery-zoom-prev">&larr;</button>
        <button class="gallery-zoom-next">&rarr;</button>
      </div>
    </div>
  </div>
</section>

<style>
.gallery-section { 
  padding: 80px 0 160px 0; 
  background: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 50%, #888888 100%); 
  color: #333; 
  overflow: hidden;
}
.gallery-section .section-title h2 { color: #333; }
.gallery-section .section-title .eyebrow { color: #777; }

/* ═══════════ GALLERY — PHOTO COLLAGE ═══════════ */
.photo-collage-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 60px;
}

.photo-collage-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.collage-frame {
  background: #fff;
  padding: 10px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  -webkit-box-reflect: below 2px linear-gradient(transparent 75%, rgba(255,255,255,0.4));
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
}

.collage-frame img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  background: #222;
  display: block;
}

.frame-lg { width: 340px; height: 340px; margin: 0 -40px; box-shadow: 0 20px 45px rgba(0,0,0,0.25); }
.frame-md { width: 280px; height: 280px; margin: 0 -30px; }
.frame-sm { width: 220px; height: 220px; margin: 0 -20px; }
.frame-xs { width: 160px; height: 160px; margin: 0 -15px; }

.collage-frame:hover {
  transform: translateY(-20px) scale(1.05);
  z-index: 50 !important;
  cursor: pointer;
}

/* Mobile */
@media (max-width: 900px) {
  .frame-lg { width: 240px; height: 240px; margin: 0 -20px; }
  .frame-md { width: 180px; height: 180px; margin: 0 -15px; }
  .frame-sm { width: 140px; height: 140px; margin: 0 -10px; }
  .frame-xs { width: 100px; height: 100px; margin: 0 -5px; }
}

@media (max-width: 600px) {
  .frame-lg { width: 180px; height: 180px; margin: 0 -15px; }
  .frame-md { width: 140px; height: 140px; margin: 0 -10px; }
  .frame-sm { width: 100px; height: 100px; margin: 0 -5px; }
  .frame-xs { width: 70px; height: 70px; margin: 0 -3px; }
  .collage-frame { padding: 5px; }
}
</style>

<script>
// Gallery data from PHP
var galleryData = <?php echo json_encode($gallery_photos); ?>;

// Run on load
document.addEventListener('DOMContentLoaded', function() {
  initGalleryZoom();
});

// Gallery zoom functionality
function initGalleryZoom() {
  const modal = document.getElementById('gallery-zoom-modal');
  const closeBtn = document.querySelector('.gallery-zoom-close');
  const zoomImage = document.getElementById('gallery-zoom-image');
  const zoomLabel = document.getElementById('gallery-zoom-label');
  const prevBtn = document.querySelector('.gallery-zoom-prev');
  const nextBtn = document.querySelector('.gallery-zoom-next');
  const zoomables = document.querySelectorAll('.gallery-photo-zoomable');
  let currentZoomIndex = 0;

  function showZoom(index) {
    if (index < 0 || index >= zoomables.length) return;
    currentZoomIndex = index;
    const img = zoomables[index];
    zoomImage.src = img.dataset.zoomSrc;
    zoomLabel.textContent = img.dataset.label;
    modal.classList.add('active');
  }

  zoomables.forEach((img, index) => {
    img.style.cursor = 'pointer';
    img.parentElement.addEventListener('click', function(e) {
      if (e.target.tagName === 'IMG' || e.target.parentElement.classList.contains('gallery-photo-tile')) {
        showZoom(index);
      }
    });
  });

  closeBtn.addEventListener('click', function() {
    modal.classList.remove('active');
  });

  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.classList.remove('active');
    }
  });

  prevBtn.addEventListener('click', function() {
    showZoom(currentZoomIndex - 1);
  });

  nextBtn.addEventListener('click', function() {
    showZoom(currentZoomIndex + 1);
  });

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (!modal.classList.contains('active')) return;
    if (e.key === 'ArrowLeft') showZoom(currentZoomIndex - 1);
    if (e.key === 'ArrowRight') showZoom(currentZoomIndex + 1);
    if (e.key === 'Escape') modal.classList.remove('active');
  });
}
</script>

<footer id="site-footer" data-include="footer.html"></footer>
<script src="js/main.js?v=31"></script>
<script>
(function(){
  var sb = document.querySelector('.geo-bg');
  if (!sb) return;
  var io = new IntersectionObserver(function(entries){
    if (entries[0].isIntersecting) { sb.classList.add('animate'); io.disconnect(); }
  }, {threshold: 0.15});
  io.observe(sb);
})();
</script>
</body>
</html>
