const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const screenshotsDir = path.join(__dirname, '..', 'screenshots');

if (!fs.existsSync(screenshotsDir)) {
  fs.mkdirSync(screenshotsDir, { recursive: true });
}

const results = [];

async function capture(page, filename, label) {
  const filepath = path.join(screenshotsDir, filename);
  try {
    await page.screenshot({ path: filepath, fullPage: true });
    const stats = fs.statSync(filepath);
    const kb = (stats.size / 1024).toFixed(1);
    results.push({ file: filename, size: kb, status: 'ok' });
    console.log(`  saved ${filename} (${kb} KB)`);
  } catch (err) {
    results.push({ file: filename, size: 0, status: `FAILED: ${err.message}` });
    console.error(`  FAILED ${filename}: ${err.message}`);
  }
}

async function navigate(page, url, label) {
  console.log(`\nNavigating to ${url}`);
  try {
    const response = await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
    const status = response ? response.status() : 'unknown';
    if (status === 404) {
      console.warn(`  WARNING: ${url} returned 404`);
    }
    await page.waitForTimeout(2500);
    return status;
  } catch (err) {
    console.error(`  Navigation error for ${url}: ${err.message}`);
    return 'error';
  }
}

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 }
  });
  const page = await context.newPage();

  // 1. Homepage full page
  await navigate(page, 'http://techstack.local', 'Homepage');
  await capture(page, 'homepage.png', 'Homepage');

  // 2. Homepage scrolled to Featured Events
  await navigate(page, 'http://techstack.local', 'Homepage - Featured Events');
  await page.evaluate(() => {
    const el = document.querySelector('.ts-featured-section, [class*="featured"]');
    if (el) el.scrollIntoView();
  });
  await page.waitForTimeout(500);
  await capture(page, 'homepage-featured.png', 'Featured Events');

  // 3. Homepage scrolled to Browse by Topic
  await navigate(page, 'http://techstack.local', 'Homepage - Browse by Topic');
  await page.evaluate(() => {
    const el = document.querySelector('.ts-topics-section, [class*="topics"], [class*="topic"]');
    if (el) el.scrollIntoView();
  });
  await page.waitForTimeout(500);
  await capture(page, 'homepage-topics.png', 'Browse by Topic');

  // 4. Events archive
  await navigate(page, 'http://techstack.local/events/', 'Events archive');
  await capture(page, 'events.png', 'Events archive');

  // 5. Hackathons
  await navigate(page, 'http://techstack.local/events/?type=Hackathon', 'Hackathons');
  await capture(page, 'hackathons.png', 'Hackathons');

  // 6. CFP open
  await navigate(page, 'http://techstack.local/events/?cfp=open', 'CFP Open');
  await capture(page, 'cfp-open.png', 'CFP Open');

  // 7. Calendar
  await navigate(page, 'http://techstack.local/calendar/', 'Calendar');
  await capture(page, 'calendar.png', 'Calendar');

  // 8. Submit form
  await navigate(page, 'http://techstack.local/submit-event/', 'Submit Event');
  await capture(page, 'submit.png', 'Submit Event');

  // Navigation flow: event card click
  console.log('\n--- Navigation flow verification ---');
  await navigate(page, 'http://techstack.local/events/', 'Nav flow - archive');
  await capture(page, 'nav-flow-1-archive.png', 'Archive page');

  let singleEventUrl = '';
  try {
    const cardLink = page.locator('.ts-event-card a, a.ts-card-link').first();
    const count = await cardLink.count();
    if (count > 0) {
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle', timeout: 15000 }),
        cardLink.click()
      ]);
      singleEventUrl = page.url();
      console.log(`  Landed on: ${singleEventUrl}`);
      await page.waitForTimeout(2500);
      await capture(page, 'nav-flow-2-single-event.png', 'Single event page');
    } else {
      console.warn('  No event card link found');
      results.push({ file: 'nav-flow-2-single-event.png', size: 0, status: 'SKIPPED: no card link found' });
    }
  } catch (err) {
    console.error(`  Card click flow error: ${err.message}`);
    results.push({ file: 'nav-flow-2-single-event.png', size: 0, status: `FAILED: ${err.message}` });
  }

  // See Site button verification
  await navigate(page, 'http://techstack.local/events/', 'Nav flow - see site');
  try {
    const seeBtn = page.locator('a:has-text("See site"), a.ts-btn-secondary').first();
    const count = await seeBtn.count();
    if (count > 0) {
      const href = await seeBtn.getAttribute('href');
      console.log(`  See site href: ${href}`);
      await capture(page, 'nav-flow-3-see-site-button.png', 'See site button visible');

      if (href) {
        try {
          await page.goto(href, { waitUntil: 'networkidle', timeout: 15000 });
          await page.waitForTimeout(2500);
          await capture(page, 'nav-flow-4-external-destination.png', 'External destination');
        } catch (err) {
          console.warn(`  External navigation blocked or failed: ${err.message}`);
          await capture(page, 'nav-flow-4-external-destination.png', 'External (blocked/partial)');
        }
      }
    } else {
      console.warn('  No See site button found');
      results.push({ file: 'nav-flow-3-see-site-button.png', size: 0, status: 'SKIPPED: no see-site button found' });
      results.push({ file: 'nav-flow-4-external-destination.png', size: 0, status: 'SKIPPED: no see-site button found' });
    }
  } catch (err) {
    console.error(`  See site flow error: ${err.message}`);
  }

  await browser.close();

  // Summary
  console.log('\n========== SCREENSHOT SUMMARY ==========');
  console.log(`${'File'.padEnd(45)} ${'Size (KB)'.padEnd(12)} Status`);
  console.log('-'.repeat(75));
  for (const r of results) {
    console.log(`${r.file.padEnd(45)} ${String(r.size).padEnd(12)} ${r.status}`);
  }
  console.log('========================================');
})();
