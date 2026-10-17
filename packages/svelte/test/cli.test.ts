// `sigta install` writes the LLM context files and one AGENTS.md line, idempotently.
import { describe, expect, it } from 'vitest';
import { execFileSync } from 'node:child_process';
import { mkdtempSync, readFileSync, writeFileSync, existsSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const bin = join(__dirname, '../bin/sigta.js');
const run = (cwd: string) => execFileSync(process.execPath, [bin, 'install', `--cwd=${cwd}`]).toString();

describe('sigta install', () => {
    it('writes the stubs and an AGENTS.md line, and changes nothing on a second run', () => {
        const project = mkdtempSync(join(tmpdir(), 'sigta-install-'));
        run(project);
        expect(existsSync(join(project, 'sigta-ui.md'))).toBe(true);
        expect(readFileSync(join(project, 'sigta-components.md'), 'utf8')).toContain('@sigta-hq/svelte');
        const agents = readFileSync(join(project, 'AGENTS.md'), 'utf8');
        expect(agents).toContain('sigta-ui-context');
        expect(run(project)).toContain('kept  AGENTS.md');
        expect(readFileSync(join(project, 'AGENTS.md'), 'utf8')).toBe(agents);
    });

    it('appends to an existing AGENTS.md', () => {
        const project = mkdtempSync(join(tmpdir(), 'sigta-install-'));
        writeFileSync(join(project, 'AGENTS.md'), '# Agents\n\n- Be kind.\n');
        run(project);
        const agents = readFileSync(join(project, 'AGENTS.md'), 'utf8');
        expect(agents).toContain('- Be kind.');
        expect(agents.match(/sigta-ui-context/g)).toHaveLength(1);
    });
});
