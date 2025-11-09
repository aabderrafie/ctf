const puppeteer = require('puppeteer');

async function run() {
  const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox'] });
  const page = await browser.newPage();
  console.log('Using browser:', await browser.version());

  await page.goto('http://imageboard:3000/login', { waitUntil: 'domcontentloaded' });
  await page.type('input[name="username"]', 'jantofix');
  await page.type('input[name="password"]', 'QegFp8CHesu4');
  await page.click('button[type="submit"]');

  await page.waitForSelector('button[type="submit"]', { timeout: 10000 });

  await page.goto('http://imageboard:3000/admin', { waitUntil: 'domcontentloaded' });

  await new Promise(r => setTimeout(r, 3000));
  await page.screenshot({ path: 'admin.png' });

  await browser.close();
}

setInterval(run, 10000);

