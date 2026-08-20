# Gallery Admin Panel Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add gallery management section to admin panel allowing admins to manage background image, edit photo labels, reorder photos, and adjust desktop scatter positions.

**Architecture:** Create `gallery_settings` database table for background image storage. Extend existing admin/team.php with two new form sections: background upload manager and photo management table. Reuse Bootstrap UI framework and existing session auth.

**Tech Stack:** PHP, SQLite, Bootstrap 5.3, HTML forms

## Global Constraints

- **Target file:** `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`
- **Database:** Create `gallery_settings` table; extend `gallery_photos` table usage
- **Framework:** Bootstrap 5.3 (matches existing admin)
- **Auth:** Require admin session login (existing system)
- **Photos:** Manage exactly 8 photos (no add/remove, edit only)
- **Background:** Single background image for gallery section
- **File upload folder:** `images/gallery/backgrounds/`
- **File upload limit:** 5MB, images only (JPG, PNG, WebP)
- **Position ranges:** X 0-1000, Y 0-800
- **Order:** 1-8, no duplicates

---

## File Structure

**Modified:**
- `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php` — Add gallery management section

**Database:**
- Create `gallery_settings` table
- Extend `gallery_photos` table (add background functionality)

---

## Task Breakdown

### Task 1: Create Database Schema for Gallery Settings

**Files:**
- Database: Create `gallery_settings` table
- Create: `setup-gallery-admin.php` (setup script)

**Interfaces:**
- Produces: `gallery_settings` table queryable via `SELECT * FROM gallery_settings WHERE id = 1`

**Steps:**

- [ ] **Step 1: Create setup script**

Create file `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/setup-gallery-admin.php`:
```php
<?php
require_once 'db.php';

try {
    // Create gallery_settings table
    $db->exec("
        CREATE TABLE IF NOT EXISTS gallery_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            background_image_path TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✓ Table created: gallery_settings\n";

    // Insert initial row if not exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM gallery_settings");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        $db->exec("INSERT INTO gallery_settings (background_image_path) VALUES (NULL)");
        echo "✓ Inserted initial row\n";
    }

    // Verify
    $stmt = $db->query("SELECT * FROM gallery_settings WHERE id = 1");
    $settings = $stmt->fetch();
    echo "✓ Gallery settings ready\n";
    echo "Current background: " . ($settings['background_image_path'] ?? 'None') . "\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
```

- [ ] **Step 2: Run setup script**

Run command:
```bash
php /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/setup-gallery-admin.php
```

Expected output:
```
✓ Table created: gallery_settings
✓ Inserted initial row
✓ Gallery settings ready
Current background: None
```

---

