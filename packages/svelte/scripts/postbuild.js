// Ships the contract's drop-in stylesheet beside the components, so an app
// imports one file: import '@sigta-hq/svelte/sigta.css'.
import { writeFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const pkg = join(dirname(fileURLToPath(import.meta.url)), '..');
// An @import keeps the font url()s resolving against the contract package.
writeFileSync(join(pkg, 'dist/sigta.css'), '@import "@sigta-hq/contract/sigta.css";\n');
