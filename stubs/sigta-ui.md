# Sigta UI — LLM Agent Rules

> This file is published into your project by `php artisan sigta:install`. It is
> the **single source of truth** for how an LLM coding agent should use Sigta UI.
> It is overwritten on every `sigta:install` — never hand-edit it here; change it
> in the package instead. For the full component API, see `sigta-components.md`
> in the same directory.

Sigta UI is a Livewire/Blade component library. Components are anonymous Blade
components rendered via two interchangeable tag forms:

```blade
<sigta:button variant="cta">Save</sigta:button>
<x-sigta::button variant="cta">Save</x-sigta::button>
```

Both compile identically. Prefer the terse `<sigta:…>` form in your Blade.

## MUST — non-negotiable

### 1. Always use Sigta components, never raw HTML equivalents

When a Sigta component exists for an element, **use it**. Never reach for the
raw HTML tag with Tailwind utility classes.

| Want this | Use this | Never write |
|---|---|---|
| Button / link | `<sigta:button>` | `<button class="bg-… text-… rounded-…">` |
| Text input | `<sigta:input>` | `<input class="border …">` |
| Textarea | `<sigta:textarea>` | `<textarea class="…">` |
| Select | `<sigta:select>` | `<select class="…">` |
| Checkbox | `<sigta:checkbox>` | `<input type="checkbox" class="…">` |
| Radio | `<sigta:radio>` | `<input type="radio" class="…">` |
| Toggle | `<sigta:switch>` | custom toggle markup |
| Range slider | `<sigta:slider>` | `<input type="range" class="…">` |
| Field wrapper | `<sigta:field>` | hand-rolled label/error markup |
| Icon | `<sigta:icon name="…">` | `<svg>` / any icon library |
| Badge / status pill | `<sigta:badge>` / `<sigta:pill>` | `<span class="rounded-full …">` |
| Alert / status box | `<sigta:callout>` | `<div role="alert" class="…">` |
| Modal | `<sigta:modal>` + `<sigta:modal.trigger>` | custom dialog markup |
| Tooltip | `<sigta:tooltip>` | `title=` attr or custom popover |
| Dropdown menu | `<sigta:dropdown>` + `<sigta:dropdown.item>` | custom menu |
| Tabs | `<sigta:tabs>` family | custom tab JS |
| Accordion / FAQ | `<sigta:accordion>` + `<sigta:accordion.item>` | custom `<details>` styling |
| Card / surface | `<sigta:card>` / `<sigta:panel>` | `<div class="border rounded-…">` |
| Heading | `<sigta:heading>` | `<h1 class="font-… text-…">` |
| Body text variant | `<sigta:text>` | `<p class="text-…">` |
| Avatar | `<sigta:avatar>` | `<img class="rounded-full …">` |

For the complete list with props and variants, read **`sigta-components.md`**.

### 2. Never use raw colors — tokens only

**Never** write hex (`#1a2b3c`) or Tailwind palette classes (`text-gray-500`,
`bg-blue-600`, `border-slate-200`). Use the semantic token utilities:

- **Surfaces / text:** `bg-surface`, `bg-surface-raised`, `text-ink`,
  `text-graphite`, `border-line`
- **Brand / status:** `bg-accent`, `border-accent-line`, `text-success`,
  `text-danger`, `bg-danger`
- **Inverse (dark bands):** `bg-ink`, `bg-inverse`, `text-inverse-fg`,
  `text-inverse-fg-muted`, `border-inverse-line`

When a utility doesn't exist, use the raw CSS var inline:
`max-w-(--sigta-maxw)`, `px-(--sigta-gutter)`, or `var(--sigta-*)` in an
arbitrary value. The rule is absolute: **no literal colors outside the theme
layer.**

### 3. Never import another icon library

Use `<sigta:icon name="…">` (Lucide under the hood). Do not add Heroicons,
Phosphor, Feather, or any other icon set. If a needed icon is missing from
Lucide, say so in a comment rather than substituting another library.

### 4. Foundation must be wired before components work

The app's CSS entry must import Sigta and scan its views:

```css
@import "tailwindcss";
@import "../../vendor/sigta/ui/resources/css/sigta.css";
@source "../../vendor/sigta/ui/resources/views";
```

The layout `<head>` must include (after Livewire styles):

```blade
@sigtaScripts
{{-- Add @sigtaEditorScripts too, only if you use <sigta:editor> --}}
```

If either is missing, components render unstyled or broken. Do not try to
"fix" missing styles by adding utility classes to components — fix the
foundation.

## SHOULD — strong defaults

### Fonts and type roles

- Headings: `font-head` (Fraunces). Body prose: `font-body` (Newsreader).
- UI chrome (labels, buttons, nav, table headers, badges):
  `font-mono` (JetBrains Mono), small sizes (0.62–0.82rem), letter-spacing
  0.04–0.22em, often uppercase.
- Decorative handwriting: `font-hand` (Caveat) — sparingly.

### Shape and depth

- Controls: `rounded-base` (3px). Cards/panels: `rounded-panel` (6px).
  Devices/spotlights: `rounded-frame` (10px).
- Card elevation: `shadow-paper`. Hover lift on actionable cards:
  `hover:-translate-y-[2px]` + the shadow.

### Brand voice

- Accent (highlighter yellow) is **scarce**: one mark per view, status
  highlights only. Never large accent fills.
- Borders: strong outline `border-[1.5px] border-ink`; hairline `border border-line`.
- Numbers in data contexts: `tabular-nums`.
- Hand-drawn flourishes (rules, sparklines): `<sigta:rule>`, `<sigta:annot>`,
  `<sigta:chart.sparkline>` — inline SVG with `currentColor`.

## Component prop conventions

Props mirror Flux/Livewire naming. Common ones across many components:
`variant`, `size`, `icon`, `label`, `description`, `error`, `placeholder`,
`heading`, `name`, `href`. Attributes you don't recognize (e.g. `wire:model`,
`x-*`, `id`, `aria-*`) **always forward onto the native element** — never
swallow them. Don't add `wire:ignore` unless truly necessary.

## Cross-component events

Some components communicate via window/Livewire events. Listen for / dispatch
these rather than building custom wiring:

- `sigta-toast` `{heading, text, variant}` → shows a toast (region included once)
- `sigta-modal-show` / `sigta-modal-hide` `{name}` → open/close a modal
- `sigta-composer-submit` `{text}` → composer sent a message
- `sigta-kanban-move` `{card, from, to, position}` → card dragged on the board

## Livewire notes

Components are Blade + Alpine, never Livewire class components. They survive
Livewire DOM morphs: keep state in Alpine (`x-data`), avoid imperative DOM
mutations. Event-driven components (toast) listen for Livewire dispatches or
window events.

## When to extend vs. when to ask

If the design calls for something no Sigta component covers, **prefer composing
existing components** (a `<sigta:card>` containing `<sigta:button>` +
`<sigta:badge>`) over writing bespoke markup. If composition can't satisfy the
need, leave a clear comment saying what's missing and why — do not silently
introduce a parallel component vocabulary.
