# Plan: Sigta UI — Livewire Component Library

## Goal

A self-owned Livewire/Blade component library with comprehensive component coverage, expressing the Sigta brand (paper/ink, serif type, hand-drawn accents) in every component — so multiple landing pages and SaaS platforms can be built on one foundation and stay visually identical.

## Approach

One monorepo. The Composer package `sigta/ui` lives at the repo root; a playground Laravel app in `/playground` consumes it via a path repository and doubles as living docs, kit page, and visual regression surface. Components are Blade components with Alpine.js behavior — not Livewire class components — with `wire:model` and Livewire event integration where it matters. Styling flows from a semantic CSS-variable token layer, mapped into Tailwind v4 via `@theme`, so consuming apps get brand-consistent utilities for their bespoke UI. Signature brand elements (hand-drawn rules, highlighter marks, annotations, paper grain) ship as first-class components, not CSS tricks. Delivery is tiered: a landing page can ship after Tier 1, a SaaS app after Tier 2, heavy widgets arrive in Tier 3.

## Key decisions made

- **Build strategy:** Scratch build (no third-party UI-kit dependency). Rationale: full ownership of markup, brand, and API for a long-horizon foundational asset.
- **Dependency line:** Alpine.js as the behavior layer (ships with Livewire anyway); headless engines allowed only for editor-class problems (TipTap); styled component libraries never. Rationale: don't rebuild industries, don't inherit someone else's look.
- **Styling engine:** Tokens-first — CSS variables as source of truth, Tailwind v4 `@theme` mapped onto them. Rationale: brand DNA lives in tokens/components; apps still get Tailwind ergonomics for bespoke UI.
- **Dark mode:** Semantic-light. Token names are role-based (`surface`, `fg`, `line`), only light values ship, components never use raw hex. Rationale: keeps a future dark palette a values-file exercise instead of a refactor, at near-zero cost now.
- **Packaging:** Monorepo, single self-contained Composer package (components + compiled Alpine JS + token CSS + self-hosted fonts + icons in one install). Rationale: one version number = one brand version; simplest consumption story.
- **Fonts:** Self-hosted in the package (Fraunces, Newsreader, JetBrains Mono, Caveat). Rationale: GDPR + performance; no Google Fonts runtime dependency.
- **Delivery:** Tiered releases, each tier gated by a real acceptance build (see Sequencing). Rationale: platforms ship while the library grows.

## Trade-offs accepted

- **Months of build effort and permanent maintenance** (accessibility, browser quirks) in exchange for full ownership. The hard widgets (date picker, command palette, editor, kanban) are each multi-week builds.
- **We own accessibility forever.** ARIA comboboxes, focus traps, roving tabindex — budgeted as ~30% of the work on interactive components.
- **Tailwind v4 floor.** Consuming apps must be on Tailwind v4; v3 projects must migrate before adopting.
- **Consistency by convention, not enforcement.** Utilities exist, so off-brand overrides are possible; the defense is a variant/prop API rich enough that staying inside it is the path of least resistance.
- **No dark mode shipped** (door held open via semantic tokens).

## Sequencing

1. **Phase 0 — Scaffold:** monorepo, package skeleton, playground app, CI, asset pipeline.
2. **Phase 1 — Foundation (Tier 0):** tokens, Tailwind theme, fonts, base primitives, signature brand components, kit page in playground.
3. **Phase 2 — Landing-page kit (Tier 1).** Acceptance: rebuild the existing Sigta landing page entirely from library components.
4. **Phase 3 — SaaS kit (Tier 2).** Acceptance: build a Plumb-style dashboard demo entirely from library components.
5. **Phase 4 — Heavy widgets (Tier 3):** built in order of real product need, not speculatively.

Rough solo, part-time calibration: Phases 0–1 ≈ 2 weeks, Phase 2 ≈ 3 weeks, Phase 3 ≈ 4–6 weeks, Phase 4 open-ended per widget.

---

