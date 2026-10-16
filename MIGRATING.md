# Moving a component onto the contract

How a Blade component that styles itself with utility classes becomes a
contract component that both runtimes render: one manifest, one stylesheet, a
Blade view and a Svelte component that emit the same DOM. Read
[CLAUDE.md](CLAUDE.md) and [CONVENTIONS.md](CONVENTIONS.md) first. The pilot
components show every pattern below in working code: `button`, `input`,
`field`, `label`, `heading`, `text`, `modal` (+ `modal.trigger`,
`modal.close`), `autocomplete` (+ `autocomplete.option`), `toast`,
`checkbox`, `icon`, `loader`.

The bar: **the Livewire look stays pixel-identical to today** (the visual
check), **both runtimes agree** (conformance, server render and browser), and
**the Blade API does not change** (same tag, `@props`, slots, attribute and
`wire:*` forwarding).

## Files per component

| | Path |
|---|---|
| Manifest | `packages/contract/src/manifests/<name>.ts` (`dropdown.item.ts`, `section.hero.ts`) |
| Stylesheet | `packages/contract/css/components/<name>.css` (one per family is fine: `table.css` can hold `table.*`) |
| Blade | `resources/views/components/<path>.blade.php` (unchanged path) |
| Svelte | `packages/svelte/src/lib/components/<Svelte>.svelte`, named by the manifest's `svelte` field: `Button.svelte`, `Dropdown.Item.svelte`, `Section.Hero.svelte` |
| Tests | `tests/Components/*Test.php` (update), Svelte contract test is automatic |

Generated, never edited by hand: `packages/contract/src/manifests/index.ts`,
`packages/contract/manifest.json`, `packages/svelte/src/lib/index.ts`,
`packages/contract/dist/css/*`, `dist/sigta.js`.

## 1. Read before writing

- The Blade view, its kit page (`playground/resources/views/kit/<page>.blade.php`),
  its Pest tests, and any Alpine component it uses in `resources/js/sigta.js`.
- The components it renders (`<x-sigta::…>` inside it). A Svelte component can
  only use Svelte components that already exist; if a dependency is not
  migrated yet and is outside your batch, say so in your report.

## 2. The stylesheet — move the look, keep it identical

Write the classes from the Blade view as `@apply` rules on contract selectors:

```css
[data-sigta="badge"] {                       /* the root: data-sigta="<name>" */
    @apply inline-flex items-center gap-1 font-mono …;

    &[data-variant="accent"] { @apply bg-accent …; }      /* props → data-* */
    &[data-size="sm"] { @apply text-[0.62rem]; }
}
[data-scope="badge"][data-part="dot"] {      /* inner elements → parts */
    @apply size-1.5 rounded-full bg-current;
}
```

- **Root**: `data-sigta="<name>"` (already there in most views).
- **Parts**: every inner element that carried classes gets
  `data-scope="<name>" data-part="<part>"`. Never select a part without its
  scope (`[data-part="label"]` alone leaks into nested components).
- **Variants and sizes**: emit the prop as `data-variant`, `data-size`,
  `data-tone`, `data-position`… (always, including the default value).
- **A `match` default arm** ("anything else looks like secondary") becomes
  `&:not(:is([data-variant="a"], [data-variant="b"]))` so unknown values keep
  today's fallback look.
- **Conditional classes** (`$error ? 'border-danger' : 'border-line'`,
  Alpine `:class="{ … }"`) become state attributes: prefer ARIA
  (`aria-invalid`, `aria-selected`, `aria-current`, `aria-expanded`,
  `aria-pressed`, `aria-checked`, `aria-disabled`), else `data-*`
  (`data-active`, `data-selected`, `data-open`). In Alpine:
  `x-bind:data-selected="sel ? '' : null"` / `x-bind:aria-selected="…"`.
- **Computed values** (a width from a percentage, grid columns from a prop)
  become a CSS custom property set inline by both runtimes:
  `style="--sigta-progress: 42"` and `width: calc(var(--sigta-progress) * 1%)`.
- **group-hover / peer** utilities become nested selectors
  (`[data-sigta="card"]:hover [data-part="arrow"]`).
- **`x-transition` classes** become CSS: an `animation` on insertion and a
  `data-leaving` (or similar) state the runtime sets before removing; see
  `toast.css`.
- **Classes that are hooks, not looks** (`sigta-loader-*`, `sigta-prose`,
  `sigta-stamp`, `sigta-write`) may stay; they are part of the contract.
- **Precedence trap.** Two utilities on one element that set the same property
  (a size's `px-[1.4em]` and a variant's `px-[0.6em]`) were resolved by
  Tailwind's internal sort order, not by intent — often the "wrong" one won.
  Nested selectors make the variant win instead. Match what renders today:
  the visual check will show any difference; drop the dead utility.
- Raw hex colours are never allowed (`scripts/check-tokens.sh`).

Run `node packages/contract/scripts/build.ts` after editing CSS.

