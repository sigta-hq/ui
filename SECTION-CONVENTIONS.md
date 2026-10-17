# Sigta UI — Section conventions (Tier 3)

Sections are full-width, content-shaped page regions (hero, pricing, FAQ),
composed from Elements + Components. Read alongside CONVENTIONS.md — everything
there still applies (tokens only, `data-sigta`, attribute forwarding, a11y).

## Delivery model — hybrid

Each section is a real, reusable component with **sensible default content** so
`<sigta:section.hero />` renders something complete out of the box, AND every
piece is overridable via props/slots so a site can bend it to its own copy. The
kit page shows the full copyable source too. Brand stays locked; copy is free.

## Namespace & files

- Files: `resources/views/components/section/<name>.blade.php`,
  `packages/svelte/src/lib/components/Section.<Name>.svelte`,
  `packages/contract/src/manifests/section.<name>.ts`,
  `packages/contract/css/components/section.<name>.css`
- Tag: `<sigta:section.hero>` ↔ `<Section.Hero>`, `<sigta:section.pricing>` ↔
  `<Section.Pricing>`, …
- Root element: `<section data-sigta="section.<name>">` containing the wrap,
  `data-scope="section.<name>" data-part="wrap"` (max width and gutter from
  `--sigta-maxw` / `--sigta-gutter`). The section's stylesheet owns its
  vertical rhythm, so pages just stack sections.

## API shape

- Common text as props WITH DEFAULTS: `eyebrow`, `heading`, `lede`. Provide
  brand-flavored defaults (studio voice) so an empty tag still looks finished.
  Short text defaults are written in the manifest, the Blade `@props` and the
  Svelte `$props()` alike (the contract tests hold them equal).
- Richer/structured content via named slots (Svelte: snippets of the same
  name) that fall back to a default render:
  `@isset($visual) {{ $visual }} @else <default svg> @endisset` /
  `{#if visual}{@render visual()}{:else}…{/if}`.
- Repeated items (features, faq entries, pricing tiers) accept an `:items`
  array prop (array of associative arrays; plain objects in Svelte). Ship
  default items so the section is populated out of the box. Default item
  lists live once, in `packages/contract/src/sections/<name>.json`: Blade
  reads them with `\Sigta\Ui\Sections::get('<name>', '<key>')`, Svelte from
  `@sigta-hq/contract/sections`. A new section may also offer a slot escape
  hatch for the items; the existing ones do not.
- Where a Blade `@if` skips a piece, Svelte uses `truthy()` from
  `runtime/sections.ts`, which follows PHP's rules (`'0'`, `''` and `[]` are
  empty), so both runtimes skip the same pieces.
- Compose from existing components — do NOT hand-roll what already exists:
  `<sigta:section-header>`, `<sigta:tile-grid>`, `<sigta:accordion>`,
  `<sigta:panel>`, `<sigta:button variant="cta|inverse|ghost-inverse">`,
  `<sigta:stat variant="inline">`, `<sigta:eyebrow>`, `<sigta:mark>`,
  `<sigta:annot>`, `<sigta:rule>`.
- Dark sections (cta) are dark in their stylesheet (`bg-inverse
  text-inverse-fg` under the section's selector) and use
  `button variant="inverse"` / `ghost-inverse` — never `!`-overrides.
- A section never passes design classes to the components it renders. A
  size or spacing it needs on a child is a part rule in the section's
  stylesheet, scoped tightly enough to beat the child's own rule.

## Kit page

Each section gets `playground/resources/views/kit/<name>.blade.php` using
`<x-kit-shell title="…">` + `<x-kit-example label="…">` with the « snippet, so
it shows live + copyable source like every other kit page. Because sections are
wide, one example per page is fine. Show the zero-config form first
(`«sigta:section.hero />`), optionally a customized one.

## Definition of done (per section)

1. Manifest, stylesheet, Blade view and Svelte twin with working defaults +
   override props/slots (see [AUTHORING.md](AUTHORING.md)).
2. Renders with zero props AND with overrides — as manifest examples, so the
   conformance check compares both runtimes.
3. Pest test in `tests/Components/Section<Name>Test.php`: zero-prop render has
   `data-sigta="section.<name>"` + default copy; override prop/slot wins.
4. Kit page (live + source).
5. `npm run check` green; page 200 on `http://sigta-ui.test/kit/<name>`.
