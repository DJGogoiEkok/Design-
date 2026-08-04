## 2024-03-24 - Unrestricted File Upload in Admin Panel
**Vulnerability:** Admin panel endpoints for file uploads (`awards.php`, `gallery.php`, `hero.php`, `team.php`, `testimonials.php`) failed to validate file extensions before writing to disk, permitting arbitrary file upload and RCE.
**Learning:** Checking only `$_FILES["image"]["error"] === UPLOAD_ERR_OK` is insufficient. The lack of an extension whitelist combined with `move_uploaded_file` led to critical exposure.
**Prevention:** Always implement an explicit `$allowed_exts` whitelist check against the file extension derived via `pathinfo` prior to executing `move_uploaded_file`.
