# Sigta UI — agent guide

Sigta UI is one design system with two runtimes: **Livewire/Blade** (the
Composer package `sigta/ui`) and **Svelte 5** (`@sigta-hq/svelte`). The look,
tokens, variants, parts, accessibility and motion language are shared and
strict; only rendering and state flow differ per runtime. An app uses one
runtime. Every one of the components exists in both, and both must keep
rendering the same DOM, pixels and behavior.

Read before changing a component:
- [RUNTIMES-PLAN.md](RUNTIMES-PLAN.md): the decisions behind the design.
- [CONVENTIONS.md](CONVENTIONS.md): the component contract rules.
- [SECTION-CONVENTIONS.md](SECTION-CONVENTIONS.md): page sections.
- [AUTHORING.md](AUTHORING.md): the step-by-step recipe.

## Decisions (settled — do not reopen without the owner)

| Question | Decision |
|---|---|
| What is shared | A **CSS + DOM contract**: one prebuilt stylesheet keyed on `data-sigta`, `data-scope` + `data-part`, `data-variant` / `data-size` / … and state attributes. Both adapters emit that DOM. |
| Behavior | **Split by kind.** Signature behavior lives once in `@sigta-hq/core`. Widget state is native per runtime (Alpine, runes) on core's pure logic. Runtime glue stays in its adapter. |
| Source of truth | A **machine-readable manifest** per component (`packages/contract/src/manifests/*.ts`). Docs, LLM stubs, the Svelte index and conformance fixtures are generated from it. |
| API shape | **Mirror the vocabulary, idiomatic mechanics.** Same names, props, variants, parts and slots. State flows the way each runtime does it. |
| Scope | **Full parity**: every component in both runtimes. No Livewire-only or Svelte-only components. |
| Distribution | Composer for Livewire. **GitHub Packages (private)** for `@sigta-hq/contract`, `@sigta-hq/core` and `@sigta-hq/svelte`, versioned in lockstep. |
| Docs | **One generated docs site** with a Livewire and a Svelte edition and a runtime toggle (`docs/`). |
| Backend | The Svelte runtime is **backend agnostic**: no PHP, Laravel or Composer needed (Go and others work). Inertia is optional. |
| Livewire API | **Must stay compatible**: existing Livewire apps keep working without changes. |

## The parity rule (non-negotiable)

A component change is never made in one runtime only. Adding, removing or
changing a component, a prop, a default, a variant, a part, a state, a slot,
an event or a behavior touches all of these in the same change:

1. **Manifest:** `packages/contract/src/manifests/<name>.ts` (the contract,
   its examples and its usage notes).
2. **CSS:** `packages/contract/css/components/<name>.css` (the look).
3. **Blade:** `resources/views/components/<name>.blade.php`, plus any Alpine
   component in `resources/js/sigta.js`.
4. **Svelte:** `packages/svelte/src/lib/components/<Name>.svelte`, plus any
   context class in `packages/svelte/src/lib/runtime/`.
5. **Core:** `packages/core/src/` when the behavior is logic both runtimes
   need. Write it once, never as two copies.
6. **Tests:**
   - Pest (`tests/`) and Vitest (`packages/*/test/`).
   - The manifest's examples, which are the conformance fixtures. Every
     variant and state gets one; behavior gets `interactions`.
7. **Generated files:** run `npm run build` and `npm run generate`, then
   commit the output. Never edit generated files by hand; they say so in
   their header.

A fix found in one runtime is a fix for both. A bug found in Blade is checked
in Svelte, and the reverse.

`npm run check` runs the whole gate and must be green before a change is done:
- build and `generate --check`
- Vitest and svelte-check
- conformance, server-rendered and in the browser
- Pest and the token guard

When a change could move the Livewire look, also run the visual check against
a baseline taken before the change (see Commands).

## Where things live

