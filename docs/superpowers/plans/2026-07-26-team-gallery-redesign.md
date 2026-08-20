# Team Gallery Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the "Our Team At Work" gallery to display a featured image on the left (auto-cycling every 4 seconds) with a vertical stack of 8 decorative thumbnail cards on the right, using a side-by-side layout inspired by art gallery designs.

**Architecture:** Single-file implementation (team.php contains HTML + CSS + JavaScript). Update the gallery-showcase grid layout to match the spec, resize all components, restructure the carousel to be a decorative-only display, and modify JavaScript to remove click interactions while maintaining auto-play cycling of the featured image.

**Tech Stack:** HTML, CSS (Grid/Flexbox), Vanilla JavaScript (no external libraries)

## Global Constraints

- File: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php` (single file modification)
- Grid layout: `grid-template-columns: 400px 1fr` (desktop)
- Featured image: 450px height, cards: 60px width × 520px height (desktop)
- Card border-radius: 32px (all corners - rounded pills)
- Auto-play interval: 4000ms (4 seconds)
- No click interactions on thumbnail cards (decorative only)
- Styling: Minimal, subtle shadows, no gradients/bold colors
- Responsive breakpoints: Desktop (>1024px), Tablet (768px-1024px), Mobile (<768px)

---

## Task 1: Update HTML Structure - Remove Old Carousel Markup

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php:240-290` (gallery section)

**Interfaces:**
- Consumes: Current carousel-container, carousel-btn, gallery-thumbnails structure
- Produces: Simplified structure with featured-section and gallery-thumbnails (no buttons/wrapper)

- [ ] **Step 1: Open team.php and locate the gallery section**

Find the `<section class="gallery-section">` starting around line 233. Identify the current structure with `carousel-container`, `carousel-btn` buttons, and 8 thumb-items.

- [ ] **Step 2: Replace the entire gallery-showcase HTML with new structure**

Replace this section (lines 240-290):
```html
    <div class="gallery-showcase reveal">
      <div class="carousel-container">
        <button class="carousel-btn carousel-prev" onclick="scrollThumbnails(-1)">‹</button>
        <div class="gallery-thumbnails" id="thumbnailsCarousel">
          <div class="thumb-item" onclick="changeFeatured(this)" data-src="images/team/12.jpg">
            <img src="images/team/12.jpg" alt="Team work" loading="lazy">
            <span class="thumb-indicator" style="background: #FF6B6B;"></span>
          </div>
          <!-- ... 7 more items ... -->
        </div>
        <button class="carousel-btn carousel-next" onclick="scrollThumbnails(1)">›</button>
      </div>
    </div>
```

With this new structure:
```html
    <div class="gallery-showcase reveal">
      <div class="featured-section">
        <img id="featured-img" src="images/team/12.jpg" alt="Team at work" loading="lazy" class="featured-image">
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
```

Key changes:
- Removed `<carousel-container>` wrapper and navigation buttons
- Added `<featured-section>` div containing featured image + caption
- Removed `onclick="changeFeatured(this)"` from all thumb-items (decorative only)
- Removed `thumb-indicator` spans from all cards
- Removed `style="--badge-color: ..."` attributes
- Removed caption avatar image (using placeholder src instead)

- [ ] **Step 3: Verify structure looks correct**

The new gallery-showcase should have exactly 2 direct children:
1. `featured-section` (left)
2. `gallery-thumbnails` (right)

Visual check: Count div elements - should see featured-section once, gallery-thumbnails once with 8 thumb-items inside.

- [ ] **Step 4: Commit HTML changes**

```bash
git add team.php
git commit -m "refactor: restructure gallery HTML for new side-by-side layout

- Remove carousel-container and navigation buttons
- Replace with featured-section (left) and gallery-thumbnails (right)
- Remove onclick handlers from thumbnails (decorative only)
- Remove badge indicators and styling attributes
- Prepare for CSS grid layout"
```

---

