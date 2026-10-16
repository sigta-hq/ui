# Plan: Sigta UI — one design system, pluggable runtimes

## Goal

Sigta UI becomes one strict design system with interchangeable runtimes: the
look, tokens, variants, parts, accessibility and motion language are
single-sourced, while the rendering and interactivity layer is an adapter.
Livewire/Blade (today's) is one adapter, Svelte 5 is the second, and a Svelte
app built on Sigta looks and behaves indistinguishably from a Livewire one.

## Approach

The design leaves the Blade templates. Every component's look lives in one
shared, prebuilt stylesheet keyed on a DOM contract (`data-sigta`,
`data-part`, `data-variant`, `data-size`, state attributes and ARIA); adapters
only render structure and set those attributes, so neither can change a
colour or a padding. Each component's contract (props, variants, parts,
states, slots, events, bindings, ARIA pattern, notes and examples) is a typed
manifest, compiled to committed JSON, from which the Svelte prop types, both
runtimes' LLM stubs, the docs site and the conformance fixtures are
generated. Behavior is split by kind: the brand-signature, DOM-level behavior
(motion, toasts, celebrate, count, positioning, focus management) is one
SSR-safe vanilla core both runtimes attach; widget state is native per runtime
on shared pure logic; runtime glue (Livewire morph hooks, Inertia router
state) stays in each adapter. Everything ships from one monorepo in lockstep:
the Composer package at the root, npm packages under `packages/`, published
privately to GitHub Packages.

## Key decisions made

- **Where the design lives:** CSS + DOM contract. Rationale: strictness by
  construction — an adapter physically cannot set a colour or a padding, and
  both runtimes ship byte-identical component CSS.
- **"Compatible" for Livewire consumers:** tags, props, slots, `data-sigta`,
  `wire:*` forwarding and the visual result. Rendered utility classes are not
  part of the API and become contract attributes.
- **Cascade:** component CSS sits in `@layer components`, below Tailwind's
  `utilities`, so a consumer's `class=""` override always wins.
- **How much behavior is single-sourced:** split by kind. Signature
  behavior is one SSR-safe vanilla core (`@sigta-hq/core`); widget state is
  native per runtime on shared pure logic; glue stays in adapters. Rationale:
  the feel cannot drift, and widget internals stay idiomatic.
- **Core rules:** no browser API touched at import time; no module-level
  singletons on a server (stores are created client-side); motion is exported
  as keyframe + timing definitions that Alpine plays imperatively and Svelte
  wraps as transitions. `window.Sigta`, `$sigta.*` and the `sigta-toast`,
  `sigta-celebrate`, `sigta-save-state` window events are a compatibility
  contract.
- **What "the contract" is physically:** a machine-readable manifest per
  component (`packages/contract/src/manifests/*.ts` → `packages/contract/manifest.json`, committed).
  Neutral concepts, mapped per runtime. Free-form notes and examples per
  runtime so generated stubs keep their nuance. Rationale: drift between
  stubs, adapters and CSS becomes structurally impossible.
- **Svelte API shape:** mirror the vocabulary, idiomatic mechanics. Same
  component names and nesting, prop names, variant values, part names, slot
  names → snippet names. State flows natively: Svelte gets `bind:value` /
  `bind:open`, callback props (`onmove`, `onsubmit`), snippets with
  parameters; Livewire keeps `wire:model`, window events and `$dispatch`.
- **Server boundary on Svelte:** a documented, backend-agnostic Inertia props
  shape (`sigta` key carrying toasts and celebrations) that any backend —
  Laravel, Go, anything — can emit, plus router-driven busy/save-state, in an
  optional `@sigta-hq/svelte/inertia` subpath. Plain Svelte without Inertia
  works.
- **Migration order and scope:** pilot slice (button, field + input, modal,
  autocomplete, toast, a motion list) proves the pipeline end to end in both
  runtimes, then vertical slices through the catalog tiers to full parity.
