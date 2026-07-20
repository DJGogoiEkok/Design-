from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.set_viewport_size({"width": 1280, "height": 800})
    page.goto("http://localhost:8743/index.php", wait_until="networkidle")
    
    # Scroll to projects section
    page.evaluate("document.getElementById('projects').scrollIntoView()")
    page.wait_for_timeout(2000) # wait for animations
    
    # Check opacity of .portfolio-split-view
    opacity = page.evaluate("window.getComputedStyle(document.querySelector('.portfolio-split-view')).opacity")
    print(f"Opacity: {opacity}")
    
    browser.close()