| What | Where | Rule |
|---|---|---|
| Tokens & theme | `packages/contract/css/theme.css`, `theme.example.css` | the only place raw colour values may appear; `resources/css/*.css` only forward to the contract |
| Tailwind bridge, base, prose, motion keyframes | `packages/contract/css/{tailwind,base,prose,motion}.css` | references `--sigta-*` only |
| Component look | `packages/contract/css/components/*.css` | `@apply` + token utilities on contract selectors only (`[data-sigta]`, `[data-scope][data-part]`, `[data-variant]`, `[data-size]`, state attributes, ARIA). Built to plain CSS in `@layer components` (`packages/contract/dist/css/`, committed) |
| Component contract | `packages/contract/src/manifests/*.ts` | typed (`schema.ts`); compiled to `manifest.json` (committed) |
| Shared section content | `packages/contract/src/sections/*.json` | read by Blade (`Sigta\Ui\Sections::get`) and Svelte (`@sigta-hq/contract/sections`) |
| Icons | `packages/contract/icons/` | one pinned Lucide set for both runtimes (`src/Icons.php`, `@sigta-hq/contract/icons`) |
| Shared behavior | `packages/core/src/` | framework-free, SSR-safe; `@sigta-hq/core/editor` (TipTap) is a separate entry |
| Livewire adapter | `resources/views/components/`, `resources/js/sigta.js` (→ `dist/sigta.js`), `resources/js/editor.js` (→ `dist/sigta-editor.js`) | structure + attributes + Alpine glue only |
| Svelte adapter | `packages/svelte/src/lib/` (`components/`, `runtime/`, `inertia/`), `bin/sigta.js` | structure + attributes + runes/glue only |
| LLM stub templates | `packages/contract/stubs/{rules,components}.md` | rendered per runtime into `stubs/` and `packages/svelte/stubs/` |
| Conformance | `scripts/conformance.js`, `scripts/harness.js`, `scripts/render-blade.php` | every manifest example rendered by both runtimes and compared |
| Acceptance pages | `harness/acceptance/<page>.{blade.php,svelte,flows.json}` | whole pages compared full-page at 1280 and 390, plus click-through flows |
| Docs site | `docs/build.js`, `docs/guides/*.md`, `docs/src/*.svelte` | generated from the manifests; guides use `<!-- @livewire -->` / `<!-- @svelte -->` blocks |

## Adapters never style

Adapters render structure and set contract attributes. They never carry
design classes: no colours, spacing, typography, borders or motion. A
consumer's `class` is merged onto the root and wins over the component layer,
because component CSS lives in `@layer components` below Tailwind's
`utilities`. Conditional looks are state attributes (`aria-invalid`,
`aria-selected`, `data-state`, `data-over`, …), never class toggles. A value
the look needs is a CSS custom property set inline by both runtimes
(`--sigta-progress`). The only classes an adapter may emit are contract hooks
(`sigta-loader-*`, `sigta-prose`, `group` on kanban cards for consumers'
`group-hover:`).

Component CSS is plain CSS after the build, so specificity and order decide
which rule wins, not Tailwind's utility sort. A part rule a parent sets on a
child must be scoped tightly enough to beat the child's own rule.

## Behavior: split by kind

- **Signature behavior** lives once in `@sigta-hq/core`, and both runtimes
  attach it:
  - motion: complete/fold, celebrate, count, loader landing, settle
  - the toast store and save states
  - floating positioning, focus trap, roving tabindex
- **Widget state** is native per runtime: Alpine `Alpine.data('sigta<Name>')`
  in Blade, runes and context classes in Svelte. Examples are the combobox,
  tabs, calendar, kanban and carousel. Both are built on core's pure logic
  helpers: filtering, keyboard steps, date and time math, money and duration
  parsing, drop math, editor commands.
- **Runtime glue** stays in its adapter:
  - Livewire: morph hooks, `data-loading`, `wire:model` forwarding, `Livewire.dispatch`.
  - Svelte: `$bindable`, callback props, keyed `{#each}` with core transitions,
    Inertia router events.

Core rules:
- Never touch `window`/`document` at import time, only when called or attached.
- Never keep a module-level store that SSR requests would share.
- Export motion as keyframes and timing read from the `--sigta-dur-*` /
  `--sigta-curve-*` tokens, so Alpine can play it and Svelte can wrap it as a
  transition.

`window.Sigta`, `$sigta.toast/complete/celebrate/save/…` and the window
events below are a public compatibility contract. Svelte installs the same
`window.Sigta` through `ensureRuntime()`. The events:
- `sigta-toast`, `sigta-celebrate`, `sigta-save-state`
- `sigta-modal-show/hide`
- `sigta-composer-submit`, `sigta-kanban-move`, `sigta-editor-set`

## API vocabulary: mirrored, mechanics idiomatic

Component names and nesting, prop names, variant values, part names and slot
names are identical across runtimes:
- `<sigta:dropdown.item>` ↔ `<Dropdown.Item>`
- `icon:trailing` ↔ `iconTrailing`
- slot `footer` ↔ snippet `footer`; the default slot ↔ `children`

How state flows follows each runtime:
- Blade forwards `wire:model` onto the native element and emits DOM or window
  events.
- Svelte exposes `bind:value` / `bind:open`, callback props (`onchange`,
  `onmove`, `onsubmit`) and snippets with parameters. It dispatches the same
  DOM events where other code relies on them.
- Behavior that Blade infers is an explicit prop in Svelte, for example a
  button's busy state from `wire:click` becomes `loading`.
- A manifest prop marked `blade: false` or `svelte: false` exists in one
  runtime only. Use that only for runtime mechanics, never for a feature.

Data props use neutral shapes: ISO date strings, plain pagination objects,
plain arrays of objects. Blade may additionally accept PHP types (Carbon, a
paginator) and normalize them.

