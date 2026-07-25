<?php
require_once 'db.php';
$activeStmt = $db->query("SELECT * FROM notices WHERE status = 'active' ORDER BY created_at DESC");
$activeNotices = $activeStmt->fetchAll();
$archivedStmt = $db->query("SELECT * FROM notices WHERE status = 'archived' ORDER BY created_at DESC");
$archivedNotices = $archivedStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Important Notices | Design Plus Assam</title>
<link rel="icon" href="images/site/favicon.png">
<link rel="stylesheet" href="css/style.css?v=64">
<style>
/* ═══════════ FILE FOLDER NOTICES ═══════════ */
.folder-section {
  padding: 60px 0 100px;
  background: url('images/site/notices-hero-bg.jpg') center center / cover no-repeat fixed;
}
.folder-wrap {
  max-width: 880px;
  margin: 0 auto;
  position: relative;
}

/* — tabs row — */
.folder-tabs {
  display: flex;
  gap: 0;
  padding: 0 30px;
  position: relative;
  z-index: 2;
}
.folder-tab {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(24px) saturate(160%);
  -webkit-backdrop-filter: blur(24px) saturate(160%);
  color: #2a2a26;
  font-size: 0.88rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  padding: 14px 36px 16px;
  border-radius: 12px 12px 0 0;
  cursor: pointer;
  position: relative;
  top: 2px;
  transition: background 0.3s, color 0.3s, box-shadow 0.3s;
  white-space: nowrap;
  border: 1.5px solid rgba(30,74,142,0.3);
  border-bottom: none;
  font-family: inherit;
}
.folder-tab:hover {
  background: rgba(255,255,255,0.25);
  border-color: rgba(30,74,142,0.45);
}
.folder-tab.active {
  background: rgba(255,255,255,0.22);
  backdrop-filter: blur(40px) saturate(180%) brightness(1.05);
  -webkit-backdrop-filter: blur(40px) saturate(180%) brightness(1.05);
  color: #1a1a18;
  font-weight: 700;
  top: 0;
  z-index: 3;
  border-color: rgba(30,74,142,0.5);
  box-shadow:
    0 1px 0 0 rgba(255,255,255,0.5) inset,
    0 -4px 12px rgba(0,0,0,0.04);
}
.folder-tab .tab-count {
  display: inline-block;
  background: rgba(0,0,0,0.1);
  color: #5a5a52;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 999px;
  margin-left: 8px;
  vertical-align: middle;
}
.folder-tab.active .tab-count {
  background: rgba(0,0,0,0.12);
  color: #3a3a36;
}

/* — folder body — */
.folder-body {
  background: linear-gradient(
    135deg,
    rgba(255,255,255,0.22) 0%,
    rgba(255,255,255,0.10) 40%,
    rgba(255,255,255,0.14) 60%,
    rgba(255,255,255,0.08) 100%
  );
  backdrop-filter: blur(40px) saturate(180%) brightness(1.06);
  -webkit-backdrop-filter: blur(40px) saturate(180%) brightness(1.06);
  border-radius: 6px 16px 16px 16px;
  min-height: 420px;
  padding: 40px 44px 48px;
  position: relative;
  z-index: 1;
  border: 1.5px solid rgba(30,74,142,0.35);
  box-shadow:
    0 0 0 0.5px rgba(255,255,255,0.2) inset,
    0 1px 0 0 rgba(255,255,255,0.6) inset,
    0 -1px 0 0 rgba(255,255,255,0.1) inset,
    0 16px 48px -12px rgba(0,0,0,0.15),
    0 2px 8px rgba(0,0,0,0.06);
}

