// Lockstep versions: one version for the npm packages (and their references
// to each other), matching the git tag the Composer package is released by.
//   node scripts/version.js 1.2.3          set it
//   node scripts/version.js --check 1.2.3  fail unless every package has it
import { readFileSync, writeFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const packages = ['packages/contract', 'packages/core', 'packages/svelte'];
const check = process.argv.includes('--check');
const version = process.argv.slice(2).find((arg) => !arg.startsWith('--'));
if (!/^\d+\.\d+\.\d+(-[\w.]+)?$/.test(version ?? '')) throw new Error('usage: version.js [--check] <semver>');

let wrong = [];
for (const dir of packages) {
    const file = join(root, dir, 'package.json');
    const pkg = JSON.parse(readFileSync(file, 'utf8'));
    const deps = Object.keys(pkg.dependencies ?? {}).filter((name) => name.startsWith('@sigta-hq/'));
    if (check) {
        if (pkg.version !== version) wrong.push(`${pkg.name}@${pkg.version}`);
        deps.filter((name) => pkg.dependencies[name] !== version).forEach((name) => wrong.push(`${pkg.name} → ${name}@${pkg.dependencies[name]}`));
        continue;
    }
    pkg.version = version;
    deps.forEach((name) => { pkg.dependencies[name] = version; });
    writeFileSync(file, JSON.stringify(pkg, null, 4) + '\n');
}
if (check && wrong.length) {
    console.error(`Not at ${version}: ${wrong.join(', ')}`);
    process.exit(1);
}
console.log(check ? `versions: all at ${version}` : `versions: set to ${version}`);
