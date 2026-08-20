# Team Gallery Redesign Spec

**Date**: July 26, 2026  
**Project**: Design Plus Team Page Gallery  
**Status**: Approved for Implementation

## Overview

Redesign the "Our Team At Work" gallery section to adopt a modern side-by-side layout inspired by contemporary art gallery websites. The featured image displays on the left with auto-cycling through all 8 team photos every 4 seconds, while the right side shows a decorative vertical stack of all 8 thumbnail cards as a visual index.

## Design Goals

- Modern, minimalist aesthetic matching Design Plus brand
- Photography-focused with clean spacing
- Visual gallery display (read-only, non-interactive thumbnails)
- Smooth auto-play carousel for featured image
- Responsive layout that adapts to different screen sizes

## Layout Architecture

### Grid Structure
- **Container**: CSS Grid with 2 columns
  - Column 1: 400px (featured section)
  - Column 2: 1fr (vertical cards section)
  - Gap: 50px
  - Alignment: `align-items: flex-start`

### Left Section: Featured Image Area
- **Image Container**: 400px wide × 520px tall
  - Border radius: 32px (top), sharp (bottom)
  - Drop shadow: `0 20px 60px rgba(0,0,0,0.15)`
  - Object-fit: cover
  - Display: block

- **Caption Box**: Below featured image
  - Background: White (#fff)
  - Border radius: 0 (top), 32px (bottom)
  - Padding: 20px
  - Layout: Flex row with avatar + text
  - Avatar: 48px circular with 2px border
  - Text: Team name (1rem bold), description (0.85rem, #666)
  - Drop shadow: `0 20px 60px rgba(0,0,0,0.15)`

### Right Section: Vertical Cards Carousel
- **Container**: Flex column with scroll
  - Width: 150px (carousel inner width, excluding buttons)
  - Max height: 520px (matches featured image height)
  - Overflow-x: auto
  - Overflow-y: hidden
  - Scroll behavior: smooth
  - Scrollbar: hidden (scrollbar-width: none, -webkit-scrollbar: display none)
  - Gap: 12px between cards
  - Flex-wrap: nowrap (forces horizontal scrolling, not wrapping)

- **Individual Card** (.thumb-item)
  - Width: 60px (desktop), 50px (mobile)
  - Height: 520px (desktop), 405px (mobile)
  - Border radius: 32px (all corners - rounded pills)
  - Overflow: hidden
  - Box shadow: `0 12px 40px rgba(0,0,0,0.12)`
  - Flex properties: flex-shrink: 0, flex-grow: 0, min-width: 60px
  - Hover effect: translateY(-6px) with enhanced shadow
  - Cursor: pointer (for visual affordance, though non-interactive)
  - Transition: transform 0.3s ease, box-shadow 0.3s ease

- **Image**: 100% width × 100% height, object-fit: cover

### Navigation Buttons (Carousel Controls)
- **Prev/Next Buttons**: Outside carousel, flanking it
  - Size: 40px × 40px circles
  - Background: White with 2px border (#ddd)
  - Border radius: 50%
  - Font size: 24px
  - Hover: scale(1.05), background #f0f0f0, border #999
  - Active: scale(0.95)
  - Flex-shrink: 0

## Featured Image Auto-Play Behavior

### Auto-Cycle Timing
- **Interval**: 4 seconds (4000ms) between image transitions
- **Start**: Automatically on page load
- **Duration**: Cycles through all 8 images (12-19.jpg), then loops back to first

### Image Rotation
- Index starts at 0 (first thumbnail/image)
- Each 4 seconds: increment index, fetch image src from data-src attribute
- Wrap around: `index % thumbs.length`

### Visual Feedback
- Featured image fades/updates smoothly (no hard cuts)
- No thumbnail highlight (purely decorative right side)
- Carousel does NOT auto-scroll (stays at initial scroll position)

### Pause Behavior
- **Pause trigger**: Mouse enters featured image area
- **Resume trigger**: Mouse leaves featured image area
- Uses: `mouseenter` and `mouseleave` events

## Styling & Aesthetic

### Color Palette
- Background: #f9f9f9 (light gray section background)
- Text: #333 (dark text for descriptions)
- Borders: #ddd (subtle button borders)
- Shadows: Use existing subtle shadows, no bold colors or gradients

### Typography
- Featured heading: 1rem, font-weight 700
- Featured description: 0.85rem, color #666
- No decorative text on thumbnail cards

### Spacing & Layout
- Section padding: 80px top/bottom
- Inter-element gaps: 50px (featured ↔ cards), 12px (card ↔ card)
- Card padding: 4px (around scrollbar area)
- Button gap from carousel: 12px

## Responsive Design

### Desktop (>1024px)
- Full side-by-side layout as described
- Grid: 400px | 1fr
- Featured image: 450px height
- Thumbnails: 60px wide × 520px tall
- Carousel: 150px total width (60px cards + gaps)

### Tablet (768px–1024px)
- **Layout change**: Single column grid (grid-template-columns: 1fr)
- **Gap**: 40px between sections
- Featured image: 350px height
- Thumbnails: Still vertical but narrower viewport
- Carousel: Responsive width

### Mobile (<768px)
- Single column (1fr)
- Featured image: 350px height
- Thumbnails: 50px wide × 405px tall
- Carousel: flex-wrap: nowrap (forces horizontal scroll, not vertical)
- Buttons remain functional

## Data & Interactions

### Images
- Featured: ID `featured-img`, updates via `changeFeatured(el)` function
- Thumbnails: `.thumb-item` elements with `data-src="images/team/XX.jpg"`
- Total: 8 images (images/team/12.jpg through 19.jpg)

### JavaScript Functions

#### `changeFeatured(el)`
- Accepts: thumbnail element
- Updates: featured image src from data-src
- Sets: all thumbnails to opacity 0.3, clicked thumbnail to opacity 1
- **Note**: Currently used for click interactions, but in this design thumbnails are non-interactive (decorative only)

#### `autoCarousel()`
- Runs every 4 seconds
- Gets current thumbnail by index
- Calls `changeFeatured()` to update featured image
- Increments carousel index, wraps around

#### `scrollThumbnails(direction)`
- Manual carousel scroll (called by prev/next buttons)
- Scrolls carousel by 100px left/right
- Uses `scrollBy()` with smooth behavior
- **Note**: In this design, carousel remains static (no auto-scroll)

### Event Listeners
- `DOMContentLoaded`: Initialize auto-play, set first thumbnail as active
- `mouseenter` (featured image): Pause auto-play interval
- `mouseleave` (featured image): Resume auto-play interval

## Error Handling & Edge Cases

- **Missing images**: If `data-src` missing, use fallback placeholder
- **No thumbnails**: Check `querySelectorAll('.thumb-item').length > 0` before initializing
- **Rapid hovers**: Auto-play pause/resume debounced by clearInterval/setInterval pattern

## Testing Checklist

- [ ] Auto-play cycles through all 8 images every 4 seconds
- [ ] Featured image updates smoothly
- [ ] Pause on hover over featured section works
- [ ] Resume on mouse leave works
- [ ] Navigation buttons scroll carousel left/right
- [ ] Thumbnail cards display at correct sizes (60px × 520px desktop)
- [ ] Cards have proper rounded corners (32px pills)
- [ ] Responsive layout stacks correctly on tablet (<1024px)
- [ ] Mobile layout adapts (50px × 405px cards)
- [ ] No console errors
- [ ] Carousel scrolls properly (484px content in 150px container)
- [ ] Smooth transitions on image change
- [ ] Subtle shadows render correctly

## Implementation Order

1. Update CSS grid layout (gallery-showcase)
2. Adjust featured section styling (image + caption)
3. Resize vertical card section (width: 150px, no flex)
4. Update thumbnail card dimensions (60px × 520px)
5. Fix carousel scrolling (flex-wrap: nowrap)
6. Update auto-play JavaScript
7. Remove click interaction from thumbnails (decorative only)
8. Test responsive breakpoints
9. Verify smooth transitions and animations

## Success Criteria

- ✓ Left featured image auto-cycles every 4 seconds
- ✓ Right vertical cards display all 8 photos as rounded pills
- ✓ No visible interaction on thumbnail cards (decorative)
- ✓ Minimal, clean aesthetic with subtle shadows
- ✓ Responsive layout adapts to all screen sizes
- ✓ Auto-play pauses on featured image hover
- ✓ Visual hierarchy emphasizes photography over controls
