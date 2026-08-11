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

- Files: `resources/views/components/section/<name>.blade.php`
- Tag: `<sigta:section.hero>`, `<sigta:section.pricing>`, …
- Root element: `<section data-sigta="section.<name>">` containing the wrap:
  `<div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">`. The section
  owns its vertical rhythm (`py-[clamp(...)]`), so pages just stack sections.

## API shape

- Common text as props WITH DEFAULTS: `eyebrow`, `heading`, `lede`. Provide
  brand-flavored defaults (studio voice) so an empty tag still looks finished.
- Richer/structured content via named slots that fall back to a default render:
  `@isset($visual) {{ $visual }} @else <default svg> @endisset`.
- Repeated items (features, faq entries, pricing tiers) accept an `:items`
  array prop (array of associative arrays) AND a slot escape hatch. Ship a
  default `$items` so the section is populated out of the box.
- Compose from existing components — do NOT hand-roll what already exists:
  `<sigta:section-header>`, `<sigta:tile-grid>`, `<sigta:accordion>`,
  `<sigta:panel>`, `<sigta:button variant="cta|inverse|ghost-inverse">`,
  `<sigta:stat variant="inline">`, `<sigta:eyebrow>`, `<sigta:mark>`,
  `<sigta:annot>`, `<sigta:rule>`.
- Dark sections (cta) use `bg-inverse text-inverse-fg` + `button variant="inverse"`
  / `ghost-inverse` — never `!`-overrides.

## Kit page

Each section gets `playground/resources/views/kit/<name>.blade.php` using
`<x-kit-shell title="…">` + `<x-kit-example label="…">` with the « snippet, so
it shows live + copyable source like every other kit page. Because sections are
wide, one example per page is fine. Show the zero-config form first
(`«sigta:section.hero />`), optionally a customized one.

## Definition of done (per section)

1. Section component with working defaults + override props/slots.
2. Renders with zero props AND with overrides.
3. Pest test in `tests/Components/Section<Name>Test.php`: zero-prop render has
   `data-sigta="section.<name>"` + default copy; override prop/slot wins.
4. Kit page (live + source).
5. `./vendor/bin/pest` + `./scripts/check-tokens.sh` green; page 200 on
   `http://sigta-ui.test/kit/<name>`.
