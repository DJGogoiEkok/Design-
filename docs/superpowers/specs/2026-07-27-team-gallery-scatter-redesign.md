# Team Gallery Artistic Scatter Redesign

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the "Our Team At Work" gallery section from a featured-carousel layout to an artistic scattered-grid layout inspired by contemporary gallery photography, with admin-controlled labels and figure illustration.

**Architecture:** Desktop displays an intentional scattered-grid composition with 8 photo tiles at specific coordinates, connecting lines, descriptive labels, and a figure illustration element. Tablet and mobile adapt to organized grid layouts (3×3, then 2×4) without scatter or lines. All content (photo labels, figure image) managed via admin panel.

**Tech Stack:** PHP/HTML (existing team.php), CSS Grid + absolute positioning for scatter, SVG for connecting lines, MySQL database for photo metadata.

---

## Global Constraints

- **Target file:** `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php`
- **Scope:** Only the "Behind The Scenes / Our Team At Work" gallery section; other team layouts (board of directors, partners) unchanged
- **Photo count:** 8 photos maximum (images/team/12-19.jpg); cannot exceed
- **Database:** Extend `team_members` table OR create new `gallery_photos` table with: `photo_image`, `photo_label`, `photo_position_x`, `photo_position_y` (desktop coordinates), `figure_image`
- **Admin panel location:** TBD (separate task after this design)
- **Responsive breakpoints:** Desktop (>1024px) = scatter layout; Tablet (768px–1024px) = 3×3 grid; Mobile (<768px) = 2×4 or 4×2 grid
- **Aesthetic:** White/light background (#fff or #f9f9f9), dark connecting lines (#333, opacity 0.2–0.3), minimalist gallery aesthetic
- **No JavaScript auto-play:** Unlike previous carousel design, this is a static gallery (no auto-cycling featured image)
- **Labels:** One label per photo, text editable from admin panel
- **Figure illustration:** Single admin-controlled image, positioned as grid element alongside photos

---

## Design Specification

### Layout Architecture

#### Desktop (>1024px) — Artistic Scatter

**Container:** Full-width `.gallery-showcase` with background color and padding. Contains 9 positioned elements: 8 photo tiles + 1 figure illustration. Absolute positioning uses CSS custom properties for coordinates (e.g., `--x: 10%`, `--y: 15%`).

**Intentional Grid Pattern:**
The 8 photos and 1 figure are arranged in a balanced, gallery-like scatter. Example layout (coordinates are placeholders; final positions set in CSS or admin data):

```
Layout example (desktop canvas):
┌─────────────────────────────────────────┐
│ Photo1      Photo2            Photo3    │
│   (small)     (med)            (large)  │
│                                         │
│ Photo4                    Figure        │
│   (med)                     (large)     │
│                                         │
│          Photo5      Photo6             │
│          (large)     (small)            │
│                                         │
│ Photo7                      Photo8      │
│ (small)                     (med)       │
└─────────────────────────────────────────┘
```

**Coordinate System:** Desktop scatter uses absolute positioning within the gallery container (width: 100%, height: auto). Positions are determined during implementation by calculating optimal visual balance across viewport widths. CSS custom properties (`--x`, `--y`) store offsets in pixels or percentages; exact positions finalized once all assets are available.

**Photo Tiles:**
- Container: Absolute positioned, fixed aspect ratio (e.g., 3:4, 4:3, 1:1 mixed for variety)
- Image: `object-fit: cover`, fills tile
- Size range: 120px–280px width (varies per tile)
- Border radius: 12px–20px (subtle, not highly rounded)
- Shadow: `0 4px 12px rgba(0,0,0,0.08)` (subtle depth)
- Label: Positioned below tile (margin-top: 12px), text-align center, font-size 0.9rem, color #333

**Figure Illustration:**
- Container: Absolute positioned, similar styling to photo tiles
- Image: Admin-controlled (file upload), positioned as one of the 9 grid elements
- Treated identically to photos (shadow, border-radius, label space below if needed)

**Connecting Lines:**
- SVG paths drawn between adjacent photo tiles
- Line color: #333 with opacity 0.2–0.3
- Line weight: 1px–2px
- Pattern: Links nearest neighbors (creates visual flow)
- Only visible on desktop; hidden on tablet/mobile

**Background:** Solid white (#fff) or light gray (#f9f9f9), full width

#### Tablet (768px–1024px) — Organized 3×3 Grid

**Layout:** CSS Grid, `grid-template-columns: repeat(3, 1fr)`, gap 24px. No absolute positioning; no connecting lines.

**Photo Tiles:**
- Equal size within grid cells
- Aspect ratio: 1:1 or 3:4 (consistent across all tiles)
- Border radius: 12px
- Shadow: Same as desktop
- Label: Below tile (same styling)

**Figure Illustration:** Flows into grid naturally as one of 9 elements

#### Mobile (<768px) — Compact 2×4 Grid

**Layout:** CSS Grid, `grid-template-columns: repeat(2, 1fr)`, gap 16px. Single-column fallback if needed.

**Photo Tiles:** Smaller than tablet, adapt to narrower viewport. Same aspect ratio and styling.

**Figure Illustration:** Flows into grid

---

### Data Structure & Admin Integration

#### Database Schema

**Option A (Extend existing table):**
Add columns to existing `team_members` table:
```sql
ALTER TABLE team_members ADD COLUMN gallery_photo_image VARCHAR(255);
ALTER TABLE team_members ADD COLUMN gallery_photo_label VARCHAR(100);
ALTER TABLE team_members ADD COLUMN gallery_position_x INT;
ALTER TABLE team_members ADD COLUMN gallery_position_y INT;
ALTER TABLE team_members ADD COLUMN gallery_figure_image VARCHAR(255);
ALTER TABLE team_members ADD COLUMN gallery_order INT;
```

**Option B (Create new table):**
```sql
CREATE TABLE gallery_photos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  photo_image VARCHAR(255) NOT NULL,
  photo_label VARCHAR(100),
  position_x INT,
  position_y INT,
  figure_image VARCHAR(255),
  gallery_order INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Recommendation:** Option B (separate table) for cleaner separation of concerns.

#### Frontend Data Model

PHP fetches gallery photos from database:
```php
$stmt = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt->fetchAll();
```

JavaScript receives array of photo objects:
```javascript
[
  { id: 1, photo_image: "images/team/12.jpg", label: "Creative", x: 50, y: 80, ... },
  { id: 2, photo_image: "images/team/13.jpg", label: "Ambitious", x: 200, y: 50, ... },
  // ... 8 photos
  { id: 9, figure_image: "images/team/figure.png", x: 350, y: 200, ... }
]
```

---

### Components

#### `.gallery-showcase` (Container)

- Display: Grid (desktop) or position relative (mobile/tablet use CSS Grid nested)
- Background: White or light gray
- Padding: 80px 0 (vertical), container max-width
- Position: relative (for absolutely positioned desktop layout)

#### `.gallery-photo-tile` (Photo Element)

- Container: `position: absolute` (desktop), `position: static` (mobile/tablet within grid)
- Width/height: CSS custom props `--w`, `--h`
- Left/top: CSS custom props `--x`, `--y` (desktop only)
- Aspect ratio: Mixed (3:4, 4:3, 1:1)
- Overflow: hidden (for border-radius)

**Markup:**
```html
<div class="gallery-photo-tile" style="--x: 50px; --y: 100px; --w: 160px; --h: 200px;">
  <img src="images/team/12.jpg" alt="Creative">
  <p class="photo-label">Creative</p>
</div>
```

#### `.gallery-figure` (Figure Illustration)

- Same container styling as photo tiles
- Positioned as one of 9 grid elements
- Image alt text: TBD by admin or default

#### `.photo-label`

- Position: Absolute below tile or relative within grid context
- Font size: 0.9rem
- Color: #333
- Text align: center
- Margin top: 12px

#### SVG Connecting Lines (Desktop Only)

- Container: `<svg class="gallery-lines" width="100%" height="100%">`
- Paths drawn programmatically based on photo positions
- Stroke: #333, stroke-width: 1.5px, opacity: 0.25
- Rendered behind photos (z-index: 0 for SVG, z-index: 1 for tiles)
- **Fallback:** If SVG generation proves complex, lines can be omitted from initial implementation without impacting core functionality (they are an aesthetic enhancement, not a functional requirement)

---

### Responsive Design

#### Desktop (>1024px)

- **Gallery showcase:** `position: relative`, allows absolute positioning of tiles
- **Photo tiles:** `position: absolute` with `--x`, `--y` coordinates
- **Connecting lines:** Visible SVG
- **Grid:** None (absolute positioning replaces grid)

#### Tablet (768px–1024px)

- **Gallery showcase:** `display: grid; grid-template-columns: repeat(3, 1fr)`
- **Photo tiles:** `position: static` (normal flow within grid)
- **Connecting lines:** Hidden (`display: none`)
- **Gap:** 24px

#### Mobile (<768px)

- **Gallery showcase:** `display: grid; grid-template-columns: repeat(2, 1fr)`
- **Photo tiles:** `position: static`
- **Connecting lines:** Hidden
- **Gap:** 16px
- **Alternative:** Fallback to single column if needed

---

### Styling & Aesthetic

#### Color Palette

- **Background:** #fff (white) or #f9f9f9 (light gray)
- **Lines:** #333 (dark gray) @ opacity 0.25
- **Text:** #333 (labels)
- **Shadows:** `0 4px 12px rgba(0,0,0,0.08)` (subtle)

#### Typography

- **Photo labels:** 0.9rem, sans-serif, font-weight 400–500, color #333
- **No heading or description** within gallery itself

#### Spacing

- **Tile border radius:** 12px–20px (subtle)
- **Label margin top:** 12px
- **Grid gap (tablet):** 24px
- **Grid gap (mobile):** 16px
- **Container padding:** 80px vertical

---

### Interactive Behavior

**Desktop:**
- Hover effect on photo tiles: subtle scale (1.02) or shadow enhancement
- No auto-play or carousel behavior
- Static, gallery-style presentation

**Mobile/Tablet:**
- Tap on photo: Optional lightbox view (TBD in implementation phase)
- No hover effects
- Static grid

---

### Admin Panel Integration (Future)

Admin panel allows:
1. **Upload/change photo image** for each of 8 photos
2. **Edit label text** for each photo
3. **Upload/change figure illustration**
4. **Reorder photos** (gallery_order field)
5. **(Optional) Fine-tune desktop coordinates** if scatter positions need adjustment

---

### Data & Interactions

#### Images

- **Photo tiles:** 8 images (images/team/12-19.jpg or admin-uploaded)
- **Figure illustration:** 1 admin-controlled image
- **Total:** 9 images in gallery

#### No JavaScript Interactions

- No auto-play
- No click handlers (static gallery)
- Optional: Lightbox on click (separate task)
- SVG lines generated server-side or CSS Grid fallback

---

### Testing Checklist

- [ ] Desktop layout: 8 photos + 1 figure scattered intentionally, labels visible, connecting lines drawn
- [ ] Tablet layout: 3×3 grid, no lines, labels visible
- [ ] Mobile layout: 2×4 grid, responsive, labels visible
- [ ] All photos load without errors
- [ ] Labels display correctly (admin-provided text)
- [ ] Figure illustration displays correctly
- [ ] Hover effects work (desktop)
- [ ] Responsive breakpoints at 1024px and 768px
- [ ] No console errors
- [ ] Database queries return correct photo data

---

## Success Criteria

- ✓ Desktop gallery displays intentional scattered-grid layout (matching reference aesthetic)
- ✓ Tablet/mobile adapt to organized grid layouts
- ✓ All 8 photos + 1 figure visible and properly labeled
- ✓ Connecting lines enhance visual flow on desktop
- ✓ Admin panel integration points identified (future implementation)
- ✓ Responsive design adapts smoothly across all breakpoints
- ✓ Minimalist, gallery-like aesthetic achieved
- ✓ Labels editable from admin interface

