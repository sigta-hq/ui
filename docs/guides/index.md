---
title: Sigta UI
order: 0
---

# One design system, two runtimes

Sigta UI carries the Sigta brand — paper, ink, and hand-drawn accents — into
every surface: landing pages and SaaS platforms, in **Livewire/Blade** or
**Svelte 5**. The look, the tokens, the variants, the parts, the accessibility
and the motion language are shared and strict; only how a component renders
and how its state flows differ.

Use the switch at the top to read every page — the installation, the API, the
live demos — for your runtime. Both render the same markup from the same
stylesheet, so a demo looks exactly the same either way.

<!-- @livewire -->
```blade
<sigta:button variant="cta" href="/book">Book a project <sigta:icon name="arrow-right" size="sm" /></sigta:button>
```
<!-- @end -->
<!-- @svelte -->
```svelte
<Button variant="cta" href="/book">Book a project <Icon name="arrow-right" size="sm" /></Button>
```
<!-- @end -->

- **[Installation](guides/installation/)** — wiring the package into an app.
- **[Theming](guides/theming/)** — tokens, re-branding, multiple brands.
- **[Motion](guides/motion/)** — loaders, completing things, celebrating.
<!-- @svelte -->
- **[Svelte, Inertia and any backend](guides/svelte/)** — state, events and the server boundary.
<!-- @end -->
- **[How it is built](guides/architecture/)** — the contract, the core and the adapters.
