// The Svelte runtime against the contract: every manifest component has a
// Svelte component whose $props() destructure carries the manifest's props
// with the manifest's defaults.
import { describe, expect, it } from 'vitest';
import { readFileSync, existsSync } from 'node:fs';
import { join } from 'node:path';
import { parse } from 'svelte/compiler';

const root = join(__dirname, '../../..');
const { components } = JSON.parse(readFileSync(join(root, 'packages/contract/manifest.json'), 'utf8'));
const fileOf = (svelte: string) => join(root, 'packages/svelte/src/lib/components', `${svelte}.svelte`);

interface Destructured { name: string; hasDefault: boolean; value: unknown }

function destructuredProps(source: string): Destructured[] {
    const ast = parse(source, { modern: true }) as any;
    const out: Destructured[] = [];
    const visit = (node: any) => {
        if (!node || typeof node !== 'object') return;
        if (node.type === 'VariableDeclarator' && node.init?.type === 'CallExpression' && node.init.callee?.name === '$props' && node.id?.type === 'ObjectPattern') {
            for (const property of node.id.properties) {
                if (property.type !== 'Property') continue;
                const name = property.key.name ?? property.key.value;
                const value = property.value;
                if (value.type === 'AssignmentPattern') {
                    const right = value.right;
                    const literal = right.type === 'Literal' ? right.value
                        : right.type === 'Identifier' && right.name === 'undefined' ? undefined
                        : right.type === 'ArrayExpression' || right.type === 'ObjectExpression' ? JSON.parse(source.slice(right.start, right.end).replace(/'/g, '"').replace(/(\w+):/g, '"$1":'))
                        : right.type === 'UnaryExpression' && right.operator === '-' ? -right.argument.value
                        : '<expression>';
                    out.push({ name, hasDefault: true, value: literal });
                } else {
                    out.push({ name, hasDefault: false, value: undefined });
                }
            }
        }
        for (const key of Object.keys(node)) {
            if (key === 'parent') continue;
            const child = node[key];
            if (Array.isArray(child)) child.forEach(visit);
            else if (child && typeof child === 'object' && typeof child.type === 'string') visit(child);
        }
    };
    visit(ast.instance?.content);
    return out;
}

describe('contract', () => {
    for (const component of components) {
        it(`${component.name} has a Svelte component with the manifest props`, () => {
            const file = fileOf(component.svelte);
            expect(existsSync(file), `missing ${file}`).toBe(true);
            const props = new Map(destructuredProps(readFileSync(file, 'utf8')).map((p) => [p.name, p]));
            for (const [name, spec] of Object.entries<any>(component.props)) {
                if (spec.svelte === false) continue;
                const key = spec.svelte?.name ?? name;
                const found = props.get(key);
                if (spec.native && !found) continue;
                expect(found, `${component.name}: prop ${key} is not destructured from $props()`).toBeDefined();
                if (spec.required) {
                    expect(found!.hasDefault, `${component.name}: required prop ${key} has a default`).toBe(false);
                } else if (spec.default === null || spec.default === undefined) {
                    expect([null, undefined], `${component.name}: ${key} should default to nothing`).toContain(found!.value);
                } else {
                    expect(found!.value, `${component.name}: default of ${key}`).toEqual(spec.default);
                }
            }
        });
    }
});
