# Sigta UI

[![Latest version](https://img.shields.io/packagist/v/sigta/ui.svg)](https://packagist.org/packages/sigta/ui)
[![CI](https://github.com/sigta-hq/ui/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/sigta-hq/ui/actions/workflows/ci.yml)
[![Downloads](https://img.shields.io/packagist/dt/sigta/ui.svg)](https://packagist.org/packages/sigta/ui)
[![License](https://img.shields.io/packagist/l/sigta/ui.svg)](LICENSE)

A Livewire/Blade component library with a paper-and-ink look: hand-drawn
accents, form controls, overlays, an app shell, a rich-text editor and
marketing sections, all themed through design tokens. See
[PLAN.md](PLAN.md) for architecture decisions and the component roadmap.

**Requirements:** PHP 8.4+, Laravel 12 or 13, Livewire 4, Tailwind CSS 4.

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

### Your own loader

`<sigta:loader>` draws the Sigta compass by default, and so does every busy
button, save state and job. To draw your own, publish the config and name a
Blade view:

```bash
php artisan vendor:publish --tag=sigta-config
```

```php
// config/sigta.php
'loader' => 'components.spinner',
```

The view is drawn inside the loader's box, which keeps the size, colour,
`data-state`, progress and accessible name. It receives `$state`, `$value`
(0–100 or null), `$size` and `$label`. State can change in the browser without
a re-render, so key motion off the box rather than off `$state`:

```blade
{{-- resources/views/components/spinner.blade.php --}}
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"
     class="group-data-[state=working]/loader:animate-spin">
    <circle cx="12" cy="12" r="9" class="opacity-20" />
    <path d="M21 12a9 9 0 0 0-9-9" />
</svg>
```

`<sigta:loader variant="compass">` or `variant="sketch"` still draws the built-in
loader wherever you ask for it by name.

## Development

```bash
composer install && ./vendor/bin/pest   # package tests
./scripts/check-tokens.sh               # token guard (no raw hex outside theme.css)

cd playground
composer install && npm install
npm run dev & php artisan serve         # kit pages at /kit
```

[CONTRIBUTING.md](CONTRIBUTING.md) has the full setup, the rules CI enforces
and what a finished change includes.

## Rules of the system

- Components reference design tokens only — raw hex outside the theme files
  (`resources/css/theme.css`, `theme.example.css`) fails CI.
- Token names are semantic roles (`--sigta-surface`, `--sigta-fg`); light
  values only for now, a dark theme is a future values file, not a refactor.
- Alpine.js is the behavior layer. Headless engines only for editor-class
  problems. Styled component libraries never.
- The package ships no content and no logo. Sections render only the copy
  you pass, and `<sigta:brand>` takes your logo through its `logo` slot or
  prop. Sigta's own landing copy lives in
  `playground/resources/content/sigta-landing.php`, and
  `tests/NoBrandContentTest.php` fails if Sigta content creeps back in.

## Contributing, support and security

- **Questions:** [Discussions](https://github.com/sigta-hq/ui/discussions). See
  [SUPPORT.md](SUPPORT.md).
- **Bugs and feature requests:** [issues](https://github.com/sigta-hq/ui/issues/new/choose).
- **Contributing:** [CONTRIBUTING.md](CONTRIBUTING.md). Everyone taking part
  follows the [code of conduct](CODE_OF_CONDUCT.md).
- **Security problems:** report privately, as described in
  [SECURITY.md](SECURITY.md).
- **Changes per version:** [releases](https://github.com/sigta-hq/ui/releases)
  and [CHANGELOG.md](CHANGELOG.md). Pushing a `v*` tag publishes the release;
  see the release steps in [CONTRIBUTING.md](CONTRIBUTING.md#releases-maintainers).

## License

Sigta UI is open-source software licensed under the [MIT license](LICENSE).

Two parts of the package come with their own terms:

- **Fonts** in `resources/fonts/` (Fraunces, Newsreader, Caveat, JetBrains
  Mono) are licensed under the [SIL Open Font License 1.1](resources/fonts/OFL.txt).
- **Bundled editor dependencies** in `dist/sigta-editor.js` (TipTap,
  ProseMirror and others) keep their own MIT licenses, listed in
  [`dist/sigta-editor.js.LICENSE.txt`](dist/sigta-editor.js.LICENSE.txt).

The MIT license covers the code, not the brand. The package ships no Sigta
logo or content, and the Sigta name and logo, which appear in the
`playground/` docs site, are trademarks of Sigta ApS and are not licensed for
use in your own products.
