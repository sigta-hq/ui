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
| Loading indicator / spinner | `<sigta:loader>` | a spinner SVG, `animate-spin`, "Loading…" text |
| "Saving…" / "Saved" indicator | `<sigta:save-state>` | hand-written status text, `wire:loading` spans |
| Background job / queue progress | `<sigta:job>` | a progress bar + text assembled by hand |
| A count that changes (unread, open) | `<sigta:count>` | a bare `{{ $n }}` in a badge |

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

### 5. A loader never moves

Whatever state the work is in, the compass stays exactly where it is and
nothing around it shifts. Words popping in beside it, a percentage gaining a
digit, a loader that exists only while busy: each one makes the mark jump and
the UI feel unfinished. Work out every state before you place one:

- **Keep it rendered; change its state.** Never mount or unmount a loader, or
  the words beside it, per request: no `@if`, `x-if`, `x-show`, `hidden` or
  bare `wire:loading` around them. Drive `state` (`idle` → `working` →
  `done`) instead. If it must not show at rest, keep its box and hide it with
  `invisible`: `class="invisible" wire:loading.class.remove="invisible"`.
- **Let the kit hold the space.** `<sigta:save-state>` keeps the width of its
  longest label, `<sigta:button>` keeps its label and stands the compass in
  for its icon only once an action outlasts a beat, `<sigta:job>` gives its
  percentage a fixed width. Reach for them before pairing a loader with text
  by hand, and never swap a button's label for "Saving…" or for the loader.
- **Pin the compass to the side that stays put; the words grow away from
  it.** Against a right edge (the end of a toolbar, a `justify-between`
  header, the last table cell) use `<sigta:save-state align="end">`, or put a
  hand-built loader after its text and right-align the text. Anything between
  the loader and the edge it hangs from will push it. In a centred group both
  ends move, so reserve the words' room (next rule).
- **Reserve room for words and numbers that change.** Stack the alternatives
  in one grid cell (`grid` on the wrapper, `[grid-area:1/1]` on each label,
  `invisible` on all but the current one), or give the text a fixed width.
  Numbers get `tabular-nums` and a width that fits their longest value
  (`w-[4ch] text-right` for a percentage). Reserve the height too when a
  label changes font (`min-h-*`).
- **Hold the place of what is loading.** A loader standing in for content sits
  in a slot as tall as that content will be (`min-h-*`), so the content
  arriving doesn't shove the page.

```blade
{{-- Never: right-aligned, so "Saved" appearing after the compass pushes it left --}}
<header class="flex items-center justify-between">
    <sigta:heading>Draft</sigta:heading>
    <span class="flex items-center gap-2">
        <sigta:loader size="sm" wire:loading wire:target="save" />
        <span x-show="saved">Saved</span>
    </span>
</header>

{{-- Instead: the compass sits flush right and never moves; its words grow leftward --}}
<header class="flex items-center justify-between">
    <sigta:heading>Draft</sigta:heading>
    <sigta:save-state target="save" idle="All changes saved" align="end" />
</header>

{{-- By hand: the loader keeps its box and both labels share one cell --}}
<span class="inline-flex items-center gap-2">
    <sigta:loader size="sm" decorative class="invisible" wire:loading.class.remove="invisible" wire:target="sync" />
    <span role="status" class="grid">
        <span class="[grid-area:1/1]" wire:loading.class="invisible" wire:target="sync">Synced</span>
        <span class="[grid-area:1/1] invisible" wire:loading.class.remove="invisible" wire:target="sync">Syncing with the bank</span>
    </span>
</span>
```

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

### Scrollbars

- Every scroll area already wears the Sigta scrollbar from the foundation: a
  faint rounded stroke in the colour of the text it scrolls, darker while the
  pointer is over the area. Just write `overflow-*-auto`; on a dark band, the
  band's `text-inverse-fg` gives it a light bar.
- **Never style scrollbars yourself** (`::-webkit-scrollbar`,
  `scrollbar-color`, `scrollbar-width: thin`). Chromium drops the Sigta bar
  wherever the standard properties apply, and `scrollbar-color` is inherited
  by everything inside, so one stray rule brings back the browser's grey bars.
- The one exception is hiding a bar on a strip that has its own controls (a
  carousel): `[scrollbar-width:none] [&::-webkit-scrollbar]:hidden`.

