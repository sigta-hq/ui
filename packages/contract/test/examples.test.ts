// One example tree, two syntaxes.
import { describe, expect, it } from 'vitest';
import { c, el, phpLiteral, svelteImports, toBlade, toSvelte, type Registry } from '../src/examples.ts';
import { defineComponent } from '../src/schema.ts';

const registry: Registry = {
    button: { name: 'button', svelte: 'Button', props: { variant: { type: 'enum', values: ['primary'], description: '' }, loading: { type: 'boolean', description: '' } } },
    'section.hero': { name: 'section.hero', svelte: 'Section.Hero', props: { primaryLabel: { type: 'string', description: '' }, items: { type: 'array', description: '' } } },
    modal: { name: 'modal', svelte: 'Modal', props: { open: { type: 'boolean', description: '', blade: false } } },
};

describe('example trees', () => {
    it('renders a component in both syntaxes', () => {
        const tree = [c('button', { variant: 'primary', disabled: true }, 'Save')];
        expect(toBlade(tree, registry)).toBe('<x-sigta::button variant="primary" disabled>Save</x-sigta::button>');
        expect(toBlade(tree, registry, { prefix: 'sigta:' })).toBe('<sigta:button variant="primary" disabled>Save</sigta:button>');
        expect(toSvelte(tree, registry)).toBe('<Button variant="primary" disabled>Save</Button>');
    });

    it('binds non-string props and kebab-cases multi-word props for Blade', () => {
        const tree = [c('section.hero', { primaryLabel: 'Book', items: [{ q: 'Why?' }] })];
        expect(toBlade(tree, registry)).toBe(`<x-sigta::section.hero primary-label="Book" :items="[['q' => 'Why?']]" />`);
        expect(toSvelte(tree, registry)).toBe('<Section.Hero primaryLabel="Book" items={[{"q":"Why?"}]} />');
        expect(svelteImports(tree, registry)).toBe("import { Section } from '@sigta-hq/svelte';");
    });

    it('turns named slots into snippets', () => {
        const tree = [c('button', {}, { icon: ['x'] }, 'Close')];
        expect(toBlade(tree, registry)).toContain('<x-slot:icon>x</x-slot:icon>');
        expect(toSvelte(tree, registry)).toContain('{#snippet icon()}');
    });

    it('leaves out props a runtime does not have', () => {
        expect(toBlade([c('modal', { open: true })], registry)).toBe('<x-sigta::modal />');
        expect(toSvelte([c('modal', { open: true })], registry)).toBe('<Modal open={true} />');
    });

    it('escapes text for each syntax', () => {
        const tree = [el('p', {}, 'a < b {c}')];
        expect(toBlade(tree, registry)).toBe('<p>a &lt; b {c}</p>');
        expect(toSvelte(tree, registry)).toBe('<p>a &lt; b &#123;c&#125;</p>');
    });

    it('keeps a double quote out of the bound attribute', () => {
        expect(phpLiteral('say "hi"')).toBe(`'say '.chr(34).'hi'.chr(34).''`);
    });

    it('writes PHP literals', () => {
        expect(phpLiteral({ a: [1, true, null], "it's": 'x' })).toBe(`['a' => [1, true, null], 'it\\'s' => 'x']`);
    });
});

describe('defineComponent', () => {
    it('rejects an enum default outside its values', () => {
        expect(() => defineComponent({
            name: 'x', title: 'X', tier: 'elements', page: 'x', svelte: 'X', description: '',
            props: { size: { type: 'enum', values: ['sm'], default: 'lg', description: '' } }, examples: [],
        })).toThrow(/not one of its values/);
    });
});
