from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.on("console", lambda msg: print(msg.text))
    page.goto("http://localhost:8744/test_offset.html")
    browser.close()
