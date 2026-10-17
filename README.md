# Sigta UI

One design system — paper, ink and hand-drawn accents — with two runtimes:

- **Livewire/Blade**: the Composer package `sigta/ui` (`<sigta:button>`).
- **Svelte 5**: the npm package `@sigta-hq/svelte` (`<Button>`), backend
  agnostic, with optional Inertia support.

The look, tokens, variants, parts, accessibility and motion language are
shared and strict: one prebuilt stylesheet keyed on `data-sigta` / `data-part`
attributes, one manifest per component, one framework-free behavior core.
Only rendering and state flow differ per runtime. An app picks one runtime.
Every component exists in both, and a conformance check renders each example
in both runtimes and requires the same DOM, pixels and behavior.

The decisions behind this are in [RUNTIMES-PLAN.md](RUNTIMES-PLAN.md).

## Repo layout

| Path | What |
|---|---|
| `/` | The Composer package `sigta/ui`: Blade views (`resources/views`), the Livewire runtime (`resources/js/sigta.js` → `dist/sigta.js`), PHP (`src/`), Pest tests |
| `packages/contract` | `@sigta-hq/contract`: manifests, the component stylesheets, tokens, fonts, icons |
| `packages/core` | `@sigta-hq/core`: shared behavior — motion, toasts, save states, positioning, formatting, widget logic |
| `packages/svelte` | `@sigta-hq/svelte`: the Svelte components, `@sigta-hq/svelte/inertia`, the `sigta install` CLI |
| `playground/` | A Laravel app consuming the package: kit pages (`/kit`), docs, the visual-regression surface |
| `docs/` | The generated docs site, a Livewire and a Svelte edition with a runtime toggle |
| `harness/` | Conformance harness and whole-page acceptance pairs |

## Installing

The full steps for both runtimes are in
[docs/guides/installation.md](docs/guides/installation.md). In short:

**Livewire**

```bash
composer require sigta/ui
php artisan sigta:install    # fonts + LLM agent context; idempotent
```

```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + Sigta brand */
```

Add `@sigtaScripts` before `</head>` (and `@sigtaEditorScripts` if you use
`<sigta:editor>`). Components are `<sigta:button>` / `<x-sigta::button>`. The
component styles ship prebuilt, so no `@source` line for Sigta's views is
needed (an existing one is harmless).

**Svelte** (GitHub Packages — `.npmrc` with `@sigta-hq:registry=https://npm.pkg.github.com`)

```bash
npm install @sigta-hq/svelte
npx sigta install            # LLM agent context; idempotent
```

```css
@import "tailwindcss";                      /* optional */
@import "@sigta-hq/contract/tailwind.css";  /* token utilities for your own markup */
@import "@sigta-hq/svelte/sigta.css";       /* every component, the theme, the fonts */
```

```svelte
<script>
    import { Button, Toast } from '@sigta-hq/svelte';
</script>

<Button variant="primary" onclick={save}>Save</Button>
<Toast />
```

With Inertia, call `connectInertia()` from `@sigta-hq/svelte/inertia` and any
backend (Laravel, Go, …) can raise toasts and celebrations through a neutral
`sigta` page prop.

## LLM agent support

Apps built or edited by LLM coding agents get two context files in the
project root plus a pointer line in `AGENTS.md`: `sigta-ui.md` (the rules —
always use Sigta components, tokens only, no other icon set) and
`sigta-components.md` (every component, its props, slots, events and a usage
example). `php artisan sigta:install` writes the Livewire edition and
`npx sigta install` the Svelte edition. Both are idempotent and safe to wire into
`post-autoload-dump` / `postinstall`.

The files are generated from the component manifests (`npm run generate`), so
they cannot drift from the components.

## Re-branding

Every component reads semantic `var(--sigta-*)` tokens — no hardcoded colors.
To carry a different brand, import `core.css` (structure) instead of
`sigta.css` and supply your own theme:

```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/core.css";   /* Svelte: @sigta-hq/contract/core.css */
@import "./my-brand.css";   /* your :root{--sigta-*} + @font-face */
```

Copy `theme.example.css` (`packages/contract/css/`) as a starting template — it
documents every token. Scope a theme under `[data-sigta-theme="x"]` to run
multiple brands in one app.

## Development

```bash
npm install && composer install
npm run build          # contract CSS, core, Livewire runtime, editor bundle, Svelte package
npm run generate       # manifest JSON, Svelte index, LLM stubs for both runtimes
npm run check          # the merge gate: build, generate --check, Vitest, svelte-check,
                       # conformance (server + browser), Pest, token guard
npm run docs           # the docs site → docs/dist
npm run consumer       # install the packed npm packages into a fresh Svelte app and drive it

cd playground
composer install && npm install
npm run dev & php artisan serve         # kit pages at /kit
```

How to change or add a component — in both runtimes, in one change — is in
[CLAUDE.md](CLAUDE.md), [CONVENTIONS.md](CONVENTIONS.md) and
[AUTHORING.md](AUTHORING.md).

## Rules of the system

- A component changes in both runtimes at once: manifest, stylesheet, Blade,
  Svelte, tests and generated files in the same change.
- Adapters never style. The look lives in the contract stylesheet; a
  consumer's `class` is layered on top and wins.
- Components reference design tokens only — raw hex outside the theme files
  fails CI.
- Token names are semantic roles (`--sigta-surface`, `--sigta-fg`); light
  values only for now, a dark theme is a future values file, not a refactor.
- Behavior shared by both runtimes lives once in `@sigta-hq/core`; widget
  state is native per runtime (Alpine, runes). Headless engines only for
  editor-class problems. Styled component libraries never.
