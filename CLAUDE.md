# Sigta UI — agent guide

Sigta UI is one design system with two runtimes: **Livewire/Blade** (the
Composer package `sigta/ui`) and **Svelte 5** (`@sigta-hq/svelte`). The look,
tokens, variants, parts, accessibility and motion language are shared and
strict; only rendering and state flow differ per runtime. The decisions behind
this are in [RUNTIMES-PLAN.md](RUNTIMES-PLAN.md); the component contract
rules are in [CONVENTIONS.md](CONVENTIONS.md) and
[SECTION-CONVENTIONS.md](SECTION-CONVENTIONS.md). Read them before changing a
component.

## The parity rule (non-negotiable)

A component change is never made in one runtime only. Adding, removing or
changing a component, a prop, a default, a variant, a part, a state, a slot or
an event touches all of these in the same change:

1. **Manifest** — `packages/contract/src/manifests/<name>.ts` (the contract).
2. **CSS** — `packages/contract/css/components/<name>.css` (the look).
3. **Blade** — `resources/views/components/<name>.blade.php` (Livewire adapter).
4. **Svelte** — `packages/svelte/src/lib/components/…` (Svelte adapter).
5. **Tests** — Pest (`tests/`), Vitest (`packages/*/test`), and the
   conformance check that renders every manifest example in both runtimes.
6. **Generated files** — run `npm run generate` and commit the output
   (manifest JSON, both runtimes' LLM stubs, docs data). Never edit generated
   files by hand; they say so in their header.

`npm run check` runs the whole gate (build, generate --check, conformance,
Vitest, Pest, token guard). It must be green before a change is done.

## Where things live

| What | Where | Rule |
|---|---|---|
| Tokens & theme | `resources/css/theme.css`, `theme.example.css` | the only place raw colour values may appear |
| Tailwind bridge, base, prose, motion keyframes | `resources/css/core.css` | references `--sigta-*` only |
| Component look | `packages/contract/css/components/*.css` | contract selectors only: `[data-sigta]`, `[data-part]`, `[data-variant]`, `[data-size]`, state attributes, ARIA. Compiled to plain CSS in `@layer components` |
| Component contract | `packages/contract/src/manifests/*.ts` | typed; compiled to committed JSON |
| Shared behavior | `packages/core/src/` | framework-free, SSR-safe |
| Livewire adapter | `resources/views/components/`, `resources/js/sigta.js` | structure + attributes + Alpine glue only |
| Svelte adapter | `packages/svelte/src/lib/` | structure + attributes + runes/glue only |
| Built assets Composer ships | `dist/` | committed; rebuilt by `npm run build` |

## Adapters never style

Adapters render structure and set contract attributes. They never carry
design classes (colours, spacing, typography, borders, motion). A consumer's
`class` is merged onto the root and wins over the component layer, because
component CSS lives in `@layer components` below Tailwind's `utilities`.
Conditional looks are state attributes (`aria-invalid`, `data-state`,
`data-selected`, …), never class toggles.

## Behavior: split by kind

- **Signature behavior** (motion: complete/fold, celebrate, count, loader
  landing, settle; toast store; floating positioning; focus trap; roving
  tabindex) lives once in `@sigta-hq/core`. Both runtimes attach it.
- **Widget state** (combobox, tabs, calendar, listbox, kanban, …) is native
  per runtime — Alpine in Blade, runes in Svelte — built on the core's pure
  logic helpers (filtering, keyboard maps, date math, formatting).
- **Runtime glue** stays in its adapter: Livewire morph hooks, message
  interception, `data-loading`, `Livewire.dispatch`; Svelte keyed `{#each}` +
  core transitions, Inertia router events.

Core rules: never touch `window`/`document` at import time, only when called
or attached; never keep a module-level store that would be shared across SSR
requests; export motion as keyframes + timing read from the `--sigta-dur-*` /
`--sigta-curve-*` tokens so Alpine can play it and Svelte can wrap it as a
transition.

`window.Sigta`, `$sigta.toast/complete/celebrate/…` and the `sigta-toast`,
`sigta-celebrate`, `sigta-save-state`, `sigta-modal-show/hide` window events
are a public compatibility contract.

## API vocabulary: mirrored, mechanics idiomatic

Component names and nesting, prop names, variant values, part names and slot
names are identical across runtimes (`<sigta:dropdown.item>` ↔
`<Dropdown.Item>`; `icon:trailing` ↔ `iconTrailing`; slot `footer` ↔ snippet
`footer`). How state flows follows each runtime: Blade forwards `wire:model`
onto the native element and emits window/DOM events; Svelte exposes
`bind:value` / `bind:open`, callback props (`onchange`, `onmove`, …) and
snippets with parameters. Blade behavior that is inferred (a button's busy
state from `wire:click`) is an explicit prop in Svelte (`loading`).

Data props use neutral shapes (ISO date strings, plain pagination objects,
label props with English defaults). Blade may additionally accept PHP types
(Carbon, a paginator) and normalizes them.

## Livewire compatibility

Existing Livewire apps must keep working without changes: tags, props, slots,
`data-sigta`, `wire:*` forwarding, `Sigta::toast()` / `Sigta::celebrate()`,
`@sigtaScripts`, `sigta:install` and the CSS import paths under
`resources/css/` are stable. Rendered utility classes are not part of the API.

## Svelte server boundary

`@sigta-hq/svelte` works without any backend. `@sigta-hq/svelte/inertia` is
optional and reads a backend-agnostic page prop:

```json
{ "sigta": { "toasts": [{ "text": "Saved", "variant": "success" }], "celebrate": { "level": "small" } } }
```

Any backend (Laravel, Go, …) can emit it. Never add a PHP or Laravel
dependency to the npm packages.

## LLM context files

The stubs consumers' agents read (`stubs/sigta-ui.md`,
`stubs/sigta-components.md` for Blade; `packages/svelte/stubs/*` for Svelte)
are generated from the manifests plus hand-written rule files. Change the
manifest or the rule source, then `npm run generate` — never the generated
file. The rules from CONVENTIONS.md ("never name another component library",
tokens only, always use Sigta components) apply to both editions.

## Commands

```bash
npm install                 # workspaces (contract, core, svelte, docs, harness)
composer install            # Pest + Testbench
npm run build               # CSS, core, Livewire runtime IIFE, editor, Svelte package
npm run generate            # manifest JSON, stubs (both runtimes), docs data
npm run conformance         # Blade vs Svelte renders of every manifest example
npm run test                # Vitest (core + svelte)
./vendor/bin/pest           # Blade adapter + PHP
./scripts/check-tokens.sh   # no raw colours outside the theme
npm run check               # all of the above — the merge gate
```
