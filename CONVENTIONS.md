# Sigta UI — Component Conventions

Contract for every component in this package. Read fully before building.

## LLM context files (keep in sync — non-negotiable)

Consumer apps — many of them built by LLM coding agents — discover Sigta UI's
API and rules from two published files, not by traversing this repo:

- **`stubs/sigta-ui.md`** — the imperative rules an LLM agent follows (always use
  `<sigta:…>` components, never raw HTML equivalents; tokens only, no hex; never
  import another icon library; foundation wiring).
- **`stubs/sigta-components.md`** — the exhaustive component reference (every
  component, props with defaults, variants, one-line description, grouped by the
  `catalog.php` tiers).

`php artisan sigta:install` in a consumer project **force-overwrites** both into
the project root (plus a reference line in `AGENTS.md`). So the stubs are the
single source of truth the LLM sees. They MUST stay accurate:

- **When you add, remove, or rename a component, or change any `@props` defaults
  or variants** → update `stubs/sigta-components.md` in the same change.
- **When you change a usage rule, token utility, or the foundation wiring** →
  update `stubs/sigta-ui.md`.
- A drift between the stubs and the actual components silently teaches consumer
  LLMs the wrong API. Treat stub edits as part of the component's Definition of
  done (section below), not an afterthought.

The stubs reference `catalog.php` for grouping — keep tier membership consistent
across both files.

## Where things go

- Component view: `resources/views/components/<name>.blade.php` (anonymous Blade component → `<x-sigta::<name>>`)
- Tests: `tests/Components/<Name>Test.php` (Pest, render via `Blade::render(...)`)
- Kit page: `playground/resources/views/kit/<name>.blade.php` — sidebar picks it up automatically
- Alpine behavior (if any): registered in `resources/js/sigta.js` inside the existing `alpine:init` listener as `Alpine.data('sigta<Name>', ...)`, then copy the file verbatim to `dist/sigta.js` (no build step yet)

## Token rules (CI-enforced)

**Never write a raw hex color** outside the theme layer (`resources/css/theme.css`, `theme.example.css`) — `scripts/check-tokens.sh` fails the build. Inline SVGs use `currentColor` or `var(--sigta-*)`.

CSS layer map: `theme.css` = default Sigta brand values (swappable). `core.css` = brand-agnostic Tailwind bridge + base + prose (references `--sigta-*` only). `sigta.css` = `core.css` + `theme.css` (drop-in). Rebrands import `core.css` + their own theme; `theme.example.css` is the template. Themes may scope tokens under `[data-sigta-theme="x"]` for multi-brand apps.

Tailwind utilities mapped to tokens (use these):

- Colors: `bg-surface`, `bg-surface-raised`, `text-ink`, `text-graphite`, `border-line`, `bg-accent`, `border-accent-line`, `text-success`, `text-danger`, `bg-danger`, `bg-ink` (ink fills), `bg-inverse`, `text-inverse-fg`, `text-inverse-fg-muted`, `border-inverse-line`
- Fonts: `font-head` (Fraunces), `font-body` (Newsreader), `font-mono` (JetBrains Mono), `font-hand` (Caveat)
- Shape: `rounded-base` (3px — controls), `rounded-panel` (6px — cards), `rounded-frame` (10px — devices/spotlights), `shadow-paper`
- Raw vars where utilities don't exist: `max-w-(--sigta-maxw)`, `px-(--sigta-gutter)`, `var(--sigta-*)` in arbitrary values

## Brand design rules

- UI chrome (labels, buttons, table headers, badges, nav) = `font-mono`, small sizes (0.62–0.82rem), letter-spacing 0.04–0.22em, often uppercase. Content = serif.
- Borders: strong outline = `border-[1.5px] border-ink`; hairline = `border border-line`.
- Hover on actionable things: `hover:-translate-y-[2px]` + subtle shadow/bg. Transitions 140–220ms ease.
- Accent (highlighter yellow) is scarce: one mark per view, status highlights only. Never large fills.
- Hand-drawn elements (rules, sparklines, chart lines) are inline SVG with `currentColor`, slight curve wobble, `stroke-linecap="round"`.
- Numbers in data contexts: `tabular-nums`.
- Disabled: `opacity-40 pointer-events-none` (via `disabled:`/`aria-disabled:` variants).

## Component API

- Consumers may write either `<sigta:button>` (compiled prefix) or `<x-sigta::button>`. Author component *views* and *tests* in the canonical `<x-sigta::…>` form; the prefix is a global rewrite, not something each component opts into.
- `@props` with defaults; prop names mirror Flux: `variant`, `size`, `icon`, `label`, `description`, `error`, `placeholder`.
- Root element carries `data-sigta="<name>"`.
- Always forward attributes: `{{ $attributes->merge(['class' => $classes]) }}`. Never swallow `wire:*`, `x-*`, `id`, `name`, `aria-*`.
- Form controls: attributes (incl. `wire:model`) forward onto the **native element** (`input`, `select`, `textarea`, `button`). Native elements first, always, unless impossible.
- Variants via `match` expressions in `@php` blocks — see `button.blade.php` for the house style.
- Slots for composition; named slots where Flux has them.

## Accessibility (non-negotiable)

- Keyboard-complete per the WAI-ARIA APG pattern for the role.
- Focus ring comes from the global `:focus-visible` style — do not suppress outlines; add `focus-visible:` utilities only when the global ring can't apply.
- Labels: form controls get real `<label for>` wiring (generate ids with `$id = $attributes->get('id') ?? 'sigta-'.Str::random(8);`).
- Decorative SVGs: `aria-hidden="true"`. Meaningful icons need `aria-label` passthrough.
- Respect `prefers-reduced-motion` (global CSS already kills transitions — don't rely on animation for meaning).

## Livewire integration

- Components are Blade + Alpine, never Livewire class components.
- Must survive Livewire DOM morphs: keep state in Alpine (`x-data`), avoid imperative DOM mutations, use `wire:ignore` only as last resort.
- Event-driven components (Toast) listen for Livewire dispatches via `Livewire.on(...)` or window events.
- Livewire 4 note: this package targets `livewire/livewire:^4.0`. It forwards `wire:model` (and any `wire:*` attribute) onto native inputs verbatim — it never authors `wire:model` directives, so the package is unaffected by v4's modifier changes. Host apps should know that under v4, `wire:model.blur` / `wire:model.change` must be written `wire:model.live.blur` / `wire:model.live.change` to get the v3 "sync on blur/change" behavior.

## Definition of done (per component)

1. Component view (+ Alpine behavior if interactive)
2. Pest tests: renders content, each variant/state asserted, attribute forwarding verified
3. Kit page covering every variant and state (use `<x-kit-shell title="...">` + `<x-kit-demo label="...">`)
4. `stubs/sigta-components.md` updated (and `stubs/sigta-ui.md` if a rule changed) — see "LLM context files" above
5. `./vendor/bin/pest` green, `./scripts/check-tokens.sh` clean
6. `cd playground && php artisan view:clear && php artisan test` green