### Task 2: Add Background Upload Handler to Admin

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`

**Interfaces:**
- Consumes: `$_POST['action']`, `$_FILES['background_image']`
- Produces: Background image saved to `images/gallery/backgrounds/`, path stored in `gallery_settings`

**Steps:**

- [ ] **Step 1: Add background upload handling after existing POST handlers**

Find the POST handler section in admin/team.php (around line 10). After the existing delete/edit handlers, add this code before line 69 (before `$stmt = $db->query("SELECT * FROM team_members"`):

```php
    } elseif (isset($_POST['action']) && $_POST['action'] === 'upload_background') {
        // Handle background image upload
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../images/gallery/backgrounds/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Validate file type
            $file_type = mime_content_type($_FILES['background_image']['tmp_name']);
            if (!in_array($file_type, ['image/jpeg', 'image/png', 'image/webp'])) {
                $_SESSION['error'] = 'Only JPG, PNG, and WebP images are allowed';
            } else if ($_FILES['background_image']['size'] > 5242880) { // 5MB
                $_SESSION['error'] = 'File must be less than 5MB';
            } else {
                // Generate safe filename
                $ext = pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION);
                $file_name = 'background_' . time() . '.' . $ext;
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['background_image']['tmp_name'], $target_file)) {
                    $image_path = 'images/gallery/backgrounds/' . $file_name;
                    $stmt = $db->prepare("UPDATE gallery_settings SET background_image_path = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
                    $stmt->execute([$image_path]);
                    $_SESSION['success'] = 'Background image updated successfully';
                } else {
                    $_SESSION['error'] = 'Error uploading file';
                }
            }
        } else {
            $_SESSION['error'] = 'No file selected or upload error';
        }
        header('Location: team.php');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'remove_background') {
        // Remove background image
        $stmt = $db->prepare("UPDATE gallery_settings SET background_image_path = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
        $stmt->execute();
        $_SESSION['success'] = 'Background image removed';
        header('Location: team.php');
        exit;
```

- [ ] **Step 2: Verify syntax**

Run:
```bash
php -l /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php
```

Expected: `No syntax errors detected`

---

### Task 3: Add Gallery Photos Update Handler

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`

**Interfaces:**
- Consumes: `$_POST['action']`, arrays of `photo_ids[]`, `photo_labels[]`, `position_x[]`, `position_y[]`, `gallery_order[]`
- Produces: Updated `gallery_photos` table rows

**Steps:**

- [ ] **Step 1: Add photo update handler**

Add this code after the background handlers (before `$stmt = $db->query("SELECT * FROM team_members"`):

```php
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_gallery') {
        // Handle gallery photo updates
        $photo_ids = $_POST['photo_ids'] ?? [];
        $labels = $_POST['labels'] ?? [];
        $positions_x = $_POST['positions_x'] ?? [];
        $positions_y = $_POST['positions_y'] ?? [];
        $orders = $_POST['orders'] ?? [];
        
        $error = false;
        
        // Validate inputs
        foreach ($photo_ids as $i => $id) {
            $label = trim($labels[$i] ?? '');
            $x = intval($positions_x[$i] ?? 0);
            $y = intval($positions_y[$i] ?? 0);
            $order = intval($orders[$i] ?? 0);
            
            if (empty($label) || strlen($label) > 50) {
                $_SESSION['error'] = 'Labels are required and must be under 50 characters';
                $error = true;
                break;
            }
            
            if ($x < 0 || $x > 1000 || $y < 0 || $y > 800) {
                $_SESSION['error'] = 'Positions must be X: 0-1000, Y: 0-800';
                $error = true;
                break;
            }
            
            if ($order < 1 || $order > 8) {
                $_SESSION['error'] = 'Order must be between 1 and 8';
                $error = true;
                break;
            }
        }
        
        // Check for duplicate orders
        if (!$error) {
            $orders_array = array_map('intval', $orders);
            if (count($orders_array) !== count(array_unique($orders_array))) {
                $_SESSION['error'] = 'Each photo must have a unique order number';
                $error = true;
            }
        }
        
        // Update database if no errors
        if (!$error) {
            try {
                foreach ($photo_ids as $i => $id) {
                    $stmt = $db->prepare("
                        UPDATE gallery_photos 
                        SET photo_label = ?, position_x = ?, position_y = ?, gallery_order = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        trim($labels[$i] ?? ''),
                        intval($positions_x[$i] ?? 0),
                        intval($positions_y[$i] ?? 0),
                        intval($orders[$i] ?? 0),
                        $id
                    ]);
                }
                $_SESSION['success'] = 'Gallery photos updated successfully';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error updating photos: ' . $e->getMessage();
            }
        }
        
        header('Location: team.php');
        exit;
```

- [ ] **Step 2: Verify syntax**

Run:
```bash
php -l /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php
```

Expected: `No syntax errors detected`

---

### Task 4: Fetch Gallery Data in Admin

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`

**Interfaces:**
- Produces: `$gallery_settings`, `$gallery_photos` variables available in template

**Steps:**

- [ ] **Step 1: Add data queries**

Find line 71 where `$stmt = $db->query("SELECT * FROM team_members WHERE type = 'team'")` exists. After the partners query (around line 76), add:

```php
// Fetch gallery settings
$stmt = $db->query("SELECT * FROM gallery_settings WHERE id = 1");
$gallery_settings = $stmt->fetch();

// Fetch gallery photos
$stmt = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$gallery_photos = $stmt->fetchAll();
```

- [ ] **Step 2: Verify queries work**

Create test script `/Volumes/DJ_CRUCIAL/DEsing+/DP/redesign/test-gallery-admin-data.php`:
```php
<?php
require_once 'db.php';

$stmt = $db->query("SELECT * FROM gallery_settings WHERE id = 1");
$settings = $stmt->fetch();
echo "Gallery settings: ";
var_dump($settings);

$stmt = $db->query("SELECT * FROM gallery_photos ORDER BY gallery_order ASC");
$photos = $stmt->fetchAll();
echo "\nGallery photos count: " . count($photos) . "\n";
foreach ($photos as $p) {
    echo "  [" . $p['gallery_order'] . "] " . $p['photo_label'] . " (x:" . $p['position_x'] . ", y:" . $p['position_y'] . ")\n";
}
?>
```

Run:
```bash
php /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/test-gallery-admin-data.php
```

Expected: Shows gallery settings and all 8 photos with their positions

---

### Task 5: Add Background Management HTML Section

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`

**Interfaces:**
- Consumes: `$gallery_settings['background_image_path']`
- Produces: HTML form for background upload/removal

**Steps:**

- [ ] **Step 1: Add background section before closing container**

Find the closing `</div><!-- container -->` tag near the end of the HTML body (before closing body tag). Add this section before it:

```html
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Gallery Background</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div id="background-preview" class="mb-3">
                            <?php if ($gallery_settings && !empty($gallery_settings['background_image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($gallery_settings['background_image_path']); ?>" 
                                     alt="Gallery Background" style="max-width: 300px; max-height: 200px; border-radius: 8px;">
                                <p class="mt-2 text-muted">Current background</p>
                            <?php else: ?>
                                <p class="text-muted">No background image set</p>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload_background">
                            <div class="mb-3">
                                <label for="background_image" class="form-label">Upload New Background</label>
                                <input type="file" class="form-control" id="background_image" name="background_image" 
                                       accept="image/jpeg,image/png,image/webp" required>
                                <small class="form-text text-muted">JPG, PNG, or WebP. Max 5MB.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload Background</button>
                            
                            <?php if ($gallery_settings && !empty($gallery_settings['background_image_path'])): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="removeBackground()">Remove Background</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
```

- [ ] **Step 2: Add remove background form (hidden)**

Add this HTML at the end of the body (before closing body tag):

```html
        <form id="remove-background-form" method="POST" style="display: none;">
            <input type="hidden" name="action" value="remove_background">
        </form>
        
        <script>
        function removeBackground() {
            if (confirm('Are you sure you want to remove the background image?')) {
                document.getElementById('remove-background-form').submit();
            }
        }
        </script>
```

- [ ] **Step 3: Verify HTML renders**

Run:
```bash
php -l /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php
```

Expected: `No syntax errors detected`

---

### Task 6: Add Gallery Photos Management HTML Section

**Files:**
- Modify: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php`

**Interfaces:**
- Consumes: `$gallery_photos` array
- Produces: HTML table form for editing photos

**Steps:**

- [ ] **Step 1: Add photos table before background section**

Add this section before the background section (before `<div class="row mt-5">`):

```html
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Gallery Photos (8 Total)</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_gallery">
                            
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">Thumbnail</th>
                                            <th>Label</th>
                                            <th style="width: 100px;">Position X</th>
                                            <th style="width: 100px;">Position Y</th>
                                            <th style="width: 60px;">Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($gallery_photos as $photo): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($photo['photo_image']); ?>" 
                                                     alt="Photo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                <input type="hidden" name="photo_ids[]" value="<?php echo $photo['id']; ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" 
                                                       name="labels[]" value="<?php echo htmlspecialchars($photo['photo_label']); ?>" 
                                                       maxlength="50" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="positions_x[]" value="<?php echo $photo['position_x']; ?>" 
                                                       min="0" max="1000" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="positions_y[]" value="<?php echo $photo['position_y']; ?>" 
                                                       min="0" max="800" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="orders[]" value="<?php echo $photo['gallery_order']; ?>" 
                                                       min="1" max="8" required>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Save Gallery Photos</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
```

- [ ] **Step 2: Verify table renders correctly**

Run:
```bash
php -l /Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/admin/team.php
```

Expected: `No syntax errors detected`

---

### Task 7: Test Gallery Admin Panel

**Files:**
- Test: No new files; verify admin/team.php functionality

**Steps:**

- [ ] **Step 1: Test background upload**

Visit admin page in browser. In "Gallery Background" section:
- Upload a test image (JPG, PNG, or WebP)
- Verify success message appears
- Verify background thumbnail displays
- Reload page to confirm persistence

Expected: Image saved to `images/gallery/backgrounds/`, path in database

- [ ] **Step 2: Test photo label editing**

In "Gallery Photos" table:
- Edit label for first photo (e.g., "Creative" → "Awesome")
- Click "Save Gallery Photos"
- Verify success message
- Reload page to confirm change persisted

Expected: Label updated in database

- [ ] **Step 3: Test position adjustment**

In "Gallery Photos" table:
- Change Position X for first photo (e.g., 50 → 100)
- Change Position Y for first photo (e.g., 80 → 120)
- Click "Save Gallery Photos"
- Verify success message

Expected: Positions updated in database

- [ ] **Step 4: Test order resequencing**

In "Gallery Photos" table:
- Change Order for first photo from 1 to 2
- Change Order for second photo from 2 to 1
- Click "Save Gallery Photos"
- Reload page
- Verify photos appear in new order

Expected: Gallery order updated in database

- [ ] **Step 5: Test validation**

Try invalid inputs:
- Label > 50 characters: Should error "must be under 50 characters"
- Position X > 1000: Should error "0-1000"
- Position Y > 800: Should error "0-800"
- Order not 1-8: Should error "between 1 and 8"
- Duplicate orders: Should error "unique order number"

Expected: Validation errors displayed, no database update

- [ ] **Step 6: Test background removal**

If background exists, click "Remove Background". Verify:
- Confirmation dialog appears
- On confirm, success message shows
- Background preview disappears
- Database cleared (path = NULL)

---

### Task 8: Verify Gallery Frontend Shows Background

**Files:**
- Reference: `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php`
- Test: No changes needed yet; just verify current implementation

**Steps:**

- [ ] **Step 1: Check if gallery fetches background from database**

Open `/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/team.php` and verify it queries `gallery_settings` for background image.

Expected: Query exists to fetch background path

- [ ] **Step 2: Verify gallery displays background**

Visit team page in browser. Check:
- Background image displays in gallery section
- After admin updates background, gallery reflects change
- No broken image icons

Expected: Gallery renders with updated background

---

## Spec Coverage Check

✓ Background image upload/management (Tasks 2, 5)
✓ Gallery photo label editing (Tasks 4, 6)
✓ Photo reordering (Tasks 4, 6)
✓ Desktop scatter position adjustment (Tasks 4, 6)
✓ Database schema (Task 1)
✓ Admin interface Bootstrap styling (Tasks 5, 6)
✓ Form validation (Task 3)
✓ File upload handling (Task 2)
✓ Error handling (Tasks 2, 3, 5, 6)

---

## Execution

Plan complete and saved to `docs/superpowers/plans/2026-07-27-gallery-admin-panel.md`.

**Two execution options:**

**1. Subagent-Driven (recommended)** - Fresh subagent per task, review between tasks

**2. Inline Execution** - Execute tasks in this session

**Which approach?**
