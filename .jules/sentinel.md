## 2025-02-12 - [CRITICAL] Unrestricted File Upload in Admin Panel
**Vulnerability:** The admin panel scripts (`hero.php`, `awards.php`, `gallery.php`, `team.php`, `testimonials.php`) allow uploading files without validating the file extension, enabling Unrestricted File Upload vulnerabilities that could lead to Remote Code Execution (RCE).
**Learning:** The previous implementation relied purely on client-side or omitted validation entirely, leaving the PHP server exposed to executing malicious scripts disguised as image uploads.
**Prevention:** Always implement strict server-side file extension validation using a whitelisted array of allowed extensions, combined with robust file path security mechanisms.
