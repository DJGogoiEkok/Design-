# Sentinel Journal

## 2024-05-24 - Unrestricted File Upload in Admin Panel
**Vulnerability:** Found multiple instances where the admin panel processed file uploads without checking the file extension, allowing potential Remote Code Execution (RCE) via malicious PHP file uploads.
**Learning:** Even within an authenticated admin section, file upload endpoints must enforce strict server-side validation to prevent shell uploads, as admin accounts can be compromised or the vulnerability chained with others.
**Prevention:** Always use a strict whitelist of allowed file extensions (e.g., using `in_array(strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)), ['jpg', 'png', ...])`) for all user-supplied file uploads.
