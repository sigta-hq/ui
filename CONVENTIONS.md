# Sigta UI — Component Conventions

Contract for every component in this package, in both runtimes (Livewire/Blade
and Svelte 5). Read fully before building; [AUTHORING.md](AUTHORING.md) is the
step-by-step recipe and [CLAUDE.md](CLAUDE.md) the parity rule.

## Never name another component library

Sigta UI is a self-owned, native library. Do not reference Flux (or any other
component library) in docs, stubs, comments, component APIs, or commit messages
— not as a comparison, a migration table, or a lineage note. Describe everything
in Sigta's own terms. Naming another library in the published stubs confuses the
consumers (often LLM agents) about what this package is and how to use it.

## LLM context files (generated — never edit by hand)

Consumer apps — many of them built by LLM coding agents — discover Sigta UI's
API and rules from two published files per runtime, not by traversing this
repo:

- **`sigta-ui.md`** — the imperative rules an LLM agent follows (always use
  Sigta components, never raw HTML equivalents; tokens only, no hex; never
  import another icon library; foundation wiring).
- **`sigta-components.md`** — the exhaustive component reference (every
  component, props with defaults, variants, slots/snippets, events, notes,
  a usage example), grouped by the `catalog.php` tiers.

Both are **generated** by `npm run generate` from the manifests
(`packages/contract/src/manifests/*.ts`) and the templates in
`packages/contract/stubs/` (`rules.md`, `components.md`), in two editions:
`stubs/` (Livewire; written into a project by `php artisan sigta:install`) and
`packages/svelte/stubs/` (Svelte; written by `npx sigta install`). So:

- **A component, prop, default, variant, slot, event or usage note changes**
  → change the manifest; the reference follows.
- **A usage rule, token utility or the foundation wiring changes** → change
  `packages/contract/stubs/rules.md`. Runtime-specific lines go in
  `<!-- @livewire -->` / `<!-- @svelte -->` … `<!-- @end -->` blocks; write a
  component tag as `{{tag:name}}` and each edition spells it its own way.
- Then `npm run generate` and commit the output. `npm run generate:check`
  (part of `npm run check`) fails when the committed files are stale.

A drift between the stubs and the components silently teaches consumer LLMs
the wrong API, which is why nothing in them is written by hand.

## Where things go

| What | Where |
|---|---|
| Contract | `packages/contract/src/manifests/<name>.ts` |
| Look | `packages/contract/css/components/<name>.css` |
| Blade view | `resources/views/components/<name>.blade.php` (anonymous Blade component → `<x-sigta::<name>>`) |
| Alpine behavior | `Alpine.data('sigta<Name>', …)` in `resources/js/sigta.js`, bundled to `dist/sigta.js` by `node scripts/build-livewire.js` (never copied by hand) |
| Svelte component | `packages/svelte/src/lib/components/<Name>.svelte` (`Dropdown.Item.svelte` for `dropdown.item`) |
| Behavior both need | `packages/core/src/` (pure functions, motion, stores — framework-free, SSR-safe) |
| Tests | `tests/Components/<Name>Test.php` (Pest), `packages/*/test/` (Vitest), manifest examples (conformance) |
| Kit page | `playground/resources/views/kit/<name>.blade.php` — sidebar picks it up from `resources/catalog.php` |

## Token rules (CI-enforced)

**Never write a raw hex color** outside the theme layer (`packages/contract/css/theme.css`, `theme.example.css`; the files of the same name under `resources/css/` only forward to them) — `scripts/check-tokens.sh` fails the build. Inline SVGs use `currentColor` or `var(--sigta-*)`.

CSS layer map (sources in `packages/contract/css/`, built to `packages/contract/dist/css/` by `node packages/contract/scripts/build.ts`): `theme.css` = default Sigta brand values (swappable). `tailwind.css` = the `@theme inline` bridge that maps tokens to utilities. `base.css`, `prose.css`, `motion.css` = brand-agnostic base, content style and keyframes. `components/*.css` = the component look, compiled to plain CSS in `@layer components`. `core.css` = everything but the theme; `sigta.css` = `core.css` + preflight + fonts + `theme.css` (drop-in). Rebrands import `core.css` + their own theme; `theme.example.css` is the template. Themes may scope tokens under `[data-sigta-theme="x"]` for multi-brand apps.

Component stylesheets are written with `@apply` and these token utilities; a consumer's own templates use the same utilities:

