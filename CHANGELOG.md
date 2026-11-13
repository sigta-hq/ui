# Changelog

Release notes for each version live on
[GitHub Releases](https://github.com/sigta-hq/ui/releases), generated from the
pull requests merged since the previous tag. Versions follow
[semantic versioning](https://semver.org); before 1.0, a minor release may
contain breaking changes.

## 0.1.0 — 2026-11-13

The first public release, under the MIT license.

- Livewire/Blade component library: elements, form controls, overlays, app
  shell, kanban, charts, a TipTap editor and marketing sections, with
  `<sigta:…>` and `<x-sigta::…>` tags.
- Theming through `var(--sigta-*)` tokens: import `sigta.css` for the default
  theme, or `core.css` with your own.
- `php artisan sigta:install` publishes fonts and the agent docs for coding
  assistants.
- Sections and `<sigta:brand>` ship without content or logo; pass your own.
- `config('sigta.loader')` swaps the compass loader for your own view.
