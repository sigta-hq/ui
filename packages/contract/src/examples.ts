/* ---------------------------------------------------------------------------
   Examples as data.

   An example is authored once as a neutral tree and rendered into Blade and
   Svelte syntax. The same trees are the conformance fixtures: both runtimes
   render them and the outputs must agree.

     c('button', { variant: 'primary' }, 'Save draft')
     el('div', { class: 'flex gap-4' }, c('badge', {}, 'New'))
     c('modal', { name: 'confirm' }, { footer: [c('button', {}, 'Close')] }, 'Body')
--------------------------------------------------------------------------- */
import type { ComponentManifest, PropSpec } from './schema.ts';

export interface ComponentNode {
    c: string;
    p?: Record<string, unknown>;
    slots?: Record<string, ExampleNode[]>;
    children?: ExampleNode[];
}

export interface ElementNode {
    el: string;
    a?: Record<string, string | boolean>;
    children?: ExampleNode[];
}

export type ExampleNode = string | ComponentNode | ElementNode;

type Child = ExampleNode | ExampleNode[] | null | undefined | false;
type SlotMap = Record<string, Child | Child[]>;

const flat = (children: Child[]): ExampleNode[] =>
    children.flat(2).filter((child): child is ExampleNode => child !== null && child !== undefined && child !== false);

const isSlotMap = (value: unknown): value is SlotMap =>
    typeof value === 'object' && value !== null && !Array.isArray(value) && !('c' in value) && !('el' in value);

/** A component node. An optional plain object after the props names slots. */
export function c(name: string, props: Record<string, unknown> = {}, ...rest: (Child | SlotMap)[]): ComponentNode {
    let slots: Record<string, ExampleNode[]> | undefined;
    if (rest.length && isSlotMap(rest[0])) {
        slots = Object.fromEntries(
            Object.entries(rest.shift() as SlotMap).map(([key, value]) => [key, flat([value as Child])]),
        );
    }
    const children = flat(rest as Child[]);
    return { c: name, p: props, ...(slots ? { slots } : {}), ...(children.length ? { children } : {}) };
}

/** A plain element node (layout wrappers in examples). */
export function el(tag: string, attributes: Record<string, string | boolean> = {}, ...children: Child[]): ElementNode {
    const kids = flat(children);
    return { el: tag, a: attributes, ...(kids.length ? { children: kids } : {}) };
}

/* -------- rendering -------------------------------------------------------- */

export type Registry = Record<string, Pick<ComponentManifest, 'name' | 'svelte' | 'props'>>;

const VOID = new Set(['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr']);

const kebab = (name: string) => name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();

