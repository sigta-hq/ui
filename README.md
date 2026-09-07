# Sigta UI

Livewire/Blade component library carrying the Sigta brand — paper, ink, and
hand-drawn accents — as the shared foundation for Sigta landing pages and SaaS
platforms. Comprehensive component coverage, fully owned. See [PLAN.md](PLAN.md)
for architecture decisions and the component roadmap.

## Repo layout

- Package (`sigta/ui`) lives at the repo root.
- `playground/` is a Laravel app consuming the package via a path repository —
  it doubles as living docs, kit pages, and the visual regression surface.

## Installing in an app

```bash
composer require sigta/ui
```

App CSS entry (requires Tailwind v4):

```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + Sigta brand */
@source "../../vendor/sigta/ui/resources/views";
```

Layout: add `@sigtaScripts` before `</head>` (and `@sigtaEditorScripts` too if
you use `<sigta:editor>`). Components are then available as `<sigta:button>` /
`<x-sigta::button>` — both forms compile identically.

## LLM agent support

If your app is built or edited by an LLM coding agent (ZCode, Claude Code,
Cursor, etc.), run one command after `composer require` to publish the agent
context and keep it in sync:

```bash
php artisan sigta:install
```

This is **idempotent** — re-running is always safe and brings your project fully
in sync with the installed package version. It:

- **Mirrors fonts** `resources/fonts/` → `public/vendor/sigta/fonts` (copies new,
  overwrites changed, prunes stale)
- **Writes `sigta-ui.md`** to the project root — the imperative rules an LLM agent
  follows (always use `<sigta:…>` components, never raw HTML equivalents; tokens
  only, no hex; never import another icon library)
- **Writes `sigta-components.md`** to the project root — the exhaustive component
  reference (every component, props, variants) so the LLM doesn't traverse code
- **Adds one reference line to `AGENTS.md`** (creating it if absent, appending if
  present) pointing the agent at those two files

### Auto-sync on every composer update

Because `sigta:install` is idempotent, you can wire it into your `composer.json`
so every `composer install` / `composer update` re-syncs the context automatically:

```json
"scripts": {
    "post-autoload-dump": [
        "@php artisan package:discover --ansi",
        "@php artisan sigta:install --ansi"
    ]
}
```

Now an LLM dropped into your project reads `AGENTS.md` → follows the pointer to
`sigta-ui.md` → reads the rules → scans `sigta-components.md` for the API →
writes `<sigta:button>` instead of `<button class="bg-blue-600">`.

> **Keeping the stubs accurate:** `stubs/sigta-ui.md` and
> `stubs/sigta-components.md` are the single source of truth the agent sees.
> Update them whenever a component, prop, or rule changes — see `CONVENTIONS.md`.

## Re-branding

Every component reads semantic `var(--sigta-*)` tokens — no hardcoded colors.
To carry a different brand, import `core.css` (structure) instead of `sigta.css`
and supply your own theme:

```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/core.css";
@import "./my-brand.css";   /* your :root{--sigta-*} + @font-face */
@source "../../vendor/sigta/ui/resources/views";
```

Copy `resources/css/theme.example.css` as a starting template — it documents
every token. Scope a theme under `[data-sigta-theme="x"]` to run multiple
brands in one app.

## Development

```bash
composer install && ./vendor/bin/pest   # package tests
./scripts/check-tokens.sh               # token guard (no raw hex outside tokens.css)

cd playground
composer install && npm install
npm run dev & php artisan serve         # kit pages at /kit
```

## Rules of the system

- Components reference design tokens only — raw hex outside
  `resources/css/tokens.css` fails CI.
- Token names are semantic roles (`--sigta-surface`, `--sigta-fg`); light
  values only for now, a dark theme is a future values file, not a refactor.
- Alpine.js is the behavior layer. Headless engines only for editor-class
  problems. Styled component libraries never.
