## 2026-08-10 - [Information Disclosure via SQLite Database]
**Vulnerability:** The SQLite database `design_plus.sqlite` was located in the public web root directory, making it directly accessible to anyone via HTTP.
**Learning:** SQLite databases are flat files and must never be placed in a directory served by a web server, or they can be downloaded by an attacker, leading to a complete compromise of all stored data (including admin hashes).
**Prevention:** Always place `.sqlite` or `.db` files in a directory outside the web root, or in a protected directory that explicitly denies all web access using `.htaccess` or server configuration rules.
