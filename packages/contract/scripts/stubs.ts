/* The LLM context files, both editions: the rules (sigta-ui.md) and the
   component reference (sigta-components.md), rendered from the templates in
   packages/contract/stubs/ and the manifests. */
import { readFileSync } from 'node:fs';
import { join } from 'node:path';
import { execFileSync } from 'node:child_process';
import type { ComponentManifest, PropSpec } from '../src/schema.ts';
import { toBlade, toSvelte, svelteImports, type Registry } from '../src/examples.ts';

export type Runtime = 'livewire' | 'svelte';

const TIERS: Record<string, string> = {
    elements: 'Elements (indivisible blocks)',
    components: 'Components (functional units with internal parts)',
    layout: 'Layout (structural scaffolds)',
    sections: 'Sections (content-shaped page regions)',
};

const kebab = (name: string) => name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
const pascal = (name: string) => name.split('.').map((part) => part.replace(/(^|-)(.)/g, (_, __, ch: string) => ch.toUpperCase())).join('.');

/** Keeps the lines of one runtime's blocks and expands {{tag:name}}. */
export function edition(template: string, runtime: Runtime, registry: Registry): string {
    const out: string[] = [];
    let block: Runtime | null = null;
    for (const line of template.split('\n')) {
        const open = line.match(/^<!-- @(livewire|svelte) -->$/);
        if (open) { block = open[1] as Runtime; continue; }
        if (line === '<!-- @end -->') { block = null; continue; }
        if (block && block !== runtime) continue;
        out.push(line);
    }
    return out.join('\n')
        .replace(/^<!-- Source of[\s\S]*?-->\n/, '')
        .replace(/\{\{tag:([a-z0-9.-]+)\}\}/g, (_, name: string) =>
            runtime === 'livewire' ? `\`<sigta:${name}>\`` : `\`<${registry[name]?.svelte ?? pascal(name)}>\``);
}

/** The kit-page order of resources/catalog.php (tiers, then pages), when PHP is around. */
function catalogOrder(root: string): string[] {
    try {
        const json = execFileSync('php', ['-r', `echo json_encode(require '${join(root, 'resources/catalog.php')}');`], { stdio: ['ignore', 'pipe', 'ignore'] }).toString();
        const catalog = JSON.parse(json) as Record<string, { items: string[] }>;
        return Object.values(catalog).flatMap((group) => group.items);
    } catch {
        return [];
    }
}

function propLine(name: string, spec: PropSpec, runtime: Runtime): string | null {
    const override = runtime === 'livewire' ? spec.blade : spec.svelte;
    if (override === false) return null;
    const shown = runtime === 'livewire' ? (override?.name ?? kebab(name)) : (override?.name ?? name);
    const facts: string[] = [];
    if (spec.type === 'enum' && spec.values) facts.push(spec.values.map((v) => `\`${v}\``).join(' · '));
    else facts.push(spec.type);
    if (spec.required) facts.push('**required**');
    else if (spec.default !== undefined && spec.default !== null) facts.push(`default \`${typeof spec.default === 'string' ? spec.default : JSON.stringify(spec.default)}\``);
    if (spec.bindable) facts.push(runtime === 'svelte' ? `bindable (\`bind:${shown}\`)` : 'two-way (`wire:model`)');
    const note = (override && 'note' in override && override.note) ? ` ${override.note}` : '';
    return `  - \`${shown}\` (${facts.join(', ')}) — ${spec.description}${note}`;
}

function usage(manifest: ComponentManifest, runtime: Runtime, registry: Registry): string | null {
    const written = manifest.usage?.[runtime];
    if (written) return written;
    const example = manifest.examples.find((e) => e.tree) ?? null;
    if (example?.tree) {
        if (runtime === 'livewire') return toBlade(example.tree, registry, { prefix: 'sigta:' });
        const imports = svelteImports(example.tree, registry);
        return `${imports ? `<script>\n    ${imports}\n</script>\n\n` : ''}${toSvelte(example.tree, registry)}`;
    }
    const snippet = manifest.examples.find((e) => (runtime === 'livewire' ? e.blade : e.svelte));
    if (!snippet) return null;
    return runtime === 'livewire' ? snippet.blade!.replace(/<x-sigta::/g, '<sigta:').replace(/<\/x-sigta::/g, '</sigta:') : snippet.svelte!;
}

