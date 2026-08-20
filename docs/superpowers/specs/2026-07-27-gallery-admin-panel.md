# Gallery Admin Panel Design

**Date:** July 27, 2026  
**Status:** Approved for Implementation

## Overview

Add gallery management section to existing admin panel (admin/team.php). Allows admin users to:
- Upload and manage gallery background image
- Edit photo labels for 8 gallery photos
- Reorder photos (change gallery_order)
- Adjust desktop scatter layout positions (position_x, position_y)

Integrates seamlessly with existing Bootstrap-based admin UI.

---

## Global Constraints

- **Target files:** `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php` (add gallery section)
- **Database:** Extend existing `gallery_photos` table + new `gallery_settings` table
- **UI Framework:** Bootstrap 5.3 (matches existing admin)
- **Auth:** Require session admin login (existing auth system)
- **Scope:** Gallery management only (not other team member management)
- **Photos:** Exactly 8 photos (cannot add/remove, only edit/reorder)
- **Background:** Single background image for gallery section

---

## Architecture

### Database Schema

#### New Table: `gallery_settings`
```sql
CREATE TABLE gallery_settings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  background_image_path TEXT,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Stores path to gallery background image. Single row (id=1).

#### Existing Table: `gallery_photos` (extend usage)
Already has columns:
- `id` (INTEGER) - photo ID
- `photo_image` (TEXT) - photo file path (read-only for now)
- `photo_label` (TEXT) - editable label
- `position_x` (INTEGER) - editable X position (desktop scatter)
- `position_y` (INTEGER) - editable Y position (desktop scatter)
- `gallery_order` (INTEGER) - editable order/sequence
- `figure_image` (TEXT) - not used in admin (reserved)

---

## UI Components

### 1. Background Image Management Section

**Title:** "Gallery Background"

**Components:**
- Current background thumbnail (image preview, 300px max-width)
- Upload form:
  - File input (accept image/* only)
  - "Upload Background" button
  - "Remove Background" button (clear path to null)
- Status message: "Background updated successfully" or error

**Functionality:**
- Accept: JPG, PNG, WebP (validate on server)
- Save to folder: `images/gallery/backgrounds/`
- Store path in `gallery_settings` table (id=1)
- Filename format: `background_TIMESTAMP.ext` (prevent naming conflicts)
- Max file size: 5MB

---

### 2. Gallery Photos Management Section

**Title:** "Gallery Photos (8 Total)"

**Layout:** Table with columns:
1. **Thumbnail** (50px × 50px image preview)
2. **Label** (text input, 30 chars max)
3. **Position X** (number input, 0-1000)
4. **Position Y** (number input, 0-800)
5. **Order** (number input, 1-8)
6. **Actions** (see preview / reset to default)

**Rows:** 8 rows (one per photo), sorted by gallery_order

**Functionality:**
- Edit label: inline text field, live validation
- Edit X/Y: number inputs for scatter positioning
- Edit order: drag-to-reorder OR type order number
  - When order changes, resequence all photos (maintain 1-8)
- Preview button: shows scatter layout preview modal
- Reset button: restore default positions for this photo
- Save all button: submit all changes in one POST

**Interactions:**
- Rows can be reordered via drag-drop (optional: fallback to order number input)
- Changes not saved until "Save Gallery" button clicked
- Validation: 
  - Labels: required, max 50 chars
  - Positions: numbers only, X: 0-1000, Y: 0-800
  - Order: numbers only, 1-8, no duplicates

---

## Data Flow

### On Page Load (GET /admin/team.php)

1. Check admin session (existing auth)
2. Fetch `gallery_settings` → get `background_image_path`
3. Fetch all `gallery_photos` ordered by `gallery_order`
4. Render background section with current thumbnail
5. Render photos table with all 8 rows

### On Background Upload (POST)

1. Validate file type (image only)
2. Validate file size (<5MB)
3. Move to `images/gallery/backgrounds/` with timestamp filename
4. Update `gallery_settings` table (id=1) with new path
5. Redirect with success message
6. Re-render page with new background

### On Photo Changes (POST action=update_gallery)

1. Receive form data:
   - Array of photo IDs
   - Array of labels
   - Array of X positions
   - Array of Y positions
   - Array of orders
2. Validate all inputs
3. For each photo, UPDATE `gallery_photos`:
   - SET `photo_label = ?`
   - SET `position_x = ?`
   - SET `position_y = ?`
   - SET `gallery_order = ?`
4. Commit transaction
5. Redirect with success message
6. Re-render page with updated data

---

## Form Markup & Styling

### Background Section
```html
<div class="card mb-4">
  <div class="card-header">
    <h5>Gallery Background</h5>
  </div>
  <div class="card-body">
    <div id="background-preview">
      <!-- Shows current background thumbnail or placeholder -->
    </div>
    <form method="POST" enctype="multipart/form-data" class="mt-3">
      <input type="hidden" name="action" value="upload_background">
      <input type="file" name="background_image" accept="image/*" required>
      <button type="submit" class="btn btn-primary">Upload Background</button>
      <button type="button" class="btn btn-outline-danger">Remove Background</button>
    </form>
  </div>
