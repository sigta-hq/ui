---
title: How it is built
order: 5
---

# How it is built

Sigta UI is one contract and two adapters.

- **The contract** (`@sigta-hq/contract`, `packages/contract`) — a typed
  manifest per component (props, defaults, variants, parts, states, slots,
  events, ARIA, notes, examples) and the component stylesheet, keyed on
  `data-sigta`, `data-part`, `data-variant` and state attributes, compiled
  into one plain CSS file in `@layer components`. Both runtimes ship the same
  bytes.
- **The core** (`@sigta-hq/core`, `packages/core`) — framework-free, SSR-safe
  behavior both runtimes attach: motion, toasts, celebrate, positioning,
  focus, and the pure logic widgets share.
- **The Livewire adapter** — the Blade views and `resources/js/sigta.js`
  (Alpine + Livewire glue), in the Composer package `sigta/ui`.
- **The Svelte adapter** — `@sigta-hq/svelte`.

Adapters render structure and set contract attributes; they never style.
Conformance renders every manifest example in both runtimes — on the server,
then live in a browser with pixels and interactions — and they must agree.
The parity rule: no component changes in one runtime only.