## The plan itself

### 1. Repository & package architecture

```
sigta-ui/                      (monorepo, package at root)
├── composer.json              sigta/ui — installable straight from GitHub/Packagist
├── src/                       ServiceProvider, Blade component classes, directives
├── resources/
│   ├── views/components/      one file per component (button.blade.php, …)
│   ├── css/
│   │   ├── tokens.css         semantic CSS variables (source of truth)
│   │   └── sigta.css          @theme mapping + base styles + font-faces
│   ├── js/                    Alpine data/behaviors, compiled to dist/
│   └── fonts/                 self-hosted woff2
├── dist/                      compiled JS/CSS shipped with the package
├── tests/                     Pest + Livewire test helpers
└── playground/                Laravel app (path-repo consumer)
    └── kit pages: one page per component + landing rebuild + dashboard demo
```

- **Install story:** `composer require sigta/ui`, add `@import "sigta/ui"` (tokens + theme) to app CSS, `@sigtaAppearance` / auto-injected assets in layout. No npm step for consumers.
- **Distribution:** public on Packagist as `sigta/ui`, MIT licensed (fonts OFL, Sigta name and logo excluded). Git tags are the releases. Semver + CHANGELOG from day one.
- **Livewire integration model:** Blade + Alpine components; form controls forward `wire:model` (with `.live`/`.blur` modifiers) via attribute forwarding; Toast listens for Livewire `dispatch()` events; File upload wraps Livewire's `WithFileUploads`.

### 2. API conventions

- Tag syntax: plain Blade namespace — `<x-sigta::button variant="cta" icon:trailing="arrow-right">`. A compiled `<sigta:button>` prefix is a possible later ergonomics layer, not v1.
- Prop vocabulary follows conventional Livewire/Blade naming (`variant`, `size`, `icon`, `icon:trailing`, `label`, `description`, `error`) to keep the learning curve near zero for Livewire devs.
- Composition via slots; escape hatch via `$attributes` merge — but variants should cover 95% of use.
- Icons: wrapped Lucide set, normalized to brand stroke weight (~1.75px) and sizes; `<x-sigta::icon name="…">`. A hand-drawn custom icon set is a deferred luxury.

### 3. Token layer (Tier 0, the contract everything obeys)

Semantic roles, light values from the brand files:

| Token | Role | Light value |
|---|---|---|
| `--color-surface` | page ground (paper) | `#F4F1EA` |
| `--color-surface-raised` | cards/panels | `#FBFAF6` |
| `--color-fg` | primary text/ink | `#1A1A1A` |
| `--color-fg-muted` | secondary text (graphite) | `#6B6B6B` |
| `--color-line` | hairlines/borders | `#D9D5CC` |
| `--color-accent` | highlighter | `#FCE96A` |
| `--color-success` / `--color-danger` | status | `#3F6B4E` / `#B23A2E` |
| `--color-inverse` / `--color-inverse-fg` | ink-filled sections (CTA band, spotlight) | `#1A1A1A` / `#F4F1EA` |

Plus: type tokens (`--font-head` Fraunces, `--font-body` Newsreader, `--font-mono` JetBrains Mono, `--font-hand` Caveat), radius `3px`, the brand shadow, gutter/max-width, focus ring spec (2.5px solid fg, 3px offset), motion (hover `translateY(-2px)`, 140–220ms eases, `prefers-reduced-motion` kill switch). Components reference tokens only — **no raw hex in any component**. Enforce with a CI grep.

### 4. Component inventory (broad coverage + Sigta signatures)

**Tier 0 — Foundation & primitives**
| Component | Notes |
|---|---|
| Heading, Text | Fraunces/Newsreader scale from the brand type ramp |
| Icon | Lucide wrap, brand stroke |
| Separator | plain hairline |
| **Rule** ✎ | hand-drawn squiggle divider (a Sigta signature) |
| **Mark** ✎ | highlighter swipe behind words |
| **Annot** ✎ | Caveat handwritten annotation with rotation |
| **Eyebrow** ✎ | mono kicker with leading dash |
| **Paper** ✎ | grain + dot-grid background wrapper |
| Brand | wordmark + glyph lockup |

