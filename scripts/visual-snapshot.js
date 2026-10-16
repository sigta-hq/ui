// Screenshots every playground kit page, for comparing the look before and
// after a change: node scripts/visual-snapshot.js <out-dir> [base-url] [page…]
import { chromium } from '@playwright/test';
import { mkdirSync, readdirSync, existsSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const [out, base = 'http://127.0.0.1:8321', ...only] = process.argv.slice(2);
if (!out) throw new Error('usage: visual-snapshot.js <out-dir> [base-url] [page…]');
mkdirSync(out, { recursive: true });

const pages = only.length ? only : readdirSync(join(root, 'playground/resources/views/kit'))
    .filter((file) => file.endsWith('.blade.php'))
    .map((file) => file.replace('.blade.php', ''));

const executablePath = ['/opt/pw-browsers/chromium-1194/chrome-linux/chrome'].find(existsSync);
const browser = await chromium.launch(executablePath ? { executablePath } : {});
const widths = [1280, 390];

for (const width of widths) {
    const context = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce', deviceScaleFactor: 1 });
    const page = await context.newPage();
    await page.addInitScript(() => { Math.random = () => 0.5; });
    for (const name of pages) {
        await page.goto(`${base}/kit/${name}`, { waitUntil: 'networkidle' });
        await page.evaluate(() => document.fonts.ready);
        await page.addStyleTag({ content: '*{caret-color:transparent!important}' });
        await page.waitForTimeout(150);
        await page.screenshot({ path: join(out, `${name}@${width}.png`), fullPage: true });
    }
    await context.close();
}
await browser.close();
console.log(`${pages.length * widths.length} screenshots → ${out}`);