## 3. The Blade view

- Keep `@props` exactly (names, defaults, order is free). The Pest contract
  test compares them with the manifest.
- Remove every design class; add the data attributes. Keep `$attributes`
  forwarding where it was (root, or the native element for form controls),
  so a consumer's `class`, `wire:*`, `x-*`, `id`, `aria-*` land where they did.
  `$attributes->merge(['data-variant' => $variant])` is fine; a consumer's
  `class` is merged in and wins over the component layer.
- Keep Alpine behavior and Livewire glue as they are; only class bindings
  change. Never interpolate state into `x-data` (see CONVENTIONS.md).
- A server-rendered element the client hides until Alpine starts needs
  `x-cloak` (the Svelte twin renders it `hidden` on the server).

## 4. The manifest

Copy the shape of a pilot manifest. Props are the Blade `@props` with neutral
types and the same defaults (`null` for "unset"); mark native attributes the
component reads (`href`, `type`, `value`, `checked`, `name`) `native: true`,
and two-way ones `bindable: true`. Describe parts, states (the attributes
the CSS reads), slots, events (`blade:` how Livewire hears it, `svelte:` the
callback prop) and notes per runtime for the LLM stubs.

Examples: port the kit page's examples as neutral trees
(`c('badge', { variant: 'accent' }, 'New')`, `el('div', { class: … }, …)`).
They are the docs and the conformance fixtures at once.

- Interactive behavior gets an example with `interactions` (click, hover,
  focus, fill, press, script) so the browser check compares the state after.
- Content a runtime renders on the client (Alpine `x-for` / `x-text`) gets
  `conformance: 'browser'`.
- Anything a tree cannot say (bindings, scripts, x-data lists) is an example
  with `blade` and `svelte` source snippets and `conformance: 'browser'`.
- An example with random output that cannot be normalized: `conformance: false`.

Run `node packages/contract/scripts/generate.ts` after adding a manifest.

## 5. The Svelte component

- Same DOM as the Blade render: same elements, attributes, parts, ARIA, text.
  Attribute order and whitespace do not matter; Alpine/Livewire attributes
  (`x-*`, `wire:*`, `@*`, `:*`) are ignored by the comparison.
- `let { …props with the manifest defaults…, children, ...rest } = $props();`
  — the Vitest contract test checks names and defaults. Two-way props use
  `$bindable(default)`. Spread `...rest` where Blade forwards `$attributes`.
- Slots → snippets with the same name (`footer` slot → `footer` snippet
  prop, `{@render footer?.()}`); the default slot is `children`.
- Events → callback props (`onchange`, `onmove`, `onsubmit`) and, where the
  Blade version dispatches window/DOM events others rely on (modal, toast,
  save-state), dispatch the same events too.
- Ids: `const uid = $props.id();` and the same prefix as Blade
  (`sigta-${uid}`, `sigta-option-${uid}`).
- Compound components share state through context (see
  `runtime/combobox.svelte.ts` and `Autocomplete.Option.svelte`).
- Shared behavior comes from `@sigta-hq/core` (pure logic, motion, toasts,
  positioning); page-wide behavior from `ensureRuntime()` (call it in
  `onMount` of components that rely on window.Sigta or the observers).
- Hidden-until-open elements render `hidden={!open}` (the comparison treats
  `hidden`, `x-cloak` and `display:none` alike).
- Import with `.js` specifiers (`'../runtime/runtime.js'`), never `.ts`.
- `<svelte:element this={tag}>` for a dynamic tag (see `Heading.svelte`).

## 6. Behavior both runtimes need

If the Alpine component holds logic that is not Alpine-specific (date math,
parsing, formatting, a keyboard map, a state machine), move it into
`packages/core/src/` as plain functions (SSR-safe: no `window`/`document` at
import time) and use it from both `resources/js/sigta.js` (or the view's
inline Alpine) and the Svelte component. Rebuild the Livewire runtime with
`node scripts/build-livewire.js`. Keep every field an Alpine component writes
declared in its returned object (`tests/RuntimeTest.php` checks it).

## 7. Tests

Pest tests that asserted classes now assert the contract: the data attributes
in the markup, and the look through `contractCss('<name>')` (the stylesheet
source). Keep every test's intent; never delete one to get green.

## 8. Verify

```bash
node packages/contract/scripts/generate.ts && node packages/contract/scripts/build.ts
node scripts/conformance.js <names…>              # server renders agree
node scripts/conformance.js <names…> --browser    # live DOM + pixels + interactions agree
./vendor/bin/pest tests/Components/<X>Test.php tests/ContractTest.php
npx vitest run --root packages/svelte
SIGTA_VISUAL_DIR=<scratch dir> scripts/visual-check.sh <baseline-dir> <kit pages…>   # Livewire look unchanged
./scripts/check-tokens.sh
```

`--write <dir>` on the conformance run keeps both runtimes' screenshots for a
look. The visual check needs the playground served on :8321.