/* — notice panel — */
.folder-panel { display: none; }
.folder-panel.active {
  display: block;
  animation: folderFadeIn 0.35s ease;
}
@keyframes folderFadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* — notice cards inside folder — */
.fn-card {
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(16px) saturate(140%);
  -webkit-backdrop-filter: blur(16px) saturate(140%);
  border: 1.5px solid rgba(30,74,142,0.25);
  border-radius: 12px;
  padding: 24px 28px;
  margin-bottom: 16px;
  transition: background 0.25s, box-shadow 0.25s, border-color 0.25s;
  box-shadow: 0 1px 0 0 rgba(255,255,255,0.4) inset;
}
.fn-card:last-child { margin-bottom: 0; }
.fn-card:hover {
  background: rgba(255,255,255,0.35);
  border-color: rgba(30,74,142,0.4);
  box-shadow: 0 1px 0 0 rgba(255,255,255,0.5) inset, 0 6px 20px -6px rgba(0,0,0,0.1);
}
.fn-card .fn-date {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 600;
  color: #6b6b60;
  background: rgba(0,0,0,0.07);
  padding: 3px 12px;
  border-radius: 999px;
  margin-bottom: 10px;
  letter-spacing: 0.04em;
}
.fn-card h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #2a2a26;
  margin: 0 0 8px;
  line-height: 1.3;
}
.fn-card p {
  color: #4a4a42;
  font-size: 0.9rem;
  line-height: 1.7;
  margin: 0;
  white-space: pre-line;
}

.folder-empty {
  text-align: center;
  padding: 60px 20px;
  color: #7a7a70;
  font-size: 0.95rem;
  font-style: italic;
}

/* — responsive — */
@media (max-width: 700px) {
  .folder-tabs { padding: 0 16px; }
  .folder-tab { padding: 11px 22px; font-size: 0.8rem; }
  .folder-body { padding: 28px 20px 36px; border-radius: 4px 12px 12px 12px; }
  .fn-card { padding: 20px 20px; }
}
/* ═══════════ end file folder ═══════════ */
</style>
</head>
<body data-page="notices">

<header id="site-header" data-include="header.html"></header>

<section class="hero hero-sm" id="top">
  <div class="hero-slide active" style="background-image:url('images/site/about-hero-bg.jpg')"></div>
  <div class="hero-content">
    <div class="eyebrow">Stay Updated</div>
    <h1>Important Notices</h1>
    <div class="breadcrumb"><a href="index.html">Home</a> / <span>Notices</span></div>
  </div>
</section>

<section class="folder-section">
  <div class="container">
    <div class="section-title reveal">
      <div class="eyebrow">Announcements</div>
      <h2>Important Notices</h2>
    </div>

    <div class="folder-wrap reveal">
      <div class="folder-tabs">
        <button class="folder-tab active" data-tab="notices">Notices <span class="tab-count"><?= count($activeNotices) ?></span></button>
        <button class="folder-tab" data-tab="archived">Archived <span class="tab-count"><?= count($archivedNotices) ?></span></button>
      </div>

      <div class="folder-body">
        <div class="folder-panel active" data-panel="notices">
          <?php if (empty($activeNotices)): ?>
            <div class="folder-empty">No active notices at the moment.</div>
          <?php else: ?>
            <?php foreach ($activeNotices as $n): ?>
              <div class="fn-card">
                <span class="fn-date"><?= date('d M Y', strtotime($n['created_at'])) ?></span>
                <h3><?= htmlspecialchars($n['title']) ?></h3>
                <?php if (!empty($n['content'])): ?>
                  <p><?= htmlspecialchars($n['content']) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="folder-panel" data-panel="archived">
          <?php if (empty($archivedNotices)): ?>
            <div class="folder-empty">No archived notices.</div>
          <?php else: ?>
            <?php foreach ($archivedNotices as $n): ?>
              <div class="fn-card">
                <span class="fn-date"><?= date('d M Y', strtotime($n['created_at'])) ?></span>
                <h3><?= htmlspecialchars($n['title']) ?></h3>
                <?php if (!empty($n['content'])): ?>
                  <p><?= htmlspecialchars($n['content']) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<footer id="site-footer" data-include="footer.html"></footer>
<script src="js/main.js?v=31"></script>
<script>
(function(){
  const tabs = document.querySelectorAll('.folder-tab');
  const panels = document.querySelectorAll('.folder-panel');
  if (!tabs.length) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const id = tab.dataset.tab;
      tabs.forEach(t => t.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      tab.classList.add('active');
      document.querySelector('.folder-panel[data-panel="' + id + '"]').classList.add('active');
    });
  });
})();
</script>
</body>
</html>
