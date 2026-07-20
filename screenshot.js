const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });
  await page.goto('http://localhost:8743', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: 'site_screenshot.png', fullPage: true });
  await browser.close();
})();
