// Compares two visual-snapshot.js runs page by page and writes diff images for
// the pages that changed: node scripts/visual-compare.js <before> <after> <diff-dir>
import { readdirSync, readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { join } from 'node:path';
import { PNG } from 'pngjs';
import pixelmatch from 'pixelmatch';

const [before, after, diffs] = process.argv.slice(2);
mkdirSync(diffs, { recursive: true });
const rows = [];

for (const file of readdirSync(before).filter((f) => f.endsWith('.png')).sort()) {
    if (!existsSync(join(after, file))) { rows.push([file, 'missing']); continue; }
    const a = PNG.sync.read(readFileSync(join(before, file)));
    const b = PNG.sync.read(readFileSync(join(after, file)));
    if (a.width !== b.width || a.height !== b.height) {
        rows.push([file, `size ${a.width}x${a.height} → ${b.width}x${b.height}`]);
        continue;
    }
    const diff = new PNG({ width: a.width, height: a.height });
    const changed = pixelmatch(a.data, b.data, diff.data, a.width, a.height, { threshold: 0.1 });
    if (changed > 0) {
        writeFileSync(join(diffs, file), PNG.sync.write(diff));
        rows.push([file, `${changed} px`]);
    }
}
rows.forEach(([file, note]) => console.log(`${file}\t${note}`));
console.log(rows.length ? `${rows.length} changed` : 'identical');
process.exitCode = rows.length ? 1 : 0;