const escapeHtml = (text: string) => text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
const escapeAttr = (text: string) => escapeHtml(text).replace(/"/g, '&quot;');
// Svelte reads `{` as the start of an expression in text and attribute values.
const escapeSvelte = (text: string) => text.replace(/\{/g, '&#123;').replace(/\}/g, '&#125;');

/** A JSON value as a PHP literal (short array syntax). */
export function phpLiteral(value: unknown): string {
    if (value === null || value === undefined) return 'null';
    if (typeof value === 'boolean') return value ? 'true' : 'false';
    if (typeof value === 'number') return String(value);
    if (typeof value === 'string') {
        // A bound attribute sits inside "…", so a double quote is spliced in with chr(34).
        return value.split('"').map((part) => `'${part.replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`).join('.chr(34).');
    }
    if (Array.isArray(value)) return `[${value.map(phpLiteral).join(', ')}]`;
    return `[${Object.entries(value as Record<string, unknown>).map(([k, v]) => `${phpLiteral(k)} => ${phpLiteral(v)}`).join(', ')}]`;
}

const propSpec = (registry: Registry, component: string, name: string): PropSpec | undefined =>
    registry[component]?.props[name];

export interface BladeOptions {
    /** `x-sigta::` (canonical, used by tests and fixtures) or `sigta:` (the consumer-facing prefix). */
    prefix?: 'x-sigta::' | 'sigta:';
    indent?: string;
}

function bladeAttributes(registry: Registry, node: ComponentNode): string {
    const out: string[] = [];
    for (const [key, value] of Object.entries(node.p ?? {})) {
        if (value === undefined || value === null) continue;
        const spec = propSpec(registry, node.c, key);
        if (spec?.blade === false) continue;
        const name = spec ? (spec.blade && spec.blade.name) || kebab(key) : key;
        if (!spec) {
            // A forwarded attribute (disabled, href, aria-*, wire:*, x-*).
            if (value === true) out.push(name);
            else if (value !== false) out.push(`${name}="${escapeAttr(String(value))}"`);
        } else if (typeof value === 'string') {
            out.push(`${name}="${escapeAttr(value)}"`);
        } else {
            // A PHP expression: Blade evaluates it as written, so no HTML escaping.
            out.push(`:${name}="${phpLiteral(value)}"`);
        }
    }
    return out.length ? ' ' + out.join(' ') : '';
}

function elementAttributes(attributes: Record<string, string | boolean> = {}, svelte = false): string {
    const out: string[] = [];
    for (const [key, value] of Object.entries(attributes)) {
        if (value === false) continue;
        if (value === true) out.push(key);
        else out.push(`${key}="${svelte ? escapeSvelte(escapeAttr(value)) : escapeAttr(value)}"`);
    }
    return out.length ? ' ' + out.join(' ') : '';
}

export function toBlade(nodes: ExampleNode[], registry: Registry, options: BladeOptions = {}, depth = 0): string {
    const prefix = options.prefix ?? 'x-sigta::';
    const pad = (options.indent ?? '    ').repeat(depth);
    const inline = nodes.every((node) => typeof node === 'string');
    if (inline) return nodes.map((node) => escapeHtml(node as string)).join('');

    return nodes.map((node) => {
        if (typeof node === 'string') return pad + escapeHtml(node);
        if ('el' in node) {
            const open = `<${node.el}${elementAttributes(node.a)}>`;
            if (VOID.has(node.el)) return pad + open;
            if (!node.children?.length) return `${pad}${open}</${node.el}>`;
            if (node.children.every((child) => typeof child === 'string')) {
                return `${pad}${open}${toBlade(node.children, registry, options)}</${node.el}>`;
            }
            return `${pad}${open}\n${toBlade(node.children, registry, options, depth + 1)}\n${pad}</${node.el}>`;
        }
        const tag = `${prefix}${node.c}`;
        const attributes = bladeAttributes(registry, node);
        const slots = Object.entries(node.slots ?? {});
        const children = node.children ?? [];
        if (!slots.length && !children.length) return `${pad}<${tag}${attributes} />`;
        if (!slots.length && children.every((child) => typeof child === 'string')) {
            return `${pad}<${tag}${attributes}>${toBlade(children, registry, options)}</${tag}>`;
        }
        const inner: string[] = slots.map(([slot, content]) => {
            const body = content.every((child) => typeof child === 'string')
                ? toBlade(content, registry, options)
                : `\n${toBlade(content, registry, options, depth + 2)}\n${pad}${options.indent ?? '    '}`;
            // Blade's slot tag takes no prefix: <x-slot:footer>.
            return `${pad}${options.indent ?? '    '}<x-slot:${slot}>${body}</x-slot:${slot}>`;
        });
        if (children.length) {
            inner.push(children.every((child) => typeof child === 'string')
                ? `${pad}${options.indent ?? '    '}${toBlade(children, registry, options)}`
                : toBlade(children, registry, options, depth + 1));
        }
        return `${pad}<${tag}${attributes}>\n${inner.join('\n')}\n${pad}</${tag}>`;
    }).join('\n');
}

function svelteValue(value: unknown): string {
    return `{${JSON.stringify(value)}}`;
}

function svelteAttributes(registry: Registry, node: ComponentNode): string {
    const out: string[] = [];
    for (const [key, value] of Object.entries(node.p ?? {})) {
        if (value === undefined || value === null) continue;
        const spec = propSpec(registry, node.c, key);
        if (spec?.svelte === false) continue;
        const name = (spec?.svelte && spec.svelte.name) || key;
        if (!spec) {
            if (key.startsWith('wire:') || key.startsWith('x-')) continue;
            if (value === true) out.push(name);
            else if (value !== false) out.push(`${name}="${escapeSvelte(escapeAttr(String(value)))}"`);
        } else if (typeof value === 'string') {
            out.push(`${name}="${escapeSvelte(escapeAttr(value))}"`);
        } else {
            out.push(`${name}=${svelteValue(value)}`);
        }
    }
    return out.length ? ' ' + out.join(' ') : '';
}

export function toSvelte(nodes: ExampleNode[], registry: Registry, depth = 0, indent = '    '): string {
    const pad = indent.repeat(depth);
    if (nodes.every((node) => typeof node === 'string')) {
        return nodes.map((node) => escapeSvelte(escapeHtml(node as string))).join('');
    }
    return nodes.map((node) => {
        if (typeof node === 'string') return pad + escapeSvelte(escapeHtml(node));
        if ('el' in node) {
            const open = `<${node.el}${elementAttributes(node.a, true)}>`;
            if (VOID.has(node.el)) return pad + open;
            if (!node.children?.length) return `${pad}${open}</${node.el}>`;
            if (node.children.every((child) => typeof child === 'string')) {
                return `${pad}${open}${toSvelte(node.children, registry)}</${node.el}>`;
            }
            return `${pad}${open}\n${toSvelte(node.children, registry, depth + 1, indent)}\n${pad}</${node.el}>`;
        }
        const tag = registry[node.c]?.svelte;
        if (!tag) throw new Error(`Unknown component in example: ${node.c}`);
        const attributes = svelteAttributes(registry, node);
        const slots = Object.entries(node.slots ?? {});
        const children = node.children ?? [];
        if (!slots.length && !children.length) return `${pad}<${tag}${attributes} />`;
        if (!slots.length && children.every((child) => typeof child === 'string')) {
            return `${pad}<${tag}${attributes}>${toSvelte(children, registry)}</${tag}>`;
        }
        const inner: string[] = slots.map(([slot, content]) => {
            const body = content.every((child) => typeof child === 'string')
                ? `${pad}${indent}${indent}${toSvelte(content, registry)}`
                : toSvelte(content, registry, depth + 2, indent);
            return `${pad}${indent}{#snippet ${slot}()}\n${body}\n${pad}${indent}{/snippet}`;
        });
        if (children.length) {
            inner.push(children.every((child) => typeof child === 'string')
                ? `${pad}${indent}${toSvelte(children, registry)}`
                : toSvelte(children, registry, depth + 1, indent));
        }
        return `${pad}<${tag}${attributes}>\n${inner.join('\n')}\n${pad}</${tag}>`;
    }).join('\n');
}

/** The `import { … } from '@sigta-hq/svelte'` line an example needs. */
export function svelteImports(nodes: ExampleNode[], registry: Registry): string {
    const names = new Set<string>();
    const walk = (list: ExampleNode[]) => list.forEach((node) => {
        if (typeof node === 'string') return;
        if ('c' in node) {
            const tag = registry[node.c]?.svelte;
            if (tag) names.add(tag.split('.')[0]);
            Object.values(node.slots ?? {}).forEach(walk);
        }
        walk(node.children ?? []);
    });
    walk(nodes);
    return names.size ? `import { ${[...names].sort().join(', ')} } from '@sigta-hq/svelte';` : '';
}
