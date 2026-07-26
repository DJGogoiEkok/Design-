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

.geo-bg .corner-tl { transform: translate(-80px, -80px); opacity: 0; }
.geo-bg .corner-br { transform: translate(80px, 80px); opacity: 0; }
.geo-bg .accent-mr { transform: translateX(60px); opacity: 0; }
.geo-bg .accent-ml { transform: translateX(-60px); opacity: 0; }
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
    <div class="eyebrow">The Design Team</div>
    <h1>Our Team</h1>
    <div class="breadcrumb"><a href="index.html">Home</a> / <span>Our Team</span></div>
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
    <h2 class="tb-title reveal">The Design Team</h2>

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

    <div class="gallery-showcase reveal">
      <div class="featured-section">
        <img id="featured-img" src="images/team/12.jpg" alt="Team at work" class="featured-image">
        <div class="featured-caption">
          <img src="images/team/profile.jpg" alt="Profile" class="caption-avatar">
          <div class="caption-text">
            <h4>Design Plus</h4>
            <p>Behind the scenes moments</p>
          </div>
        </div>
      </div>

      <div class="gallery-thumbnails" id="thumbnailsCarousel">
        <div class="thumb-item" data-src="images/team/12.jpg">
          <img src="images/team/12.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/13.jpg">
          <img src="images/team/13.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/14.jpg">
          <img src="images/team/14.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/15.jpg">
          <img src="images/team/15.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/16.jpg">
          <img src="images/team/16.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/17.jpg">
          <img src="images/team/17.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/18.jpg">
          <img src="images/team/18.jpg" alt="Team work" loading="lazy">
        </div>
        <div class="thumb-item" data-src="images/team/19.jpg">
          <img src="images/team/19.jpg" alt="Team work" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.gallery-section { padding: 80px 0; background: #f9f9f9; }

.gallery-showcase {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 50px;
  align-items: flex-start;
}

.featured-section {
  display: flex;
  flex-direction: column;
}

.featured-image {
  width: 100%;
  border-radius: 32px 32px 0 0;
  height: 450px;
  object-fit: cover;
  display: block;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  transition: opacity 0.3s ease;
}

.featured-caption {
  background: #fff;
  border-radius: 0 0 32px 32px;
  padding: 20px;
  display: flex;
  gap: 14px;
  align-items: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.caption-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #f0f0f0;
}

.caption-text {
  flex: 1;
}

.caption-text h4 {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
}

.caption-text p {
  margin: 2px 0 0;
  font-size: 0.85rem;
  color: #666;
}

.gallery-thumbnails {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  padding: 4px 0;
  flex-shrink: 0;
  scrollbar-width: none;
  flex-wrap: nowrap;
}

.gallery-thumbnails::-webkit-scrollbar {
  display: none;
}

.thumb-item {
  position: relative;
  min-width: 60px;
  width: 60px;
  height: 520px;
  border-radius: 32px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 12px 40px rgba(0,0,0,0.12);
  flex-shrink: 0;
  flex-grow: 0;
}

.thumb-item:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 50px rgba(0,0,0,0.18);
}

.thumb-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

@media (max-width: 1024px) {
  .gallery-showcase {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .featured-image {
    height: 350px;
  }
}

@media (max-width: 768px) {
  .gallery-thumbnails {
    flex-wrap: nowrap;
  }

  .thumb-item {
    width: 50px;
    height: 405px;
    min-width: 50px;
  }
}
</style>

<script>
var carouselIndex = 1;
var autoCarouselInterval;

function autoCarousel() {
  var thumbs = document.querySelectorAll('.thumb-item');
  if(thumbs.length === 0) return;

  var nextThumb = thumbs[carouselIndex % thumbs.length];
  var src = nextThumb.getAttribute('data-src');

  // Fade out, change src, fade in
  var featuredImg = document.getElementById('featured-img');
  featuredImg.style.opacity = '0';
  setTimeout(function() {
    featuredImg.src = src;
    featuredImg.alt = 'Team member ' + (carouselIndex % thumbs.length);
    featuredImg.style.opacity = '1';
  }, 150);

  // Update opacity states for visual feedback
  document.querySelectorAll('.thumb-item').forEach(t => t.style.opacity = '0.3');
  nextThumb.style.opacity = '1';

  carouselIndex++;
}

document.addEventListener('DOMContentLoaded', function() {
  var thumbs = document.querySelectorAll('.thumb-item');
  if(thumbs.length === 0) return;

  // Set first thumbnail as initially active
  thumbs[0].style.opacity = '1';
  document.querySelectorAll('.thumb-item:not(:first-child)').forEach(t => t.style.opacity = '0.3');

  // Start auto carousel every 4 seconds
  autoCarouselInterval = setInterval(autoCarousel, 4000);

  // Pause on hover over entire featured section
  var featuredSection = document.querySelector('.featured-section');
  if(featuredSection) {
    featuredSection.addEventListener('mouseenter', function() {
      clearInterval(autoCarouselInterval);
    });

    featuredSection.addEventListener('mouseleave', function() {
      clearInterval(autoCarouselInterval);
      autoCarouselInterval = setInterval(autoCarousel, 4000);
    });
  }

  // Keyboard & reduced-motion accessibility
  var featuredImg = document.getElementById('featured-img');
  if(featuredImg) {
    featuredImg.setAttribute('tabindex', '0');
    featuredImg.addEventListener('focus', function() {
      clearInterval(autoCarouselInterval);
    });
    featuredImg.addEventListener('blur', function() {
      autoCarouselInterval = setInterval(autoCarousel, 4000);
    });
  }

  // Respect prefers-reduced-motion
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    clearInterval(autoCarouselInterval);
  }
});
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
