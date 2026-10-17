---
title: Installation
order: 1
---

# Installation

<!-- @livewire -->
Sigta UI for Livewire is the Composer package `sigta/ui`. It is private, so
Composer needs to know where the repository is and how to authenticate.

## Requirements

```text
PHP ^8.4
Laravel ^12.0 | ^13.0
Livewire ^4.0
Tailwind CSS v4 (your app's build, via @tailwindcss/vite)
```

## 1. Point Composer at the repository

```json
{
    "require": {
        "sigta/ui": "dev-main"
    },
    "repositories": [{
        "type": "vcs",
        "url": "git@github.com:sigta-hq/ui.git"
    }]
}
```

Use `https://github.com/sigta-hq/ui.git` on CI and deploy runners, with a
token instead of an SSH key:

```sh
composer config --global github-oauth.github.com ghp_xxxxxxxxxxxxxxxxxxxx
# CI: provide it without committing auth.json
export COMPOSER_AUTH='{"github-oauth":{"github.com":"ghp_xxx"}}'
```

## 2. Require it

```sh
composer require sigta/ui:dev-main
```

## 3. Import the stylesheet

```css
/* resources/css/app.css */
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + the Sigta brand */
```

The component stylesheet ships prebuilt, so no `@source` line is needed for
Sigta's own views (an existing one is harmless).

## 4. Add the runtime to your layout

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@sigtaScripts   {{-- + @sigtaEditorScripts if you use <sigta:editor> --}}
…
<sigta:toast />
@livewireScripts
```

## 5. Sync fonts and the agent context

```sh
php artisan sigta:install
```

It mirrors the fonts to `public/vendor/sigta/fonts` and writes `sigta-ui.md`,
`sigta-components.md` and a line in `AGENTS.md` for LLM coding agents. It is
idempotent; keep it in sync on every install:

```json
{
    "scripts": {
        "post-autoload-dump": [
            "@php artisan package:discover --ansi",
            "@php artisan sigta:install --ansi"
        ]
    }
}
```
<!-- @end -->
<!-- @svelte -->
Sigta UI for Svelte is the npm package `@sigta-hq/svelte` (with
`@sigta-hq/core` and `@sigta-hq/contract`), published privately to GitHub
Packages. It needs no PHP and no particular backend: Vite + Svelte 5, with or
without SvelteKit or Inertia, in front of Laravel, Go or anything else.

## Requirements

```text
Svelte ^5.29
Vite (or SvelteKit)
Tailwind CSS v4 — optional, for the token utilities in your own markup
```

## 1. Authenticate with GitHub Packages

```ini
# .npmrc in your project
@sigta-hq:registry=https://npm.pkg.github.com
//npm.pkg.github.com/:_authToken=${NODE_AUTH_TOKEN}
```

`NODE_AUTH_TOKEN` is a GitHub token with `read:packages`. Access is granted
per repository: in each `@sigta-hq/*` package's settings on GitHub, give your
app's repository read access (Manage Actions access), or its CI cannot
install. In Docker or SSR image builds, pass the token as a build secret —
never as an `ARG` or `ENV`, which ends up in an image layer:

```dockerfile
RUN --mount=type=secret,id=npm_token NODE_AUTH_TOKEN=$(cat /run/secrets/npm_token) npm ci
```

## 2. Install

```sh
npm install @sigta-hq/svelte
```

## 3. Import the stylesheet

```css
/* app.css */
@import "tailwindcss";                      /* optional */
@import "@sigta-hq/contract/tailwind.css";  /* the token utilities: bg-surface, text-ink, font-mono … */
@import "@sigta-hq/svelte/sigta.css";       /* every component, the Sigta theme and fonts */
```

Without Tailwind, `import '@sigta-hq/svelte/sigta.css'` in your entry is
enough. The fonts are bundled by Vite from the package.

## 4. Mount the toast region once

```svelte
<!-- the root layout (+layout.svelte, or your Inertia layout) -->
<script>
    import { Toast } from '@sigta-hq/svelte';
    let { children } = $props();
</script>

{@render children()}
<Toast />
```

## 5. Sync the agent context

```sh
npx sigta install
```

It writes `sigta-ui.md`, `sigta-components.md` and a line in `AGENTS.md` for
LLM coding agents. Keep it in sync on every install by adding it to **your**
`package.json` (a dependency may not write into your project by itself):

```json
{ "scripts": { "postinstall": "sigta install" } }
```

## 6. Inertia (optional)

```js
// app.js — after createInertiaApp(…)
import { connectInertia } from '@sigta-hq/svelte/inertia';
connectInertia();
```

Any backend can now raise toasts and celebrations through a `sigta` page
prop. See [Svelte, Inertia and any backend](../svelte/).
<!-- @end -->
