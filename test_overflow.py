from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.goto("http://localhost:8743/index.php", wait_until="networkidle")
    
    # Get computed styles of body
    body_style = page.evaluate("window.getComputedStyle(document.body).overflow")
    html_style = page.evaluate("window.getComputedStyle(document.documentElement).overflow")
    
    # Also check if projects section is visible
    projects_visible = page.evaluate("document.getElementById('projects').getBoundingClientRect().top")
    
    print(f"Body overflow: {body_style}")
    print(f"HTML overflow: {html_style}")
    print(f"Projects Top: {projects_visible}")
    
    browser.close()