</div>
```

### Photos Management Section
```html
<div class="card">
  <div class="card-header">
    <h5>Gallery Photos (8 Total)</h5>
  </div>
  <div class="card-body">
    <form method="POST">
      <input type="hidden" name="action" value="update_gallery">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Thumbnail</th>
            <th>Label</th>
            <th>Position X</th>
            <th>Position Y</th>
            <th>Order</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="photos-table">
          <!-- 8 rows dynamically populated -->
        </tbody>
      </table>
      <button type="submit" class="btn btn-success">Save Gallery</button>
      <button type="button" class="btn btn-outline-secondary" id="preview-btn">Preview Layout</button>
    </form>
  </div>
</div>
```

---

## Responsive Design

**Desktop (>768px):** Full table, drag-to-reorder rows, preview modal

**Tablet (768px):** Condensed table, order inputs instead of drag (smaller screen)

**Mobile (<576px):** Stack form fields vertically, hide thumbnail column, show order/actions inline

---

## Error Handling

**File Upload Errors:**
- File type invalid: "Only image files (JPG, PNG, WebP) are allowed"
- File too large: "File must be less than 5MB"
- Upload failed: "Error uploading file. Please try again"

**Gallery Update Errors:**
- Validation failed: "Please check all fields. Labels required, positions 0-1000, orders 1-8"
- Database error: "Error saving gallery. Please try again"
- Duplicate orders: "Each photo must have a unique order number"

**Success Messages:**
- "Background updated successfully"
- "Gallery photos updated successfully"

---

## Testing Checklist

- [ ] Admin can upload background image
- [ ] Background image displays as thumbnail
- [ ] Admin can remove background
- [ ] Admin can edit photo labels (inline)
- [ ] Admin can change X/Y positions (number inputs)
- [ ] Admin can reorder photos (drag or order input)
- [ ] Form validation works (required fields, valid numbers)
- [ ] Save button updates database correctly
- [ ] All 8 photos display in table
- [ ] Changes persist after page reload
- [ ] Unauthorized access redirected to login
- [ ] Responsive layout on tablet/mobile

---

## Implementation Notes

- Reuse existing Bootstrap admin styling for consistency
- Use existing session auth system (no new auth needed)
- File upload folder: create if doesn't exist
- Transaction on gallery update (all-or-nothing)
- No image cropping/resizing in this phase (future enhancement)
- Preview modal shows scatter layout preview (static/non-interactive)

---

## Success Criteria

- ✓ Background image management fully functional
- ✓ All 8 photos editable (labels, positions, order)
- ✓ Changes persist to database
- ✓ Admin UI matches existing Bootstrap theme
- ✓ Form validation works
- ✓ Error messages clear and helpful
- ✓ Responsive on all screen sizes