- **Parity merge gate:** from now on no component change lands in one runtime
  only — manifest, CSS, Blade, Svelte and conformance move together.
- **PHP leaks:** neutral data shapes in the manifest (pagination object, ISO
  date strings, label props with English defaults). Blade keeps accepting the
  PHP types and normalizes. One pinned icon source rendered by both runtimes.
- **Repo and distribution:** one monorepo; Composer package stays at the root
  and keeps shipping prebuilt `dist/` assets (no Node for Composer consumers);
  npm workspaces in `packages/`; lockstep versions from one tag.
- **npm registry:** private GitHub Packages, `@sigta-hq/*` scope.
- **Install tooling:** `sigta:install` (Composer) gets a Node twin,
  `npx sigta install` (bin in `@sigta-hq/svelte`), which consumers wire into
  their own `postinstall`. Fonts bundle through Vite from the package CSS.
- **Docs:** one static docs site generated from the manifests, with a
  site-wide runtime toggle (only one runtime's demos and syntax load at a
  time). Guides stay hand-written markdown. The Laravel playground and a
  Svelte harness become integration/acceptance harnesses.
- **Examples are data:** most examples are authored once as a neutral tree and
  rendered to Blade and Svelte syntax; the same examples are the conformance
  fixtures and pixel-diff cases. Binding-heavy examples get per-runtime
  snippets.

## Trade-offs accepted

- Markup is written twice (Blade + Svelte); conformance tests are the guard.
- Every component change costs both runtimes, permanently.
- Livewire consumers that targeted rendered utility classes (browser tests,
  custom selectors) must move to the contract attributes.
- The core gains a real build step; `dist/sigta.js` is no longer a verbatim
  copy of a source file.
- Renaming a part or an attribute is a breaking change in both runtimes.
- GitHub Packages needs per-repo, per-package access grants and build secrets.
- Server-driven demos (Livewire save-state, server toasts) cannot be live on a
  static docs site.
- Hand-written kit pages are replaced by generated pages + guides.

## Sequencing

1. Foundation: monorepo workspaces, manifest schema + generators, CSS build,
   core extraction, Svelte package skeleton, conformance runner.
2. Pilot slice in both runtimes, end to end.
3. Vertical slices by catalog tier to full parity: elements → components →
   layout → sections → heavy widgets (editor, kanban, file upload, pickers).
4. Generated stubs for both runtimes, `npx sigta install`.
5. Generated docs site with runtime toggle; harnesses and composite pages.
6. CI: conformance, pixel diff, parity gate, publishing.

---

## The plan itself

### 1. Repository layout

```
/                         sigta/ui (Composer, unchanged install) + npm workspace root
├── composer.json
├── package.json          workspaces: packages/*, docs, harness/svelte
├── src/                  PHP: service provider, Sigta facade, sigta:install
├── resources/
│   ├── views/components/ Livewire adapter — Blade components emitting the contract
│   ├── js/sigta.js       Livewire adapter runtime: Alpine.data + Livewire glue on @sigta-hq/core
│   ├── js/editor.js      Livewire editor glue
│   └── css/              stable Composer import paths (sigta.css, core.css, theme.css, …)
├── dist/                 committed builds Composer ships (sigta.js IIFE, sigta-editor.js, components CSS)
├── packages/
│   ├── contract/         @sigta-hq/contract — manifests, component CSS, tokens/theme, fonts, icons, generators
│   ├── core/             @sigta-hq/core — framework-free, SSR-safe behavior
│   └── svelte/           @sigta-hq/svelte — Svelte 5 adapter (+ /inertia, bin: sigta)
├── docs/                 generated static docs site (runtime toggle)
├── harness/svelte/       Svelte harness: conformance renders + composite acceptance pages
├── playground/           Laravel harness: Livewire integration + composite acceptance pages
└── tests/                Pest (Blade adapter) + conformance
```

### 2. The contract (per component)