## Task 2: Update CSS - Grid Layout and Featured Section

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php:294-400` (style section)

**Interfaces:**
- Consumes: HTML structure from Task 1
- Produces: CSS grid layout (400px | 1fr), featured-section styling, featured-image/caption sizing

- [ ] **Step 1: Locate the style section and remove old CSS**

Find `<style>` tag around line 294. Delete all existing CSS rules for:
- `.carousel-container`
- `.carousel-btn`
- `.gallery-thumbnails` (old styling)
- Any media query rules related to these classes

Keep only:
- `.gallery-section`
- `.gallery-showcase`

- [ ] **Step 2: Rewrite the gallery-showcase CSS**

Replace the `.gallery-showcase` rule with:
```css
.gallery-showcase {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 50px;
  align-items: flex-start;
}
```

Remove any `order` properties or flex settings - this should be a simple 2-column grid.

- [ ] **Step 3: Add featured-section CSS**

After `.gallery-showcase` rule, add:
```css
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
```

- [ ] **Step 4: Update gallery-thumbnails CSS**

Replace any existing `.gallery-thumbnails` rule with:
```css
.gallery-thumbnails {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  padding: 4px 0;
  width: 150px;
  flex-shrink: 0;
  scrollbar-width: none;
  flex-wrap: nowrap;
}

.gallery-thumbnails::-webkit-scrollbar {
  display: none;
}
```

- [ ] **Step 5: Update .thumb-item CSS**

Replace the existing `.thumb-item` rule with:
```css
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
```

Remove any rules for `.thumb-indicator` and `.badge` (they no longer exist in HTML).

- [ ] **Step 6: Update media queries**

Replace all existing media queries with:
```css
@media (max-width: 1024px) {
  .gallery-showcase {
    grid-template-columns: 1fr;
    gap: 40px;
  }
}

@media (max-width: 768px) {
  .featured-image {
    height: 350px;
  }
  
  .gallery-thumbnails {
    flex-wrap: nowrap;
  }
  
  .thumb-item {
    width: 50px;
    height: 405px;
    min-width: 50px;
  }
}
```

- [ ] **Step 7: Verify CSS structure**

Check that:
- No `.carousel-container`, `.carousel-btn`, or `.badge` rules exist
- `.gallery-showcase` is `display: grid` with `grid-template-columns: 400px 1fr`
- `.featured-image` is 450px tall with rounded top corners
- `.thumb-item` is 60px wide × 520px tall (desktop)
- Media queries exist for tablet and mobile

- [ ] **Step 8: Commit CSS changes**

```bash
git add team.php
git commit -m "style: update CSS for new side-by-side gallery layout

- Change gallery-showcase to grid: 400px | 1fr
- Add featured-section styling (image + caption)
- Update thumbnail cards: 60px × 520px rounded pills
- Remove carousel button and badge styling
- Update responsive breakpoints
- Hide scrollbar on thumbnail carousel"
```

---

## Task 3: Update JavaScript - Auto-Play Without Carousel Scroll

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php:375-395` (script section)

**Interfaces:**
- Consumes: HTML structure from Task 1, featured-img element, thumb-item elements
- Produces: Auto-play function that cycles featured image, removes click handlers

- [ ] **Step 1: Locate and replace entire script section**

Find the `<script>` tag around line 375. Replace everything with:

```javascript
var carouselIndex = 0;
var autoCarouselInterval;

function autoCarousel() {
  var thumbs = document.querySelectorAll('.thumb-item');
  if(thumbs.length === 0) return;

  var nextThumb = thumbs[carouselIndex % thumbs.length];
  var src = nextThumb.getAttribute('data-src');
  document.getElementById('featured-img').src = src;

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

  // Pause on hover over featured image
  var featuredImg = document.querySelector('.featured-image');
  if(featuredImg) {
    featuredImg.addEventListener('mouseenter', function() {
      clearInterval(autoCarouselInterval);
    });
    
    featuredImg.addEventListener('mouseleave', function() {
      autoCarouselInterval = setInterval(autoCarousel, 4000);
    });
  }
});
```