- Colors: `bg-surface`, `bg-surface-raised`, `text-ink`, `text-graphite`, `border-line`, `bg-accent`, `border-accent-line`, `text-success`, `text-danger`, `bg-danger`, `bg-ink` (ink fills), `bg-inverse`, `text-inverse-fg`, `text-inverse-fg-muted`, `border-inverse-line`
- Fonts: `font-head` (Fraunces), `font-body` (Newsreader), `font-mono` (JetBrains Mono), `font-hand` (Caveat)
- Shape: `rounded-base` (3px — controls), `rounded-panel` (6px — cards), `rounded-frame` (10px — devices/spotlights), `shadow-paper`
- Raw vars where utilities don't exist: `max-w-(--sigta-maxw)`, `px-(--sigta-gutter)`, `var(--sigta-*)` in arbitrary values

## Brand design rules

Written as utilities, applied in the component stylesheets (`@apply`), never in
the adapters.

- UI chrome (labels, buttons, table headers, badges, nav) = `font-mono`, small sizes (0.62–0.82rem), letter-spacing 0.04–0.22em, often uppercase. Content = serif.
- Borders: strong outline = `border-[1.5px] border-ink`; hairline = `border border-line`.
- Hover on actionable things: `hover:-translate-y-[2px]` + subtle shadow/bg. Transitions 140–220ms ease.
- Press: a lifted thing settles back onto the paper while pressed (`active:translate-y-0 active:scale-[0.97] active:duration-[var(--sigta-dur-press,90ms)]`) and springs back (`ease-sigta-spring`).
- Motion language — ink on paper: waits draw (the `<sigta:loader>` compass, never a generic spinner or "Loading…" text), a finished thing is ticked, crossed out and folds away, a milestone gets a flourish (`$sigta.celebrate`). Keyframes live in the motion section of `core.css` and read the `--sigta-curve-*` / `--sigta-dur-*` tokens with the Sigta values as fallbacks.
- Motion decorates state, it never carries it: animate from the `data-state`, ARIA or text the markup already has, so a morph can drive it and reduced motion loses nothing.
- A loader never moves, and nothing moves around it. A component that pairs the compass with words or numbers keeps both rendered through every state: it changes `data-state`, stacks each alternative label in one grid cell and switches them with `visibility` (never `display`, `x-show` or `@if`), fixes the width of changing numbers, and offers `align="end"` when it can sit at a right edge. Test it by stepping through every state and checking the compass keeps its position.
- Accent (highlighter yellow) is scarce: one mark per view, status highlights only. Never large fills.
- Hand-drawn elements (rules, sparklines, chart lines) are inline SVG with `currentColor`, slight curve wobble, `stroke-linecap="round"`.
- Numbers in data contexts: `tabular-nums`.
- Disabled: `opacity-40 pointer-events-none` (via `disabled:`/`aria-disabled:` variants).

## Component API

- Names are mirrored across runtimes: `<sigta:dropdown.item>` ↔ `<Dropdown.Item>`, prop `icon:trailing` ↔ `iconTrailing`, slot `footer` ↔ snippet `footer`, default slot ↔ `children`. Prop names follow one vocabulary: `variant`, `size`, `icon`, `label`, `description`, `error`, `placeholder`.
- Consumers may write either `<sigta:button>` (compiled prefix) or `<x-sigta::button>`. Author component *views* and *tests* in the canonical `<x-sigta::…>` form; the prefix is a global rewrite, not something each component opts into.
- `@props` / `$props()` with the manifest's defaults (the contract tests compare all three).
- Root element carries `data-sigta="<name>"`; inner elements that the stylesheet reads carry `data-scope="<name>" data-part="<part>"`; props the look depends on are `data-variant`, `data-size`, `data-tone`…; conditional looks are ARIA or `data-*` state attributes. Adapters never set design classes.
- Always forward attributes: `{{ $attributes->merge([...contract attributes]) }}` in Blade, `{...rest}` in Svelte. Never swallow `wire:*`, `x-*`, `id`, `name`, `aria-*`. A consumer's `class` lands on the root and wins over the component layer.
- Form controls: attributes (incl. `wire:model`) forward onto the **native element** (`input`, `select`, `textarea`, `button`). Native elements first, always, unless impossible. Svelte exposes the value as `bind:value`.
- Slots for composition; named slots where a component has distinct regions. Svelte: snippets of the same name, with parameters where Blade passes data.
- Events: Blade dispatches DOM/window events (and Livewire hears them); Svelte takes callback props (`onchange`, `onmove`) and dispatches the same DOM events where others rely on them.

## Accessibility (non-negotiable)