- **Manifest** (`packages/contract/src/manifests/<name>.ts`): name, tier,
  description, props (type, default, values, bindable, runtime names),
  parts (`data-part` names), states (attribute + values), slots/snippets,
  events → callbacks, ARIA pattern, runtime availability, notes per runtime,
  examples (neutral trees or per-runtime snippets).
- **CSS** (`packages/contract/css/components/<name>.css`): Tailwind-authored,
  keyed only on contract selectors, compiled to plain CSS in
  `@layer components`, self-contained (depends only on `--sigta-*` tokens).
- **Blade** (`resources/views/components/<name>.blade.php`): same `@props`
  as the manifest (tested), emits the contract, keeps attribute and `wire:*`
  forwarding onto native elements.
- **Svelte** (`packages/svelte/src/lib/components/...`): same vocabulary,
  emits the same contract, state via runes/`bind:`/callbacks.
- **Conformance**: every manifest example rendered by both adapters,
  normalized and compared (tags, contract attributes, ARIA, text).

### 3. Core (`@sigta-hq/core`)

- Motion: tokens/timing helpers, burst, ring, note, confetti, celebrate,
  fold/unfold/leave/revive, complete, loader landing, count bump, shake,
  exported as definitions plus imperative players.
- Toast store (created client-side), save-state bus, busy-button timing.
- Floating positioning, focus trap, roving tabindex, dismiss-on-outside.
- Pure logic: combobox filtering/activation, calendar grid, date/time/
  duration/money parsing and formatting, keyboard maps.
- `installGlobals()` provides `window.Sigta` and the window-event contract.

### 4. Adapters

- **Livewire**: `resources/js/sigta.js` registers `Alpine.data` components on
  the core plus Livewire glue (morph hooks for motion lists, save-state
  message interception, busy buttons via `data-loading`, `Livewire.dispatch`
  for toast actions). Bundled to `dist/sigta.js` (IIFE) and committed.
- **Svelte**: components on the core via attachments and actions; motion lists
  use keyed `{#each}` + the core's `out:` transitions; `@sigta-hq/svelte/inertia`
  maps the `sigta` page props to toasts/celebrations and router events to
  busy/save-state.

### 5. Pilot slice

button, field + input, modal, autocomplete, toast, motion list
(complete → fold). Exit criteria: manifest + CSS + Blade + Svelte +
conformance green; Pest suite green; Svelte unit tests green.

### 6. Vertical slices (catalog order)

Elements → components → layout → sections → heavy widgets. Each component:
manifest, CSS extraction, Blade migration, Svelte component, examples,
conformance, Pest/Vitest updates, stub regeneration.

Heavy redesigns on Svelte: file upload (`File` objects handed to the form or
Inertia), editor (TipTap as an ESM dependency), kanban (callbacks for
move/column-move), pickers (ISO strings).

### 7. Tooling

- `npm run build`: contract CSS → `dist/` + `packages/contract/dist`; core;
  Livewire runtime IIFE; editor bundle; Svelte package.
- `npm run generate`: manifest JSON, stubs (both runtimes), docs data.
- `npm run conformance`: Blade renders (PHP) vs Svelte SSR renders (Node).
- `npx sigta install`: fonts are bundled by Vite; writes `sigta-ui.md`,
  `sigta-components.md` (Svelte edition) and the `AGENTS.md` line.

### 8. Docs

Static site from manifests; runtime toggle stored per visitor; one runtime
loaded per page view; hand-written guides (installation incl. GitHub Packages
and Go/Inertia setup, theming, motion, wiring the feel in Svelte, the Inertia
props contract).

---

## Open questions / deferred decisions

- **Icon tree-shaking on Svelte:** v1 ships the full pinned icon map; a
  per-icon import path or a Vite plugin can come when bundle size demands it.
- **Open-sourcing:** private GitHub Packages for now; publishing publicly is a
  republish, not a redesign.
- **A third runtime:** the manifest + CSS + core are runtime-neutral; a new
  adapter is a new package that passes conformance.
