from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    
    page.on("console", lambda msg: print(f"Browser console: {msg.text}"))
    page.on("pageerror", lambda err: print(f"Browser error: {err}"))
    
    response = page.goto("http://localhost:8743/index.php", wait_until="networkidle")
    print(f"Status: {response.status}")
    
    page.screenshot(path="screenshot.png")
    print("Screenshot saved to screenshot.png")
    
    browser.close()