Key changes from old script:
- Removed `changeFeatured()` function (thumbnails are non-interactive)
- Removed `scrollThumbnails()` function (carousel doesn't scroll)
- Simplified `autoCarousel()` to only update featured image and opacity states
- Updated hover listeners to attach to `.featured-image` instead of carousel
- Removed all click event handlers (thumbnails are decorative)

- [ ] **Step 2: Verify script logic**

Check that:
- `autoCarousel()` function exists and updates `featured-img` src
- `DOMContentLoaded` initializes auto-play interval
- First thumbnail is set to opacity 1, others to 0.3
- Hover/leave events pause/resume auto-play
- No click handlers exist

- [ ] **Step 3: Commit JavaScript changes**

```bash
git add team.php
git commit -m "feat: update JavaScript for auto-play gallery

- Simplify autoCarousel() to cycle featured image only
- Remove changeFeatured() and scrollThumbnails() functions
- Remove all click handlers (thumbnails decorative)
- Update hover listeners to attach to featured image
- Set auto-play interval to 4000ms
- Initialize first thumbnail with opacity 1"
```

---

## Task 4: Test and Verify Implementation

**Files:**
- Test: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php` (visual testing via browser)

**Interfaces:**
- Consumes: All changes from Tasks 1-3
- Produces: Verified working gallery implementation

- [ ] **Step 1: Reload team.php in browser**

Navigate to `http://localhost:8080/team.php` and perform a hard refresh (Cmd+Shift+R or Ctrl+Shift+R).

- [ ] **Step 2: Verify layout visually**

Visual checks:
- [ ] Featured image appears on LEFT side (400px wide)
- [ ] Featured image is large (450px tall)
- [ ] Caption box appears below featured image with white background and rounded bottom
- [ ] Vertical thumbnail cards appear on RIGHT side
- [ ] Cards are narrow (60px wide) and tall (520px)
- [ ] Cards have rounded corners (pill-shaped)
- [ ] All 8 thumbnail images visible vertically
- [ ] No navigation buttons visible (< and > buttons removed)
- [ ] No colored badge indicators on cards
- [ ] Spacing looks balanced (50px gap between left and right sections)

- [ ] **Step 3: Test auto-play functionality**

- Open browser DevTools (F12 or Cmd+Option+I)
- Go to Console tab
- Watch the featured image:
  - Should change automatically every 4 seconds
  - Should cycle through all 8 images (12.jpg through 19.jpg)
  - Should loop back to first after 8th image
- Check thumbnail opacity:
  - Current thumbnail should be fully opaque (opacity: 1)
  - Other thumbnails should be faded (opacity: 0.3)

- [ ] **Step 4: Test hover pause behavior**

- Move mouse over the featured image area
- Auto-play should pause (image stops changing)
- Wait 5+ seconds to confirm it doesn't auto-play
- Move mouse away from featured image
- Auto-play should resume (image changes after 4 seconds)

- [ ] **Step 5: Test click behavior on thumbnails**

- Click on a thumbnail card
- Featured image should NOT change (thumbnails are decorative)
- Verify no JavaScript errors in console
- Verify cursor shows pointer on hover (visual affordance only)

- [ ] **Step 6: Test responsive layout (Tablet)**

- Resize browser to 768px-1024px width
- Gallery should stack vertically (featured on top, cards below)
- Featured image should be 350px tall (mobile breakpoint)
- Cards should still be visible below
- Layout should reflow smoothly

- [ ] **Step 7: Test responsive layout (Mobile)**

- Resize browser to <768px width
- Gallery remains stacked vertically
- Featured image: 350px tall
- Thumbnail cards: 50px wide × 405px tall
- Cards should still be scrollable if needed
- Text should remain readable

- [ ] **Step 8: Check console for errors**

- Open DevTools Console
- Reload page
- Verify NO JavaScript errors appear
- Verify NO 404 errors for images
- Verify auto-play starts automatically

- [ ] **Step 9: Commit verification (no code changes)**

```bash
git commit --allow-empty -m "test: verify gallery redesign implementation

Desktop layout:
- Featured image (400px × 450px) left side ✓
- Thumbnail cards (60px × 520px) right side ✓
- 8 images visible vertically ✓
- Rounded pill-shaped cards ✓
- No navigation buttons ✓

Auto-play:
- Featured image cycles every 4 seconds ✓
- Cycles through all 8 images ✓
- Pauses on hover ✓
- Resumes on mouse leave ✓

Responsiveness:
- Tablet (768px-1024px): stacks vertically ✓
- Mobile (<768px): responsive sizing ✓

Interactions:
- Thumbnails are non-interactive (decorative) ✓
- No console errors ✓"
```

---

## Task 5: Performance and Final Polish

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php` (final tweaks)

**Interfaces:**
- Consumes: Working gallery from Task 4
- Produces: Optimized, production-ready implementation

- [ ] **Step 1: Verify image loading and caching**

Open DevTools → Network tab:
- All 8 team photos should load
- No broken image links (404 errors)
- Images use lazy loading (`loading="lazy"`)
- Check that featured image updates smoothly (no visible lag)

- [ ] **Step 2: Test animation smoothness**

- Watch featured image transitions
- Should fade smoothly between images (CSS transitions in place)
- Thumbnail opacity changes should be smooth
- No jittery behavior or jarring transitions

- [ ] **Step 3: Verify CSS specificity and conflicts**

Open DevTools → Elements tab:
- Select featured-image element
- Check computed styles match intended values:
  - width: 100%
  - height: 450px
  - border-radius: 32px 32px 0 0
  - box-shadow: 0 20px 60px rgba(0,0,0,0.15)
- Select a thumb-item element
- Check computed styles:
  - width: 60px
  - height: 520px
  - border-radius: 32px
  - box-shadow: 0 12px 40px rgba(0,0,0,0.12)

- [ ] **Step 4: Test with different image aspect ratios**

The team images might have different dimensions. Verify:
- Images maintain aspect ratio with `object-fit: cover`
- No distortion or stretching
- Images fill their containers properly
- No white space or overflow

- [ ] **Step 5: Final visual polish**

Visually inspect:
- Caption text is readable (good contrast on white background)
- Spacing/padding looks balanced
- Shadows are subtle and not overwhelming
- Hover effects on thumbnails (lift on hover) work smoothly
- First thumbnail starts with full opacity (not faded)

- [ ] **Step 6: Test in different browsers (if available)**

If possible, test in:
- Chrome/Edge (Chromium)
- Firefox
- Safari (if on macOS)

Verify:
- Layout renders identically
- Auto-play works consistently
- No browser-specific issues

- [ ] **Step 7: Final commit**

```bash
git add team.php
git commit -m "polish: finalize gallery redesign implementation

- Verify image loading and caching
- Test animation smoothness
- Validate CSS computed styles
- Verify object-fit and aspect ratios
- Test hover interactions
- Cross-browser compatibility check
- Performance optimization"
```

---

## Implementation Checklist

Before considering this complete, verify:

- [ ] HTML structure updated (featured-section + gallery-thumbnails)
- [ ] CSS grid layout implemented (400px | 1fr on desktop)
- [ ] Featured image: 450px tall with rounded corners
- [ ] Thumbnail cards: 60px × 520px with 32px border-radius
- [ ] Auto-play cycles featured image every 4 seconds
- [ ] Thumbnails are decorative (no click interactions)
- [ ] Hover pause/resume working on featured image
- [ ] Opacity states updated with auto-play
- [ ] Responsive layout stacks on tablets and mobile
- [ ] No navigation buttons visible
- [ ] No badge indicators on cards
- [ ] No console errors
- [ ] All 8 images load correctly
- [ ] Smooth transitions between images
- [ ] Visual polish complete (shadows, spacing, typography)

---

## Success Criteria (from Spec)

- ✓ Left featured image auto-cycles every 4 seconds
- ✓ Right vertical cards display all 8 photos as rounded pills
- ✓ No visible interaction on thumbnail cards (decorative)
- ✓ Minimal, clean aesthetic with subtle shadows
- ✓ Responsive layout adapts to all screen sizes
- ✓ Auto-play pauses on featured image hover
- ✓ Visual hierarchy emphasizes photography over controls
