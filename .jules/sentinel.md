## 2024-08-20 - RCE via Arbitrary File Upload
**Vulnerability:** File upload scripts in `admin/*.php` (like `admin/gallery.php`) used `move_uploaded_file` with only `basename()` sanitization and no file extension or MIME type checks, allowing an attacker with admin access to upload arbitrary `.php` scripts and gain Remote Code Execution (RCE).
**Learning:** Even internal/admin-facing file upload forms are critical attack vectors if they allow arbitrary file types. The application relies on saving files directly into the webroot (`images/` directory).
**Prevention:** Always validate both the file extension against an allowlist and the MIME type (using `mime_content_type`) before calling `move_uploaded_file`. Additionally, sanitize the file name to prevent path traversal issues.
