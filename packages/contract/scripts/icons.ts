/* Regenerates icons/lucide.json — the pinned icon set both runtimes render —
   from the Lucide SVGs the Blade runtime serves (mallardduck/blade-lucide-icons
   in vendor/), so <sigta:icon> and <Icon> can never draw different versions.
   Run after bumping that Composer package: node packages/contract/scripts/icons.ts */
import { readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '../../..');
const source = join(root, 'vendor/mallardduck/blade-lucide-icons/resources/svg/icons');
const lock = JSON.parse(readFileSync(join(root, 'composer.lock'), 'utf8'));
const version = [...lock.packages, ...(lock['packages-dev'] ?? [])].find((p) => p.name === 'mallardduck/blade-lucide-icons')?.version;

const icons: Record<string, string> = {};
for (const file of readdirSync(source).filter((f) => f.endsWith('.svg')).sort()) {
    const svg = readFileSync(join(source, file), 'utf8');
    const inner = svg.replace(/^[\s\S]*?<svg[^>]*>/, '').replace(/<\/svg>\s*$/, '');
    icons[file.replace(/\.svg$/, '')] = inner.replace(/\n\s*/g, '').trim();
}
writeFileSync(join(root, 'packages/contract/icons/lucide.json'), JSON.stringify({ source: `mallardduck/blade-lucide-icons@${version}`, icons }) + '\n');
console.log(`${Object.keys(icons).length} icons from blade-lucide-icons ${version}`);
