# Team Gallery Artistic Scatter Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the featured-carousel gallery with an artistic scattered-grid layout on desktop, organized grid layouts on tablet/mobile, with admin-controlled photo labels and figure illustration.

**Architecture:** 
- Create database table for gallery photos (8 photos + 1 figure illustration)
- Update PHP to query gallery data and pass to frontend
- Replace HTML carousel markup with scattered-grid structure
- Add CSS for responsive layouts: desktop scatter (absolute positioning), tablet 3×3 grid, mobile 2×4 grid
- Add JavaScript to render gallery from database data and generate SVG connecting lines (desktop only)

**Tech Stack:** PHP, MySQL, HTML/CSS, JavaScript, SVG

## Global Constraints

- **Target file:** `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php`
- **Scope:** Only "Our Team At Work" gallery; other team layouts unchanged
- **Photo count:** 8 photos maximum + 1 figure illustration (9 total elements)
- **Aesthetic:** White/light gray background, dark connecting lines (#333, opacity 0.25), minimalist gallery style
- **Responsive:** Desktop (>1024px) scatter, Tablet (768px–1024px) 3×3 grid, Mobile (<768px) 2×4 grid
- **Admin integration:** Labels and figure image changeable from admin panel (integration points identified, full admin implementation separate task)
- **No auto-play:** Unlike previous carousel, this is a static gallery (no cycling featured image)

---

## File Structure

**Modified Files:**
- `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php` — All HTML, CSS, JavaScript for gallery section (existing file, replace carousel section with new structure)

**Database:**
- Create new table: `gallery_photos` (or extend existing `team_members` table — see Task 1 for decision)
- Columns: `id`, `photo_image`, `photo_label`, `position_x`, `position_y`, `figure_image`, `gallery_order`

---

## Task Breakdown

### Task 1: Create Database Schema & Seed Initial Data

**Files:**
- Create: Database migration or direct SQL
- Modify: `db.php` (if creating connection pattern)

**Interfaces:**
- Produces: `gallery_photos` table with 9 rows (8 photos + 1 figure), queryable via `SELECT * FROM gallery_photos ORDER BY gallery_order ASC`

**Steps:**

- [ ] **Step 1: Create database table**

Run this SQL:
```sql
CREATE TABLE IF NOT EXISTS gallery_photos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  photo_image VARCHAR(255) NOT NULL,
  photo_label VARCHAR(100) NOT NULL,
  position_x INT DEFAULT 0,
  position_y INT DEFAULT 0,
  figure_image VARCHAR(255),
  gallery_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Expected: Table created successfully.

- [ ] **Step 2: Seed initial data with existing photos**

Insert 8 photos + 1 figure:
```sql
INSERT INTO gallery_photos (photo_image, photo_label, position_x, position_y, gallery_order) VALUES
('images/team/12.jpg', 'Creative', 50, 80, 1),
('images/team/13.jpg', 'Ambitious', 200, 50, 2),
('images/team/14.jpg', 'Innovative', 350, 120, 3),
('images/team/15.jpg', 'Collaborative', 100, 220, 4),
('images/team/16.jpg', 'Driven', 280, 200, 5),
('images/team/17.jpg', 'Artistic', 450, 180, 6),
('images/team/18.jpg', 'Strategic', 150, 320, 7),
('images/team/19.jpg', 'Dedicated', 380, 280, 8),
('images/team/figure.png', '', 320, 100, 9);
```

Expected: 9 rows inserted.

- [ ] **Step 3: Verify data**

Query the table:
```sql
SELECT * FROM gallery_photos ORDER BY gallery_order ASC;
```

Expected: 9 rows returned with correct image paths and labels.

- [ ] **Step 4: Commit**

```bash
git add db-migrations.sql
git commit -m "feat: create gallery_photos table with initial seed data

- New table: gallery_photos (8 photos + 1 figure illustration)
- Columns: photo_image, photo_label, position_x/y, figure_image, gallery_order
- Seed: 9 rows with existing team photos and labels"
```

---

### Task 2: Update PHP to Query Gallery Data

**Files:**
- Modify: `team.php` (add PHP query at top of file)

**Interfaces:**
- Produces: `$gallery_photos` array (9 photos with id, photo_image, photo_label, position_x, position_y, figure_image)

**Steps:**

- [ ] **Step 1: Add gallery query to PHP**

In `team.php` after existing `team_members` queries (~line 11-15), add:
```php
// Fetch gallery photos
$stmt_gallery = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt_gallery->fetchAll();
```

- [ ] **Step 2: Verify query works**

Test by adding a temporary debug line after query:
```php
echo "<!-- Gallery photos count: " . count($gallery_photos) . " -->";
```

Visit the page. Open DevTools → check HTML source. Expected comment: `<!-- Gallery photos count: 9 -->`

- [ ] **Step 3: Remove debug line**

Delete the echo statement from Step 2.

- [ ] **Step 4: Commit**

```bash
git add team.php
git commit -m "feat: add gallery_photos database query

- Query: SELECT * FROM gallery_photos ORDER BY gallery_order ASC
- Result: $gallery_photos array passed to template"
```

---

### Task 3: Replace HTML Carousel Markup with Scattered-Grid Structure

**Files:**
- Modify: `team.php` (replace carousel HTML ~lines 240–287)

**Interfaces:**
- Consumes: `$gallery_photos` array from Task 2
- Produces: New HTML structure with `.gallery-showcase`, `.gallery-photo-tile`, `.photo-label`, `.gallery-figure`

**Steps:**

- [ ] **Step 1: Identify current carousel markup**

Find lines 240–287 in `team.php`:
```html
<div class="gallery-showcase reveal">
  <div class="featured-section">
    ...carousel and featured image HTML...
  </div>
  <div class="gallery-thumbnails">
    ...8 thumb-item divs...
  </div>
</div>
```

This will be completely replaced.

- [ ] **Step 2: Write new scattered-grid markup**

Replace lines 240–287 with:
```html
<div class="gallery-showcase reveal">
  <?php foreach ($gallery_photos as $i => $photo): ?>
    <?php if ($i < 8): // First 8 are photos ?>
      <div class="gallery-photo-tile" style="--x: <?php echo $photo['position_x']; ?>px; --y: <?php echo $photo['position_y']; ?>px;">
        <img src="<?php echo htmlspecialchars($photo['photo_image']); ?>" alt="<?php echo htmlspecialchars($photo['photo_label']); ?>">
        <p class="photo-label"><?php echo htmlspecialchars($photo['photo_label']); ?></p>
      </div>
    <?php else: // 9th is figure illustration ?>
      <div class="gallery-figure" style="--x: <?php echo $photo['position_x']; ?>px; --y: <?php echo $photo['position_y']; ?>px;">
        <img src="<?php echo htmlspecialchars($photo['figure_image']); ?>" alt="Team figure illustration">
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  
  <!-- SVG connecting lines (generated by JavaScript) -->
  <svg class="gallery-lines" id="gallery-lines-svg" width="100%" height="100%"></svg>
</div>
```

- [ ] **Step 3: Verify markup renders**

Visit page. Inspect with DevTools. Expected:
- 8 `.gallery-photo-tile` divs with `style="--x: Npx; --y: Npx"`
- 1 `.gallery-figure` div with same positioning
- 1 `<svg class="gallery-lines">`
- All `<img>` tags with src and alt attributes

- [ ] **Step 4: Commit**

```bash
git add team.php
git commit -m "feat: replace carousel markup with scattered-grid structure

- Remove: featured-section, carousel-pause-btn, gallery-thumbnails, all carousel HTML
- Add: gallery-photo-tile divs (8) + gallery-figure div (1)
- Add: SVG container for connecting lines
- Loop: render from $gallery_photos array with position_x/y as CSS custom properties"
```

---

### Task 4: Add CSS for Desktop Scatter Layout

**Files:**
- Modify: `team.php` (add CSS in `<style>` block, ~line 291+)

**Interfaces:**
- Consumes: HTML structure from Task 3 (`.gallery-showcase`, `.gallery-photo-tile`, `.gallery-figure`, CSS custom properties `--x`, `--y`)
- Produces: Desktop layout with absolute positioning, shadows, border-radius, hover effects

**Steps:**

- [ ] **Step 1: Update `.gallery-showcase` for desktop**

Find the `.gallery-showcase` rule (~line 294). Replace with:
```css
.gallery-showcase {
  position: relative;
  width: 100%;
  height: 600px; /* Adjust based on photo arrangement; will be calculated or use min-height */
  background: #fff;
  padding: 0;
  align-items: flex-start;
  display: grid;
  grid-template-columns: 1fr; /* Single column for absolute positioning */
}
```

- [ ] **Step 2: Add `.gallery-photo-tile` styling**

Add new rule in `<style>`:
```css
.gallery-photo-tile {
  position: absolute;
  left: var(--x, 0);
  top: var(--y, 0);
  width: 160px;
  height: 200px;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  z-index: 1;
}

.gallery-photo-tile img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.gallery-photo-tile:hover {
  transform: scale(1.02);
  box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.photo-label {
  position: absolute;
  bottom: -30px;
  left: 0;
  right: 0;
  text-align: center;
  font-size: 0.9rem;
  color: #333;
  margin: 0;
  padding: 8px 0;
  white-space: nowrap;
}
```

- [ ] **Step 3: Add `.gallery-figure` styling**

Add new rule:
```css
.gallery-figure {
  position: absolute;
  left: var(--x, 0);
  top: var(--y, 0);
  width: 180px;
  height: 200px;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  z-index: 1;
}

.gallery-figure img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
```

- [ ] **Step 4: Add SVG connecting lines styling**

Add new rule:
```css
.gallery-lines {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 0;
  pointer-events: none;
}

.gallery-lines path {
  stroke: #333;
  stroke-width: 1.5px;
  opacity: 0.25;
  fill: none;
}
```

- [ ] **Step 5: Test desktop layout**

Open page in browser at desktop width (>1024px). Expected:
- 8 photos + 1 figure scattered at specified positions
- Labels below each photo (visible on desktop)
- Hover effects on photos (scale 1.02)
- SVG container present (will be populated by JavaScript in later task)

- [ ] **Step 6: Commit**

```bash
git add team.php
git commit -m "feat: add CSS for desktop scatter layout

- .gallery-showcase: position relative, single-column grid for absolute positioning
- .gallery-photo-tile: position absolute, uses --x/--y custom properties, 160x200px
- .photo-label: positioned below tile, centered, 0.9rem
- .gallery-figure: same as photo-tile, 180x200px
- .gallery-lines: SVG container, z-index 0 (behind tiles)
- Hover: scale 1.02 on photo tiles"
```

---

### Task 5: Add CSS for Tablet Grid Layout (3×3)

**Files:**
- Modify: `team.php` (add media query in `<style>`)

**Interfaces:**
- Consumes: HTML structure from Task 3 (same markup works for both layouts)
- Produces: Tablet layout using CSS Grid (3×3), hiding desktop-specific elements

**Steps:**

- [ ] **Step 1: Add media query for tablet (768px–1024px)**

In `<style>`, add at bottom:
```css
@media (max-width: 1024px) {
  .gallery-showcase {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    height: auto;
    position: static;
    padding: 40px 20px;
  }

  .gallery-photo-tile,
  .gallery-figure {
    position: static;
    width: auto;
    height: auto;
    aspect-ratio: 3 / 4;
  }

  .gallery-photo-tile img,
  .gallery-figure img {
    width: 100%;
    height: 100%;
  }

  .photo-label {
    position: static;
    bottom: auto;
    margin-top: 12px;
    white-space: normal;
    font-size: 0.9rem;
  }

  .gallery-lines {
    display: none;
  }
}
```

- [ ] **Step 2: Test tablet layout**

Resize browser to 900px width. Expected:
- 3×3 grid layout (9 elements total)
- Equal-sized tiles with aspect-ratio 3:4
- Labels below each tile
- SVG lines hidden
- No absolute positioning

- [ ] **Step 3: Commit**

```bash
git add team.php
git commit -m "feat: add CSS for tablet grid layout (3x3)

- Media query: @media (max-width: 1024px)
- Layout: grid-template-columns: repeat(3, 1fr), gap 24px
- Tiles: position static, aspect-ratio 3:4
- Labels: position static, margin-top 12px
- Lines: display none (hidden on tablet)"
```

---

### Task 6: Add CSS for Mobile Grid Layout (2×4)

**Files:**
- Modify: `team.php` (add media query in `<style>`)

**Interfaces:**
- Consumes: HTML structure from Task 3 (same markup works for all layouts)
- Produces: Mobile layout using CSS Grid (2×4), smaller gap

**Steps:**

- [ ] **Step 1: Add media query for mobile (<768px)**

In `<style>`, add below the tablet media query:
```css
@media (max-width: 768px) {
  .gallery-showcase {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    padding: 30px 16px;
  }

  .gallery-photo-tile,
  .gallery-figure {
    aspect-ratio: 3 / 4;
  }

  .photo-label {
    font-size: 0.85rem;
    margin-top: 8px;
  }
}
```

- [ ] **Step 2: Test mobile layout**

Resize browser to 480px width. Expected:
- 2×4 grid layout
- Smaller gaps (16px)
- Same aspect-ratio (3:4)
- Responsive label sizing
- No horizontal scroll needed

- [ ] **Step 3: Commit**

```bash
git add team.php
git commit -m "feat: add CSS for mobile grid layout (2x4)

- Media query: @media (max-width: 768px)
- Layout: grid-template-columns: repeat(2, 1fr), gap 16px
- Padding: 30px 16px (narrower on mobile)
- Label: font-size 0.85rem, margin-top 8px"
```

---

### Task 7: Add JavaScript to Calculate Container Height & Render Tiles

**Files:**
- Modify: `team.php` (add JavaScript in `<script>` block at end of file)

**Interfaces:**
- Consumes: HTML structure from Task 3, gallery data from PHP (embedded in data attributes or JSON)
- Produces: Dynamic height calculation for `.gallery-showcase` on desktop, photo tiles rendered

**Steps:**

- [ ] **Step 1: Extract gallery data to JavaScript**

In `team.php`, before closing `</body>`, add:
```php
<script>
var galleryData = <?php echo json_encode($gallery_photos); ?>;
</script>
```

This makes `$gallery_photos` available as `galleryData` in JavaScript.

- [ ] **Step 2: Add height calculation function**

In the `<script>` block, add:
```javascript
function calculateGalleryHeight() {
  var showcase = document.querySelector('.gallery-showcase');
  if (!showcase || window.innerWidth <= 1024) return; // Only on desktop
  
  var tiles = document.querySelectorAll('.gallery-photo-tile, .gallery-figure');
  var maxY = 0;
  tiles.forEach(function(tile) {
    var top = parseInt(tile.style.top.replace('px', '')) || 0;
    var height = tile.offsetHeight;
    maxY = Math.max(maxY, top + height);
  });
  
  showcase.style.height = (maxY + 60) + 'px'; // 60px buffer
}

// Run on load and on resize
document.addEventListener('DOMContentLoaded', calculateGalleryHeight);
window.addEventListener('resize', calculateGalleryHeight);
```

- [ ] **Step 3: Test height calculation**

Visit page at desktop (>1024px). Expected:
- `.gallery-showcase` has dynamic height based on lowest photo
- No overflow; all photos visible
- Height recalculates on window resize

- [ ] **Step 4: Commit**

```bash
git add team.php
git commit -m "feat: add JavaScript for gallery height calculation

- Extract gallery data: json_encode($gallery_photos) to JavaScript
- Function: calculateGalleryHeight() finds max Y position + height
- Events: DOMContentLoaded, resize
- Fallback: No calculation on tablet/mobile (auto height)"
```

---

### Task 8: Add JavaScript to Generate SVG Connecting Lines (Desktop Only)

**Files:**
- Modify: `team.php` (add JavaScript function)

**Interfaces:**
- Consumes: Rendered `.gallery-photo-tile` and `.gallery-figure` elements with positions
- Produces: SVG paths drawn between adjacent elements in `.gallery-lines`

**Steps:**

- [ ] **Step 1: Add SVG line generation function**

In `<script>`, add:
```javascript
function generateConnectingLines() {
  if (window.innerWidth <= 1024) return; // Desktop only
  
  var svg = document.getElementById('gallery-lines-svg');
  if (!svg) return;
  
  // Clear previous paths
  svg.innerHTML = '';
  
  var tiles = document.querySelectorAll('.gallery-photo-tile, .gallery-figure');
  var positions = [];
  
  tiles.forEach(function(tile) {
    var rect = tile.getBoundingClientRect();
    var svgRect = svg.getBoundingClientRect();
    positions.push({
      x: rect.left - svgRect.left + rect.width / 2,
      y: rect.top - svgRect.top + rect.height / 2
    });
  });
  
  // Draw lines between adjacent tiles (simple nearest-neighbor)
  for (var i = 0; i < positions.length - 1; i++) {
    var p1 = positions[i];
    var p2 = positions[i + 1];
    
    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M ' + p1.x + ' ' + p1.y + ' L ' + p2.x + ' ' + p2.y);
    svg.appendChild(path);
  }
}

// Run on load and resize
document.addEventListener('DOMContentLoaded', generateConnectingLines);
window.addEventListener('resize', generateConnectingLines);
```

- [ ] **Step 2: Test SVG line generation**

Visit page at desktop. Open DevTools → Inspect `.gallery-lines` SVG. Expected:
- SVG contains `<path>` elements
- Paths connect between photo center points
- Lines visible with dark gray color and low opacity
- Lines update on resize

- [ ] **Step 3: Fallback note**

If line generation is too complex or causes performance issues, this can be omitted (lines are aesthetic, not functional). Document this decision.

- [ ] **Step 4: Commit**

```bash
git add team.php
git commit -m "feat: add JavaScript to generate SVG connecting lines

- Function: generateConnectingLines() calculates tile positions
- Lines: Drawn between adjacent tiles using SVG paths
- Color: #333 @ opacity 0.25 (from CSS)
- Desktop only: Hidden on tablet/mobile
- Events: DOMContentLoaded, resize"
```

---

### Task 9: Test Responsive Design Across All Breakpoints

**Files:**
- Test: No code changes; visual verification only
- Reference: Spec @ `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/docs/superpowers/specs/2026-07-27-team-gallery-scatter-redesign.md`

**Steps:**

- [ ] **Step 1: Test desktop layout (>1024px)**

Resize browser to 1280px width.

Expected:
- 8 photos + 1 figure scattered at specified positions
- Labels visible below each photo
- Connecting lines visible between tiles
- Hover effects work (scale 1.02)
- No horizontal/vertical scroll needed
- All photos load without 404 errors

- [ ] **Step 2: Test tablet layout (768px–1024px)**

Resize browser to 900px width.

Expected:
- 3×3 grid layout (3 columns, 3 rows)
- Equal-sized tiles, aspect-ratio 3:4
- Labels below each tile
- Connecting lines hidden
- Gap: 24px between tiles
- No absolute positioning

- [ ] **Step 3: Test mobile layout (<768px)**

Resize browser to 480px width.

Expected:
- 2×4 grid layout (2 columns, 4 rows + 1 row for 9th element)
- Smaller tiles, same aspect-ratio
- Labels visible, smaller font
- Gap: 16px between tiles
- No horizontal scroll

- [ ] **Step 4: Test breakpoints**

Slowly resize from desktop → tablet → mobile. Expected:
- Layout changes smoothly at 1024px and 768px boundaries
- No jarring shifts or overflow
- All images visible at all sizes

- [ ] **Step 5: Test database integration**

Verify that photo labels and images load from database (not hardcoded).

- [ ] **Step 6: Test console for errors**

Open DevTools → Console. Reload page. Expected:
- No JavaScript errors
- No 404 errors for images
- No warnings about missing elements

- [ ] **Step 7: Commit test results**

```bash
git add -A  # No code changes, just noting test completion
git commit -m "test: verify responsive gallery layout across all breakpoints

Tested:
- Desktop (1280px): scatter layout, lines, labels, hover effects
- Tablet (900px): 3x3 grid, no lines, gap 24px
- Mobile (480px): 2x4 grid, gap 16px
- Breakpoints: smooth transitions at 1024px and 768px
- Database: photos and labels load from gallery_photos table
- Errors: no console errors, no 404s

All tests passed."
```

---

### Task 10: Integration & Final Polish

**Files:**
- Modify: `team.php` (clean up any debug code, verify section styling)
- Reference: Spec for aesthetic requirements

**Steps:**

- [ ] **Step 1: Remove debug code**

Search `team.php` for any `console.log()`, `alert()`, or debug comments. Delete if found.

- [ ] **Step 2: Verify section background**

The gallery section should have:
- Background: `#f9f9f9` or `#fff`
- Padding: 80px vertical (already set in `.gallery-section`)
- Container max-width: Matches site design

Confirm `.gallery-section` rule (should exist from previous work):
```css
.gallery-section {
  padding: 80px 0;
  background: #f9f9f9;
}
```

- [ ] **Step 3: Verify heading "Our Team At Work"**

Heading should display correctly above gallery. Check:
- Font: site's heading font
- Color: #333 or site's heading color
- Margin: 44px below

- [ ] **Step 4: Final visual inspection**

Load page in browser. View "Our Team At Work" section.

Visual checklist:
- [ ] Desktop: 8 photos + 1 figure scattered artfully, labels visible, lines visible
- [ ] Tablet: 3×3 grid, clean spacing
- [ ] Mobile: 2×4 grid, responsive
- [ ] All images load (no broken image icons)
- [ ] Labels display correctly (no overflow)
- [ ] No console errors
- [ ] Section styling matches site design

- [ ] **Step 5: Commit**

```bash
git add team.php
git commit -m "feat: final polish and integration

- Clean: Remove any debug code
- Verify: Section background, padding, heading styling
- Visual: Inspect all breakpoints (desktop/tablet/mobile)
- Quality: No console errors, all images load
- Ready: Gallery section complete and integrated"
```

---

## Execution

Plan complete and saved to `docs/superpowers/plans/2026-07-27-team-gallery-scatter-redesign.md`.

**Two execution options:**

**1. Subagent-Driven (recommended)** - I dispatch a fresh subagent per task, review between tasks, fast iteration

**2. Inline Execution** - Execute tasks in this session using executing-plans

**Which approach?**
