# Sigta UI — Section conventions (Tier 3)

Sections are full-width, content-shaped page regions (hero, pricing, FAQ),
composed from Elements + Components. Read alongside CONVENTIONS.md — everything
there still applies (tokens only, `data-sigta`, attribute forwarding, a11y).

## Delivery model — structure ships, content doesn't

Each section is a real, reusable component that owns layout, rhythm and
composition, and **ships no content**: no copy, links, logos, client names or
illustrations. The package is public, so a default would end up on someone
else's site. Every piece renders only when the site passes it, and a section
with nothing passed renders its empty frame. Sigta's own copy lives in
`playground/resources/content/sigta-landing.php`, which the landing kit page
passes in. The kit page shows the full copyable source too.

## Namespace & files

- Files: `resources/views/components/section/<name>.blade.php`
- Tag: `<sigta:section.hero>`, `<sigta:section.pricing>`, …
- Root element: `<section data-sigta="section.<name>">` containing the wrap:
  `<div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">`. The section
  owns its vertical rhythm (`py-[clamp(...)]`), so pages just stack sections.

## API shape

- Common text as props defaulting to `null`: `eyebrow`, `heading`, `lede`.
  Render each only when set (`@if ($heading)`), and the section-header only
  when one of them is.
- Richer/structured content via optional named slots, with the layout adapting
  when one is absent: `@isset($visual) … @endisset`, and the two-column grid
  class only `isset($visual)`.
- Repeated items (features, faq entries, pricing tiers) accept an `:items`
  array prop (array of associative arrays) defaulting to `[]`.
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
wide, one example per page is fine. Show realistic, neutral example content
(an "Acme" studio, `#` links, `example.com` addresses), never Sigta's own.

## Definition of done (per section)

1. Section component with props/slots and no default content.
2. Renders with zero props (empty frame) AND with content.
3. Pest test in `tests/Components/Section<Name>Test.php`: zero-prop render has
   `data-sigta="section.<name>"` and no copy; passed props/slots render.
   `tests/NoBrandContentTest.php` stays green.
4. Kit page (live + source).
5. `./vendor/bin/pest` + `./scripts/check-tokens.sh` green; page 200 on
   `http://sigta-ui.test/kit/<name>`.