### Brand voice

- Accent (highlighter yellow) is **scarce**: one mark per view, status
  highlights only. Never large accent fills.
- Borders: strong outline `border-[1.5px] border-ink`; hairline `border border-line`.
- Numbers in data contexts: `tabular-nums`.
- Hand-drawn flourishes (rules, sparklines): `<sigta:rule>`, `<sigta:annot>`,
  `<sigta:chart.sparkline>` — inline SVG with `currentColor`.

### Motion — finishing things should feel finished

- **Never show "Loading…" / "Saving…" text or a generic spinner.** Use
  `<sigta:loader>` (the Sigta compass), `<sigta:save-state target="…">` for
  saves, `<sigta:job>` for long-running work. A `<sigta:button wire:click="…">`
  already shows the compass beside its label when its action runs long, and
  nothing for a quick one: don't add `wire:loading` text or a loader to it.
  Wherever a loader goes, it holds still (MUST 5).
- **Completing an item must not just make it vanish.** Put list rows in a
  `data-sigta-motion` container with `wire:key` on each row, wrap the title's
  text in `<span data-sigta-strike>`, and complete through
  `$sigta.complete($el, () => $wire.complete(id))`: it ticks, crosses out and
  folds the row away, and restores it if the commit fails. Offer Undo with
  `Sigta::toast(..., action: [...], key: '…')`.
- **Celebrate sparingly.** `$sigta.celebrate(el, 'small')` for a satisfying
  tick; `'big'` (confetti) only for milestones like inbox zero or a finished
  import — `Sigta::celebrate('big', text: '…')` from Livewire.
- **Don't hand-roll keyframes or easing.** Use `animate-sigta-settle`,
  `ease-sigta-out` / `ease-sigta-pen` / `ease-sigta-spring` and the
  `--sigta-dur-*` tokens. Reduced motion is handled by the kit; never make an
  animation the only carrier of meaning.

## Component prop conventions

Props follow conventional Livewire naming. Common ones across many components:
`variant`, `size`, `icon`, `label`, `description`, `error`, `placeholder`,
`heading`, `name`, `href`. Attributes you don't recognize (e.g. `wire:model`,
`x-*`, `id`, `aria-*`) **always forward onto the native element** — never
swallow them. Don't add `wire:ignore` unless truly necessary.

## Cross-component events

Some components communicate via window/Livewire events. Listen for / dispatch
these rather than building custom wiring:

- `sigta-toast` `{heading, text, variant, duration, link, action, key}` → shows a toast (region included once). Prefer `Sigta::toast()` from Livewire, `$sigta.toast()` from Alpine, or `window.Sigta.toast()` from JS.
- `sigta-celebrate` `{level, text, target}` → a burst / ring + note / paper confetti. Prefer `Sigta::celebrate()` from Livewire, `$sigta.celebrate(el, level)` from Alpine.
- `sigta-save-state` `{name, state}` → drives the `<sigta:save-state name="…">` with that name. Prefer `$sigta.save(promise, name)`.
- `sigta-modal-show` / `sigta-modal-hide` `{name}` → open/close a modal
- `sigta-composer-submit` `{text}` → composer sent a message
- `sigta-kanban-move` `{card, from, to, position}` → card dragged on the board

## Livewire notes

Components are Blade + Alpine, never Livewire class components. They survive
Livewire DOM morphs: keep state in Alpine (`x-data`), avoid imperative DOM
mutations. Event-driven components (toast) listen for Livewire dispatches or
window events.

### Toast API

Trigger a toast with `Sigta::toast('msg')` (Livewire), `$sigta.toast('msg')` (Alpine), or `window.Sigta.toast(...)` (JS). Variants include `warning` and `info` (`info` is Sigta-specific).

Include `<sigta:toast />` once in your layout. With `wire:navigate`, wrap it in `@persist('toast')`.

## When to extend vs. when to ask

If the design calls for something no Sigta component covers, **prefer composing
existing components** (a `<sigta:card>` containing `<sigta:button>` +
`<sigta:badge>`) over writing bespoke markup. If composition can't satisfy the
need, leave a clear comment saying what's missing and why — do not silently
introduce a parallel component vocabulary.
