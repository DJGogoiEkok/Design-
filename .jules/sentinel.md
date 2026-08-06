## 2024-05-18 - [Fix Arbitrary File Upload Vulnerability]
**Vulnerability:** Found arbitrary file upload vulnerability in the admin panel scripts (admin/hero.php, admin/awards.php, admin/gallery.php, admin/team.php, and admin/testimonials.php). Uploaded files lacked extension validation, posing a critical RCE risk.
**Learning:** Always validate uploaded files against a whitelist of allowed extensions on the server side to prevent malicious scripts from being uploaded.
**Prevention:** Use a whitelist array and check the extension of the uploaded file against it before calling move_uploaded_file().
