## 2024-05-18 - [Block Direct SQLite Access]
**Vulnerability:** The SQLite database `design_plus.sqlite` was accessible via the web root directory in Apache servers.
**Learning:** SQLite databases should never be placed inside the web root, or they must be explicitly protected with server configuration if placed there.
**Prevention:** Consider moving SQLite database files outside the web root (`public_html`/`htdocs`) or always include a `.htaccess` file denying access to `.sqlite` files.