Sigta's own strings (aria labels, empty states) go through `__('English
text')` in Blade and `t('English text')` in Svelte, where
`const t = translator()` comes from `runtime/messages.ts`. An app translates
both with one map keyed by the English text: a Laravel `lang/<locale>.json`
on one side, `setMessages(map)` on the other. Add a string to both runtimes
at once; `packages/svelte/test/messages.test.ts` fails when Blade's `__()`
strings and Svelte's `t()` strings differ.

## Livewire compatibility

Existing Livewire apps must keep working without changes. These are stable:
- tags, props, slots and `data-sigta`
- `wire:*` forwarding
- `Sigta::toast()` / `Sigta::celebrate()`
- `@sigtaScripts` / `@sigtaEditorScripts` and `sigta:install`
- the CSS import paths under `resources/css/`

Rendered utility classes are not part of the API. A Blade `@props` name or
default never changes silently. `tests/ContractTest.php` compares it against
the manifest, and the Svelte contract test compares `$props()` against the
manifest.

Livewire rules that still bite:
- **Constant `x-data`.** Never interpolate state into `x-data`; read initial
  values from the DOM.
- **Declare every Alpine field you write.** `tests/RuntimeTest.php` checks it.
- **Keep third-party instances out of Alpine's reactive data.**
- **`wire:ignore` plus a stable id on library-owned DOM.**

## Svelte server boundary

`@sigta-hq/svelte` works without any backend. `@sigta-hq/svelte/inertia` is
optional and reads a backend-agnostic page prop:

```json
{ "sigta": { "toasts": [{ "text": "Saved", "variant": "success" }], "celebrate": { "level": "small" } } }
```

Any backend (Laravel, Go, …) can emit it. Never add a PHP or Laravel
dependency to the npm packages, and never require PHP in a Svelte consumer's
workflow (`npx sigta install` is pure Node).

## LLM context files

Consumers' agents read two stubs per runtime, both generated:
- Blade: `stubs/sigta-ui.md` and `stubs/sigta-components.md`.
- Svelte: `packages/svelte/stubs/*`.

They are built from the manifests (descriptions, props, slots, events,
`notes.shared` / `notes.livewire` / `notes.svelte`, `usage`) and the templates
in `packages/contract/stubs/`.

To change them:
- Edit the manifest or the template, then run `npm run generate`. Never edit
  the generated file.
- In shared text, write component tags as `{{tag:name}}`; each edition then
  spells them its own way.

The rules from CONVENTIONS.md apply to both editions: never name another
component library, tokens only, always use Sigta components.

## Commands

```bash
npm install && composer install   # workspaces (contract, core, svelte, harness, docs) + Pest/Testbench
npm run build                     # contract CSS + types, core, Livewire runtime (dist/sigta.js), editor bundle, Svelte package
npm run generate                  # manifest JSON, manifests index, Svelte index, LLM stubs (both runtimes)
npm run test                      # Vitest: contract, core, svelte
npm run check -w @sigta-hq/svelte # svelte-check
npm run conformance               # Blade vs Svelte server renders of every manifest example
npm run conformance -- --browser  # live DOM, screenshots and interactions in Chromium, plus acceptance pages
./vendor/bin/pest                 # Blade adapter + PHP
./scripts/check-tokens.sh         # no raw colours outside the theme
npm run check                     # all of the above — the merge gate
npm run docs                      # the docs site → docs/dist (both editions)
npm run consumer                  # pack the npm packages, install them into a fresh app outside the repo, build, SSR, drive it in Chromium (CI job; needs the registry)
npm run version -- <x.y.z>        # bump every package in lockstep; a v* tag publishes to GitHub Packages
```

Narrower loops:
- `node scripts/conformance.js <names…> [--browser] [--write <dir>]` checks
  only some components. `--write` keeps both runtimes' screenshots.
- `node scripts/conformance.js acceptance --browser` runs the whole pages and
  their flows.
- `scripts/visual-check.sh <baseline-dir> <kit pages…>` compares the
  Livewire kit pages against a baseline. It needs the playground on :8321.
  Take the baseline first with
  `node scripts/visual-snapshot.js <dir> http://127.0.0.1:8321 <pages…>`.

Packaging: anything a published package imports must be in its `files`
(`npm run consumer` catches a miss). The Svelte package must never import
from outside its own `dist` or another `@sigta-hq/*` package.

Build-order gotchas:
- After editing `packages/core/src` or `resources/js/*.js`, rebuild the
  Livewire runtime with `node scripts/build-livewire.js` (and
  `npm run build:editor` for the editor). `tests/RuntimeTest.php` checks the
  sources hash in its banner.
- svelte-check resolves `@sigta-hq/contract/*` and `@sigta-hq/core/editor`
  through the built packages. Build the contract and core first;
  `npm run check` does.
- The conformance harness runs Vite in middleware mode without a WebSocket
  (`scripts/sigta-vite.js`). Pages strip `/@vite/client`. A first-run
  "504 Outdated Optimize Dep" is Vite re-optimizing; rerun.