function entry(manifest: ComponentManifest, runtime: Runtime, registry: Registry, depth: string): string {
    const name = runtime === 'livewire' ? manifest.name : manifest.svelte;
    const lines = [`${depth} \`${name}\``, '', manifest.description, ''];
    const props = Object.entries(manifest.props).map(([key, spec]) => propLine(key, spec, runtime)).filter(Boolean);
    lines.push(props.length ? `- **Props:**\n${props.join('\n')}` : '- **Props:** none');
    const slots = Object.entries(manifest.slots ?? {});
    if (slots.length) {
        lines.push(`- **${runtime === 'livewire' ? 'Slots' : 'Snippets'}:** ${slots.map(([slot, spec]) => {
            const shown = runtime === 'svelte' && slot === 'default' ? 'children' : slot;
            const params = runtime === 'svelte' && spec.params ? ` ${spec.params}` : '';
            return `\`${shown}\`${params} — ${spec.description}`;
        }).join('; ')}`);
    }
    const events = Object.entries(manifest.events ?? {})
        .map(([, spec]) => ({ spec, how: runtime === 'livewire' ? spec.blade : spec.svelte }))
        .filter(({ how }) => how);
    if (events.length) {
        lines.push(`- **Events:** ${events.map(({ spec, how }) => `\`${how}\`${spec.detail ? ` (${spec.detail})` : ''} — ${spec.description}`).join('; ')}`);
    }
    for (const note of [...(manifest.notes?.shared ?? []), ...(manifest.notes?.[runtime] ?? [])]) lines.push(`- ${note}`);
    const code = usage(manifest, runtime, registry);
    if (code) lines.push(`\`\`\`${runtime === 'livewire' ? 'blade' : 'svelte'}`, code, '```');
    return lines.join('\n');
}

export function componentsSection(manifests: ComponentManifest[], runtime: Runtime, registry: Registry, root: string): string {
    const order = catalogOrder(root);
    const rank = (page: string) => { const i = order.indexOf(page); return i === -1 ? Number.MAX_SAFE_INTEGER : i; };
    const out: string[] = [];
    for (const [tier, title] of Object.entries(TIERS)) {
        const inTier = manifests.filter((m) => m.tier === tier);
        if (!inTier.length) continue;
        out.push(`## ${title}`, '');
        const pages = [...new Set(inTier.map((m) => m.page))].sort((a, b) => rank(a) - rank(b) || a.localeCompare(b));
        for (const page of pages) {
            const members = inTier.filter((m) => m.page === page).sort((a, b) => a.name.split('.').length - b.name.split('.').length || a.name.localeCompare(b.name));
            if (members.length > 1) {
                out.push(`### \`${page}\` family`, '');
                members.forEach((m) => out.push(entry(m, runtime, registry, '####'), ''));
            } else {
                out.push(entry(members[0], runtime, registry, '###'), '');
            }
        }
    }
    return out.join('\n').trimEnd();
}

function eventsSection(manifests: ComponentManifest[], runtime: Runtime): string {
    const rows = manifests.flatMap((m) => Object.values(m.events ?? {})
        .map((spec) => ({ m, spec, how: runtime === 'livewire' ? spec.blade : spec.svelte }))
        .filter(({ how, spec }) => how && !/^sigta-(toast|celebrate|save-state|modal-show|modal-hide)\b/.test(String(spec.blade ?? ''))));
    if (!rows.length) return '';
    return rows.map(({ m, spec, how }) => `| \`${how}\` | ${spec.detail ? `\`${spec.detail}\`` : '—'} | ${runtime === 'livewire' ? `\`${m.name}\`` : `\`<${m.svelte}>\``} | ${spec.description} |`).join('\n');
}

export function renderStubs(root: string, manifests: ComponentManifest[], registry: Registry, runtime: Runtime): { rules: string; components: string } {
    const header = (file: string) => `<!-- Generated by packages/contract/scripts/generate.ts from packages/contract/stubs/${file} and the manifests — do not edit. -->\n`;
    const rules = edition(readFileSync(join(root, 'packages/contract/stubs/rules.md'), 'utf8'), runtime, registry);
    const components = edition(readFileSync(join(root, 'packages/contract/stubs/components.md'), 'utf8'), runtime, registry)
        .replace('<!-- @components -->', componentsSection(manifests, runtime, registry, root))
        .replace('<!-- @events -->', eventsSection(manifests, runtime));
    return { rules: header('rules.md') + rules.trimEnd() + '\n', components: header('components.md') + components.trimEnd() + '\n' };
}