**Tier 1 — Landing-page kit** (acceptance: rebuild landing.html)
| Component | Notes |
|---|---|
| Button | primary / secondary / ghost / cta variants, arrow affordance, sizes, loading, disabled |
| Badge, Pill | outline / solid / accent; status pills with dot |
| Card | thumb/body pattern, hover lift |
| Callout | the bordered-left alert style |
| Avatar, Profile | |
| Navbar | sticky blur nav + mobile menu (the piece the brand files skipped) |
| Breadcrumbs | |
| Accordion | `<details>`-based, FAQ styling |
| Field, Input, Textarea, Select (native), Checkbox, Radio, Switch | full `wire:model` support; Field carries label/description/error |
| Table (static) | mono headers, tabular numerals |
| Tooltip | basic Alpine |
| Skeleton | |
| **Stat** ✎ | metric card with sparkline slot |

**Tier 2 — SaaS kit** (acceptance: Plumb-style dashboard demo)
| Component | Notes |
|---|---|
| Dropdown (menu), Popover, Context menu | shared Alpine positioning core (anchor/float) — build once |
| Modal | focus trap, sizes, wire-openable |
| Toast | stacked, Livewire event-driven |
| Tabs | |
| Radio group / Segmented | the brand `.seg` control |
| Pagination, Progress, Slider | |
| Table (rich) | sortable columns, row actions, selection |
| Autocomplete, Pillbox | combobox ARIA core shared |
| Command | palette on the same combobox core |
| OTP Input | |
| File upload | drag-drop + Livewire uploads |
| Timeline | |
| **Chart (sparkline + hand-drawn line/bar)** ✎ | own SVG charts in the brand's hand-drawn style — differentiator, no chart lib |
| **App shell** ✎ | the sidebar + main layout from the brand dashboard |

**Tier 3 — Heavy widgets** (build per product need)
| Component | Notes |
|---|---|
| Calendar → Date picker → Time picker | one calendar core, three surfaces |
| Editor | TipTap engine, Sigta chrome |
| Kanban | drag-drop + wire sorting |
| Carousel | |
| Composer | chat-style input |
| Chart (full) | axes/legend/interaction on the Tier 2 SVG core |
| Color picker | deferred indefinitely — brand barely needs it |

### 5. Accessibility baseline (applies to every component)

- Keyboard-complete per WAI-ARIA APG pattern for its role; focus-visible = token ring; semantic HTML first (native `select`, `details`, `dialog` where viable).
- `prefers-reduced-motion` respected globally (already brand convention).
- Combobox components (Select rich, Autocomplete, Command, Pillbox) share one audited ARIA core — write it once, carefully, in Tier 2.

### 6. Quality & docs

- **Tests:** Pest; Blade render tests + Livewire integration tests per component; browser tests (Pest v4 browser testing) for Alpine behavior on the tricky ones (modal trap, combobox, kanban).
- **Docs = playground:** every component gets a kit page (all variants/states, copy-paste snippets) — the brand index.html "kit" section, generated from real components. The kit pages are also the visual-regression surface (screenshot diffing later; eyeballs first).
- **Releases:** semver; MINOR = new components, PATCH = fixes, MAJOR = token or API breaks. CHANGELOG entry per release; consuming apps pin and upgrade deliberately.

---

## Open questions / deferred decisions

- **Compiled `<sigta:button>` tag prefix** — ergonomics-only; decide after Tier 1 when the API has settled.
- **Dark palette** — door held open by semantic tokens; needs a real design pass (warm dark, not slate) whenever a SaaS product demands it.
- **Hand-drawn custom icon set** — replace wrapped Lucide per-icon when brand value justifies it.
- **Color picker** — only if a product needs one.