- Keyboard-complete per the WAI-ARIA APG pattern for the role.
- Focus ring comes from the global `:focus-visible` style — do not suppress outlines; add a `:focus-visible` rule in the component stylesheet only when the global ring can't apply.
- Labels: form controls get real `<label for>` wiring (ids: `$attributes->get('id') ?? 'sigta-'.Str::random(8)` in Blade, `id ?? \`sigta-${$props.id()}\`` in Svelte).
- Decorative SVGs: `aria-hidden="true"`. Meaningful icons need `aria-label` passthrough.
- Respect `prefers-reduced-motion` (global CSS already kills transitions — don't rely on animation for meaning). Script-driven animations (`el.animate()`, canvas) are not stopped by that rule: check `prefers-reduced-motion` in the script and skip the flourish, never the work.

## Livewire integration

- Components are Blade + Alpine, never Livewire class components.
- Must survive Livewire DOM morphs: keep state in Alpine (`x-data`), avoid imperative DOM mutations, use `wire:ignore` only as last resort.
- Event-driven components (Toast) listen for Livewire dispatches via `Livewire.on(...)` or window events.
- Never interpolate state into `x-data` (`x-data="thing(@js($value))"`). A Livewire morph that changes the attribute string makes Alpine re-run `x-data`, rebuilding the component mid-interaction. Read initial values from the DOM instead (a `data-*` attribute, the hidden input's value) with a constant `x-data="thing()"`.
- Declare every field an Alpine component writes in the object it returns (`timer: null`, `observer: null`). Alpine stores an undeclared `this.x = …` on the nearest ancestor scope that has an `x`, so a component nested in another one overwrites the host's state: a save state inside a `<sigta:modal>` once set the modal's `name` to its own and the modal never opened. `tests/RuntimeTest.php` checks it.
- Keep third-party instances (TipTap, charts) out of Alpine data. Anything read back through the reactive proxy comes wrapped, and proxied ProseMirror objects fail their own identity checks ("Applying a mismatched transaction"). Keep them in a module-level `WeakMap` keyed by `$root` behind a method (`instance()`), which the proxy cannot wrap.
- A DOM subtree a library owns needs `wire:ignore` and a stable id. Livewire keys its morph on `wire:key` and `id`, so the usual random per-render id makes the morph swap the element for an empty one on the first re-render; derive the id from the binding (`name`, `wire:model`) when none is given.
- Livewire 4 note: this package targets `livewire/livewire:^4.0`. It forwards `wire:model` (and any `wire:*` attribute) onto native inputs verbatim — it never authors `wire:model` directives, so the package is unaffected by v4's modifier changes. Host apps should know that under v4, `wire:model.blur` / `wire:model.change` must be written `wire:model.live.blur` / `wire:model.live.change` to get the v3 "sync on blur/change" behavior.

## Svelte integration

- Svelte 5 runes only: `$props()`, `$state`, `$derived`, `$effect`, `$bindable`; ids from `$props.id()` so server and client agree.
- SSR-safe: nothing touches `window` or `document` at module level or during render; page-wide behavior comes from `ensureRuntime()` in `onMount`.
- Compound components share state through context (`runtime/*.svelte.ts`), the twin of an Alpine component's shared scope.
- Keep third-party instances (TipTap) in plain variables, not `$state`.
- Sigta's own strings go through `t('English text')` (`const t = translator()` from `runtime/messages.ts`) wherever Blade uses `__('English text')`: the same keys, so one `lang/<locale>.json` translates both runtimes (`setMessages(map)` in the Svelte root layout). A test holds the two lists equal.
- `@sigta-hq/svelte` never depends on a backend. Server-driven toasts and celebrations come through the optional `@sigta-hq/svelte/inertia` and the neutral `sigta` page prop.

## Definition of done (per component)

1. Manifest, stylesheet, Blade view (+ Alpine behavior if interactive) and Svelte component — together, never one runtime alone
2. Manifest examples for every variant and state (with `interactions` for behavior) — they are the docs and the conformance fixtures
3. Pest tests: renders content, each variant/state asserted, attribute forwarding verified; Vitest for runtime logic
4. Kit page covering every variant and state (use `<x-kit-shell title="...">` + `<x-kit-demo label="...">`)
5. `npm run generate` run and its output committed (stubs, manifest JSON, Svelte index)
6. `npm run check` green (build, generate --check, Vitest, svelte-check, conformance in server and browser, Pest, token guard)
7. The Livewire look unchanged unless intended (`scripts/visual-check.sh` against a baseline); `cd playground && php artisan view:clear && php artisan test` green
