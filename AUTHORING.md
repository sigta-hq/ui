# Building a component on the contract

How a component is made so both runtimes render it: one manifest, one
stylesheet, a Blade view and a Svelte component that emit the same DOM. Every
component in the package was moved onto the contract this way, so the
existing ones show every pattern below in working code — start from the
closest one (`button`, `input`, `field`, `modal`, `autocomplete`, `toast`,
`tabs`, `kanban`, `section.hero`). Read [CLAUDE.md](CLAUDE.md) and
[CONVENTIONS.md](CONVENTIONS.md) first.

The bar for every change: **both runtimes agree** (conformance, server render
and browser), **the Livewire look does not move unless you meant it to** (the
visual check against a baseline taken before the change), and **the Blade API
stays compatible** (same tag, `@props`, slots, attribute and `wire:*`
forwarding).

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

- For a change: the manifest, the stylesheet, both adapters, the kit page
  (`playground/resources/views/kit/<page>.blade.php`), the Pest tests and any
  Alpine component it uses in `resources/js/sigta.js`.
- For a new component: the closest existing one, and the components the new
  one renders. Add it to `resources/catalog.php` (the kit sidebar, the docs
  navigation and the stub ordering all read it) and give it a kit page.
- Take a visual baseline of the kit pages you may affect before you edit
  anything (`node scripts/visual-snapshot.js <dir> http://127.0.0.1:8321 <pages…>`).

## 2. The stylesheet

The look is written as Tailwind `@apply` rules on contract selectors — the
token utilities (`bg-surface`, `font-mono`, `rounded-panel`, …) are the
vocabulary, the selectors are the contract:

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
- **A fallback look** ("anything else looks like secondary") is
  `&:not(:is([data-variant="a"], [data-variant="b"]))` so unknown values keep
  the fallback look.
- **Conditional looks** (an error border, a selected row) are state
  attributes, never class toggles: prefer ARIA
  (`aria-invalid`, `aria-selected`, `aria-current`, `aria-expanded`,
  `aria-pressed`, `aria-checked`, `aria-disabled`), else `data-*`
  (`data-active`, `data-selected`, `data-open`). In Alpine:
  `x-bind:data-selected="sel ? '' : null"` / `x-bind:aria-selected="…"`.
- **Computed values** (a width from a percentage, grid columns from a prop)
  become a CSS custom property set inline by both runtimes:
  `style="--sigta-progress: 42"` and `width: calc(var(--sigta-progress) * 1%)`.
- **group-hover / peer** effects are nested selectors
  (`[data-sigta="card"]:hover [data-part="arrow"]`).
- **Enter/leave transitions** are CSS: an `animation` on insertion and a
  `data-leaving` (or similar) state the runtime sets before removing; see
  `toast.css`.
- **Classes that are hooks, not looks** (`sigta-loader-*`, `sigta-prose`,
  `sigta-stamp`, `sigta-write`) may stay; they are part of the contract.
- **Precedence.** Rules are plain CSS, so specificity and order decide —
  not Tailwind's utility sort. A variant nested under the root beats the
  root's own rule; a part rule a parent component sets on a child (a
  section sizing its heading) needs a selector specific enough to beat the
  child's own rule. Check the result in both runtimes, not the source.
- Raw hex colours are never allowed (`scripts/check-tokens.sh`).

Run `node packages/contract/scripts/build.ts` after editing CSS.

## 3. The Blade view

- `@props` match the manifest (names and defaults); the Pest contract test
  compares them. Never rename or drop an existing prop (Livewire apps rely
  on it).
- No design classes; only the data attributes. Keep `$attributes`
  forwarding where it was (root, or the native element for form controls),
  so a consumer's `class`, `wire:*`, `x-*`, `id`, `aria-*` land where they did.
  `$attributes->merge(['data-variant' => $variant])` is fine; a consumer's
  `class` is merged in and wins over the component layer.
- Behavior is a registered `Alpine.data('sigta<Name>')` in
  `resources/js/sigta.js` with a constant `x-data` (never interpolate state
  into it; see CONVENTIONS.md).
- A server-rendered element the client hides until Alpine starts needs
  `x-cloak` (the Svelte twin renders it `hidden` on the server).

## 4. The manifest

Copy the shape of a pilot manifest. Props are the Blade `@props` with neutral
types and the same defaults (`null` for "unset"); mark native attributes the
component reads (`href`, `type`, `value`, `checked`, `name`) `native: true`,
and two-way ones `bindable: true`. Describe parts, states (the attributes
the CSS reads), slots, events (`blade:` how Livewire hears it, `svelte:` the
callback prop) and notes per runtime for the LLM stubs.

Examples: write the kit page's examples as neutral trees
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

Pest tests assert the contract: the data attributes in the markup, and the
look through `contractCss('<name>')` (the stylesheet source). Svelte gets its
contract test for free (props and defaults against the manifest); add a
Vitest test in `packages/svelte/test/` for runtime logic the conformance
examples cannot reach. Never delete a test to get green.

A whole page built from many components belongs in `harness/acceptance/`
(`<page>.blade.php` beside `<page>.svelte`, optional `<page>.flows.json` of
click-through steps). They are compared full-page at 1280 and 390 wide.

## 8. Verify

```bash
node packages/contract/scripts/generate.ts && node packages/contract/scripts/build.ts
node scripts/conformance.js <names…>              # server renders agree
node scripts/conformance.js <names…> --browser    # live DOM + pixels + interactions agree
./vendor/bin/pest tests/Components/<X>Test.php tests/ContractTest.php
npx vitest run --root packages/svelte
node scripts/conformance.js acceptance --browser   # whole pages and their flows
SIGTA_VISUAL_DIR=<scratch dir> scripts/visual-check.sh <baseline-dir> <kit pages…>   # Livewire look unchanged
./scripts/check-tokens.sh
```

`--write <dir>` on the conformance run keeps both runtimes' screenshots for a
look. The visual check needs the playground served on :8321.
