// Ships the contract's drop-in stylesheet beside the components, so an app
// imports one file: import '@sigta-hq/svelte/sigta.css'.
import { copyFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const pkg = join(dirname(fileURLToPath(import.meta.url)), '..');
copyFileSync(join(pkg, '../contract/dist/css/sigta.css'), join(pkg, 'dist/sigta.css'));
