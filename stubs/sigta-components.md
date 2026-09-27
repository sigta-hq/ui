# Sigta UI — Component Reference

> Published into your project by `php artisan sigta:install`. Overwritten on
> every run — never hand-edit it here. This is the exhaustive API surface an LLM
> agent scans to pick the right component. Read the rules in `sigta-ui.md` first.

Every component is an anonymous Blade component. Either tag form works:

```blade
<sigta:button variant="cta">Save</sigta:button>      {{-- preferred terse form --}}
<x-sigta::button variant="cta">Save</x-sigta::button>  {{-- canonical form --}}
```

Subcomponents use a dot: `<sigta:dropdown.item>`, `<sigta:tabs.panel>`.
Unknown attributes (`wire:model`, `x-*`, `id`, `aria-*`, `name`) always forward
to the native element. Props with no default are **required**.

---

## Elements (indivisible blocks)

### typography family

A family covering text roles. Use these instead of styling `<h1>`/`<p>` by hand.

#### `heading`
- **Props:** `level` (int, default `2`), `size` (default derived from level: 1→xl, 2→lg, 3→base, else sm)
- **Sizes:** `display`, `xl`, `lg`, `base`, `sm`
- Renders `<h1>`–`<h6>`. Semantic level drives default size; override with `size`.
```blade
<sigta:heading level="1" size="display">Page title</sigta:heading>
```

#### `text`
- **Props:** `variant` (default `body`)
- **Variants:** `lede`, `muted`, `caption`, `hand`, `body`
- Body paragraph with tonal/typographic variants. `lede` = large intro; `muted` = graphite secondary; `caption` = small mono; `hand` = Caveat handwriting.
```blade
<sigta:text variant="muted">Secondary detail.</sigta:text>
```

#### `eyebrow`
- **Props:** none
- Mono uppercase kicker with a leading dash rule. Sits above section headings.
```blade
<sigta:eyebrow>01 · Install</sigta:eyebrow>
```

#### `mark`
- **Props:** none
- Highlighter swipe behind a word or two. Use **sparingly** — one per view.
```blade
<sigta:heading level="2">One brand, <sigta:mark>every surface</sigta:mark>.</sigta:heading>
```

#### `annot`
- **Props:** `rotate` (default `-6`), `tone` (default `ink`), `muted` (bool, default `false`), `decorative` (bool, default `true`)
- **Tones:** `muted`, `accent`, `ink`
- Handwritten margin annotation (Caveat). `decorative=true` → `aria-hidden`.
```blade
<sigta:annot tone="accent">lad os tegne det op ↘</sigta:annot>
```

#### `rule`
- **Props:** `variant` (int, default `1`)
- **Variants:** `1`, `2`, `3` (three hand-drawn squiggle paths)
- Decorative divider, `aria-hidden`.
```blade
<sigta:rule variant="2" />
```

#### `separator`
- **Props:** `variant` (default `solid`)
- **Variants:** `solid`, `dashed`
- Plain `<hr>`. Use inside content; use `<sigta:rule>` for decorative dividers.

#### `brand`
- **Props:** `name` (default `SIGTA`), `glyphOnly` (bool, default `false`), `size` (default `base`)
- **Sizes:** `sm`, `base`, `lg`
- Wordmark + glyph lockup. `currentColor` throughout.
```blade
<sigta:brand size="lg" />
```

### `button`
- **Props:** `variant` (default `secondary`), `size` (default `base`), `icon` (default null), `href` (default null), plus any native `<button>`/`<a>` attributes
- **Variants:** `primary`, `cta`, `ghost`, `danger`, `inverse`, `ghost-inverse`, `secondary`
- **Sizes:** `sm`, `base`
- Renders `<a>` when `href` is set, else `<button>`. Lifts on hover. `cta` = accent-filled emphasis. Pass `target="_blank"` on a link button (adds `rel="noopener"` unless `rel` is set).
```blade
<sigta:button variant="cta" href="/book">Book a project <sigta:icon name="arrow-right" size="sm" /></sigta:button>
<sigta:button href="https://calendly.com/sf-sigta/30min" target="_blank">Book a call</sigta:button>
<sigta:button>Preview</sigta:button>
<sigta:button variant="ghost">Cancel</sigta:button>
```

### `badge`
- **Props:** `variant` (default `outline`)
- **Variants:** `solid`, `accent`, `outline`
- Small mono pill with 1.5px border.
```blade
<sigta:badge>Draft</sigta:badge>
<sigta:badge variant="solid">Live</sigta:badge>
```

### `pill`
- **Props:** `variant` (default `neutral`)
- **Variants:** `success`, `danger`, `neutral`
- Status pill with leading dot and tinted background.

### `avatar`
- **Props:** `src` (default null), `name` (default null), `alt` (default null), `shape` (default `circle`), `size` (default `base`), `tone` (default `ink`)
- **Sizes:** `xs`, `sm`, `base`, `lg` · **Shapes:** `circle`, `square` · **Tones:** `ink`, `line`
- Shows initials fallback when no `src`.

### `profile`
- **Props:** `name`, `meta`, `src`, `reverse` (bool, default `false`)
- Avatar + name + optional meta line. `reverse` mirrors layout.

### `assignee-picker`
- **Props:** `user` (current assignee object/array with id+name, or null), `members` (iterable of id+name), `method` (Livewire method), `args` (leading args), `size` (default `xs`), `showName` (bool, default `false`), `label`, `inline` (bool, default `false`)
- Avatar (or dashed "unassigned" affordance) opening a client-side searchable people list. On select it calls `method(...args, userId)` on the nearest Livewire component (so one component serves board cards, subtasks, the task modal). Default teleports the panel to `<body>`; `:inline` renders it absolutely for a native `<dialog>`. Pass a value-keyed `wire:key`. Re-check membership server-side in the called method.
```blade
<sigta:assignee-picker :user="$task->assignee" :members="$members" method="assignTask" :args="[$task->id]" wire:key="assignee-{{ $task->assignee?->id }}" />
```

### `icon`
- **Props:** `name` (**required**), `size` (default `base`)
- **Sizes:** `xs`, `sm`, `base`, `lg`
- Lucide renderer. `aria-hidden` by default; pass `aria-label` for meaningful icons.
```blade
<sigta:icon name="arrow-right" size="sm" />
```

### `input`
- **Props:** `label`, `description`, `error`, `icon`, `type` (default `text`), plus native `<input>` attrs (`wire:model`, `name`, etc.)
- Text input with optional leading icon, wrapped in `<sigta:field>`.
```blade
<sigta:input label="Email" type="email" icon="mail" wire:model="email" placeholder="you@studio.co" description="We only use this for the invoice." />
```

### `textarea`
- **Props:** `label`, `description`, `error`, `rows` (default `4`), plus native attrs

### `select`
- **Props:** `label`, `description`, `error`, plus native `<select>` attrs
- Native select with chevron overlay, wrapped in `<sigta:field>`.

### `checkbox`
- **Props:** `label`, plus native attrs. Renders `<label>` wrapper when `label` set, bare input otherwise.

### `radio`
- **Props:** `label`, plus native attrs. Same wrapper behavior as checkbox.

### `switch`
- **Props:** `label`, plus native attrs. `role="switch"` toggle with sliding thumb.

### `slider`
- **Props:** `label`, `description`, `error`, `showValue` (bool, default `false`), plus native range attrs
- Brand-styled range input; optional live value readout.

### `skeleton`
- **Props:** `variant` (default `line`)
- **Variants:** `heading`, `block`, `circle`, `line`
- Loading placeholder, `aria-hidden`.

### `progress`
- **Props:** `value` (default `0`), `max` (default `100`), `label`, `indeterminate` (bool, default `false`)
- Determinate or indeterminate progress bar.

### `field`
- **Props:** `label`, `description`, `error`, `for` (default auto-generated id)
- Field wrapper: label + slot (the control) + description/error. Transparent if no label/description/error. Use when wrapping a custom control.

### `label`
- **Props:** `variant` (default `default`)
- **Variants:** `default`, `inverse`
- Mono uppercase form label. `inverse` re-colors it for a dark surface (inside `<sigta:panel variant="inverse">`).

### `elapsed-clock`
- **Props:** `since` (ISO-8601 string or Carbon/DateTimeInterface; default = now)
- Live-ticking `HH:MM:SS` value counting up from `:since`. Presentational — the timer's persistence stays in your app; it cleans up its interval on teardown.
```blade
<sigta:elapsed-clock :since="$entry->started_at" class="text-lg" />
```

### `duration`
- **Props:** `minutes` (int|null), `format` (default `compact`)
- **Formats:** `compact` (`1h 15m`), `clock` (`01:30`)
- Static duration display in tabular numerals. Exact hours drop the minutes (`2h`); zero → `0m`. For a value that counts up live use `elapsed-clock`.

---

## Components (functional units with internal parts)

### `stat`
- **Props:** `label`, `value`, `delta`, `trend`, `variant` (default `panel`), `tone` (default `default`)
- **Variants (`variant`):** `inline`, `panel` · **Variants (`trend`):** `up`, `down` · **Tones (`tone`):** `default`, `inverse`
- Numeric figure with optional delta + trend arrow. `tone="inverse"` darkens it for a dark surface (boxed variant gets a dark background; inline stays bare to sit on a `<sigta:panel variant="inverse">`); trend colors stay semantic.

### `money`
- **Props:** `minor` (int|null, MINOR units), `currency` (default `DKK`), `decimals` (default `2`), `symbol`, `position` (`before`/`after`), `thousands`, `decimal`, `suffix`
- Formats a minor-unit amount (øre/cents) in tabular numerals. Known codes (DKK/EUR/USD/GBP/SEK/NOK) carry symbol, position and separators; override any piece, or pass an unknown code (trailing symbol). `suffix` renders a muted per-unit tail (e.g. `/ hour`). The amount and currency stay app data.
```blade
<sigta:money :minor="$rate->amount_minor" :currency="$rate->currency->value" suffix="/ hour" />
```

### `money-input`
- **Props:** `label`, `value` (initial MINOR units), `placeholder`, `currency` (default `DKK`), `currencies` (code⇒label map), `currencyName`, `currencyModel`, plus native attrs
- Amount field (entered in major units) with an inline currency switcher: a small dropdown button (code + chevron) sits inside the same field, not a separate toggle. Emits MINOR units onto the forwarded `wire:model` via a hidden input; the currency binds separately via `currencyModel` through a hidden input. App logic (the currency enum) stays in your app.
```blade
<sigta:money-input label="Hourly rate" :value="$rate->amount_minor" wire:model="amountMinor" :currency="$currency" currency-model="currency" />
```

### `callout`
- **Props:** `variant` (default `info`), `icon`, `heading`
- **Variants:** `success`, `danger`, `info`
- Inline alert with left accent border; icon + ARIA role auto-selected.
```blade
<sigta:callout variant="success" heading="Deploy complete.">Plumb 2.4 is live.</sigta:callout>
```

### `card`
- **Props:** `hover` (bool, default `true`)
- Raised surface with optional thumb slot (`<x-slot:thumb>`). Lifts on hover when `hover=true`.
- Its body has fluid padding. Pass any padding class on the card (`p-5`, `sm:p-4`) and it replaces the body padding instead of adding to it.

### `panel`
- **Props:** `variant` (default `default`)
- **Variants:** `default`, `inverse`
- Bordered surface **without** `overflow-hidden` — so overhanging annotations/badges show. Static (no hover lift). Use where Card would clip. `variant="inverse"` is a dark ink band; pair with `<sigta:label variant="inverse">` and `<sigta:stat tone="inverse">`.
- Default padding is fluid (24 to 40px). Pass any padding class (`p-4`, `px-6`, `sm:p-5`, `!p-2`) and it replaces the default entirely; without one, the default applies.
- Children stack as a flex column, so `gap-*` spaces them and `items-*` / `justify-*` act on that column (`items-center` centres them horizontally). Pass a display class (`flex`, `grid`, `block`) and it replaces the stack entirely, so `class="flex items-center justify-between"` is a row; a direction class alone (`flex-row`) keeps the flex and turns it. Responsive classes (`sm:flex-row`) build on the stack.
```blade
<sigta:panel class="flex items-center justify-between gap-3 p-4">
    <sigta:profile name="Ada Lovelace" meta="ada@example.com" />
    <sigta:badge>Owner</sigta:badge>
</sigta:panel>
```

### `tooltip`
- **Props:** `text` (**required**), `position` (default `top`)
- **Positions:** `top`, `bottom`, `left`, `right`
- Ink bubble on hover/focus, 150ms open delay. Escape dismisses.
```blade
<sigta:tooltip text="Delete permanently"><sigta:button variant="ghost"><sigta:icon name="trash-2" /></sigta:button></sigta:tooltip>
```

### `dropdown` family

Menu container + items. `sigtaDropdown` Alpine handles open/escape/click-outside/roving focus.

#### `dropdown`
- **Props:** `position` (default `bottom-start`)
- **Positions:** `bottom-start`, `bottom-end`, `top-start`, `top-end`
- Trigger goes in the default slot; items in `<x-slot:menu>` or directly.

#### `dropdown.item`
- **Props:** `icon`, `variant` (default `default`), `kbd`, plus `href`
- **Variants:** `danger`, `default`
- Renders `<a>` when `href` given, else `<button role="menuitem">`. Closes menu on click.

#### `dropdown.separator`
- **Props:** none. `<hr role="separator">` between items.

```blade
<sigta:dropdown>
    <x-slot:trigger><sigta:button>Open</sigta:button></x-slot:trigger>
    <sigta:dropdown.item href="/profile" icon="user">Profile</sigta:dropdown.item>
    <sigta:dropdown.separator />
    <sigta:dropdown.item variant="danger" icon="log-out">Sign out</sigta:dropdown.item>
</sigta:dropdown>
```

### `popover`
- **Props:** `position` (default `bottom-start`)
- **Positions:** `bottom-start`, `bottom-end`, `top-start`, `top-end`
- Generic popover with dropdown positioning but free-form content (no menu semantics).

### `context-menu`
- **Props:** none (default slot = surface, named `menu` slot = items)
- Right-click surface that opens a dropdown-styled menu at the pointer. Reuses `dropdown.item`.

### `modal` family

#### `modal`
- **Props:** `name`, `size` (default `base`), `heading`, `dismissible` (bool, default `true`), `fullscreen` (bool, default derived from `size`)
- **Sizes:** `sm`, `base`, `lg`, `xl`
- **Full screen on a phone:** below the tablet breakpoint a `lg` or `xl` modal fills the screen edge to edge (no gutter, border, or rounding) while `sm` and `base` stay centred cards, because a workspace needs the room and a confirmation does not. Pass `fullscreen` to force it on a small modal, or `:fullscreen="false"` to keep a large one centred. Above the breakpoint every size is unchanged.
- A full screen modal lays its body out as a column with a full-height minimum, so a child marked `flex-1` fills the screen exactly and taller content scrolls the dialog as usual.
- Dialog driven by `sigtaModal` Alpine. Open via `<sigta:modal.trigger>` or `sigta-modal-show {name}` window event.
- With a `heading` the close button sits in the header row; with no heading it floats in the top-right corner and no header band is reserved, so keep the top-right of your content clear for it.
- Keeps its open state across a host Livewire re-render (it renders `wire:ignore.self`), so a modal that stays open while its contents autosave or run New/Delete actions does not close on the morph. You do not add `wire:ignore.self` yourself.

#### `modal.trigger`
- **Props:** `name`. Wraps content; click dispatches `sigta-modal-show {name}`.

#### `modal.close`
- **Props:** `name`. Wraps content; click dispatches `sigta-modal-hide {name}`.

```blade
<sigta:modal.trigger name="settings"><sigta:button>Settings</sigta:button></sigta:modal.trigger>
<sigta:modal name="settings" heading="Settings">…</sigta:modal>
```

### `toast`
- **Props:** `position` (default `bottom-end`)
- **Positions:** `bottom-end`, `bottom-start`, `top-end`, `top-start`
- Toast region — **include once per layout**. With `wire:navigate`, wrap in `@persist('toast')` so toasts survive page transitions.
- **Variants:** `success`, `warning`, `danger`, `info` (Sigta-only)
- **Trigger from Livewire (preferred):**
```php
use Sigta\Ui\Facades\Sigta;

Sigta::toast('Your changes have been saved.');

Sigta::toast(
    heading: 'Changes saved.',
    text: 'You can always update this in your settings.',
    variant: 'success',
    duration: 5000,
    link: ['text' => 'View settings', 'href' => '/settings', 'navigate' => true],
);
```
- **Trigger from Alpine:** `$sigta.toast('Saved')` or `$sigta.toast({ heading: '…', variant: 'warning' })`
- **Trigger from JS:** `window.Sigta.toast('Saved')` or `window.Sigta.toast({ text: '…', variant: 'danger' })`
- **Low-level event** (still supported): `$this->dispatch('sigta-toast', heading: 'Saved', variant: 'success')`
```blade
@persist('toast')
    <sigta:toast />
@endpersist
```

### `floating-bar`
- **Props:** `show` (bool, default `false`), `label` (default null; names the bar as a region)
- Dark pill pinned to the bottom centre that slides up when `show` turns true and away when it turns false, for state that lasts while the member works (a running timer, a bulk selection). A first render that is already shown appears without animating. Drive `show` from Livewire; the morph updates it in place. Content is your slot. While up it sets `--sigta-floating-space` on `<html>`, which `shell.main` and bottom toasts add to their bottom edge. Wrap it in `@persist` when it lives in a `wire:navigate` layout, so it does not replay on every page.
```blade
<sigta:floating-bar :show="$entry !== null" label="Running timer">…</sigta:floating-bar>
```

### `tabs` family

#### `tabs`
- **Props:** `default` (the active tab name on load)

#### `tabs.list`
- **Props:** `label` (default `Tabs`). `role="tablist"`; arrow-key roving.

#### `tabs.tab`
- **Props:** `name` (**required**). `role="tab"`.

#### `tabs.panel`
- **Props:** `name` (**required**). `role="tabpanel"`; shown when its tab is active.

```blade
<sigta:tabs default="overview">
    <sigta:tabs.list>
        <sigta:tabs.tab name="overview">Overview</sigta:tabs.tab>
        <sigta:tabs.tab name="billing">Billing</sigta:tabs.tab>
    </sigta:tabs.list>
    <sigta:tabs.panel name="overview">…</sigta:tabs.panel>
    <sigta:tabs.panel name="billing">…</sigta:tabs.panel>
</sigta:tabs>
```

### `accordion` family

#### `accordion`
- **Props:** `name` (forwards to items for native exclusive-open via `<details name>`).

#### `accordion.item`
- **Props:** `heading` (**required**), `name`
- Native `<details>`/`<summary>`. "+" rotates when open. No JS.

```blade
<sigta:accordion name="faq">
    <sigta:accordion.item heading="How does billing work?">Monthly, cancel anytime.</sigta:accordion.item>
</sigta:accordion>
```

### `breadcrumbs` family

#### `breadcrumbs`
- **Props:** none. Container (`<nav><ol>`).

#### `breadcrumbs.item`
- **Props:** `href`, `current` (bool, default `false`). Renders link, or current-page span with `aria-current="page"`.

### `navbar`
- **Props:** `ctaLabel`, `ctaHref`, `target`. Slots: brand, default (links), actions. Sticky translucent header; mobile menu via Alpine. When `ctaLabel` is set and the actions slot is empty, renders a CTA button; `target` forwards onto it like `<sigta:button>`.

### `navlink`
- **Props:** `current` (bool, default `false`). Underline grows on hover; `aria-current` when current.

### `pagination`
- **Props:** `paginator` (Laravel paginator), `pages`, `current` (default `1`), `url`
- Accepts a Laravel paginator or manual pages/current/url.

### `command` family

Command palette (⌘K / Ctrl-K).

#### `command`
- **Props:** `name` (default `command`), `placeholder` (default `Type a command…`), `shortcut` (bool, default `true`)

#### `command.item`
- **Props:** `icon`, `kbd`, `value`, plus `href`. Renders `<a>` or `<button>`; auto-closes palette on click.

### `autocomplete` family

#### `autocomplete`
- **Props:** `label`, `description`, `error`, `placeholder`, `name`. Filtering combobox over slotted options; hidden input carries `name`/`wire:model`.

#### `autocomplete.option`
- **Props:** `value`, `label`. Option row shared by autocomplete/listbox/pillbox.

### `listbox`
- **Props:** `label`, `description`, `error`, `placeholder` (default `Select…`), `name`, `value`
- Rich single-select (button trigger + combobox over `autocomplete.option`).

### `pillbox`
- **Props:** `label`, `description`, `error`, `placeholder` (default `Add…`), `name`
- Multi-select combobox with removable chips; hidden input carries a JSON array of selected values.

### `segmented`
- **Props:** `name` (**required**), `options` (array, default `[]`), `label`, `checked`. Radio-group semantics, zero JS.

### `otp`
- **Props:** `length` (default `6`), `label`, `description`, `error`, `autofocus` (bool, default `false`)
- One-time-code input: row of single-char boxes backed by one hidden input. Supports paste/arrow keys.

### `file-upload`
- **Props:** `label`, `description`, `error`, `multiple` (bool, default `false`), `helper`
- Dropzone file input; label wraps an sr-only native input so all attrs forward.

### `composer`
- **Props:** `placeholder` (default `Write a message…`), `name`
- Chat composer: auto-growing textarea, leading (`<x-slot:leading>`) attach slot, send button. Dispatches `sigta-composer-submit {text}`.

### `editor`
- **Props:** `label`, `description`, `error`, `name`, `value` (default `''`), `format` (default `html`), `variant` (default `default`), `placeholder` (default `''`), `rows` (min height in lines; default `6`, or `3` for `inline`), `upload-url` (default `null`), `resolve-url` (default `null`)
- **Formats:** `html`, `markdown`. **Variants:** `default`, `inline`
- Rich text editor (TipTap). **Requires `@sigtaEditorScripts` in layout head.** Toolbar: bold/italic/strike/link/heading/lists/quote/code, plus a picture tool when `upload-url` is set. Links: Cmd/Ctrl+K or the toolbar edits (with Open and Remove), Cmd/Ctrl+click follows; a plain click only places the caret. `format="markdown"` parses `value` as markdown and writes markdown to the hidden input (single newlines are line breaks). `variant="inline"` is the always-editable form for detail views: borderless until hover, toolbar only while editing. The editor never reads its hidden input back, so Livewire re-renders cannot disturb typing; replace content on purpose by dispatching `sigta-editor-set` on the root with the new value as detail.
- **Pictures:** with `upload-url`, a dropped, pasted or picked image is posted there as multipart `file` with the session CSRF token in `X-CSRF-TOKEN`; the endpoint answers JSON `{url, name?}` and the picture is inserted as an image node (`![name](url)` in markdown). A failed upload raises a warning toast and inserts nothing. Data URLs are refused.
- **Titled links:** with `resolve-url`, a pasted address on the page's own origin is linked at once, then `GET resolve-url?url=…` is asked for JSON `{title, url?}`; the link text becomes the title (and the href the returned `url`) when it answers, and stays the address when it does not. With text selected, a pasted address links the selection as before.
```blade
<sigta:editor format="markdown" variant="inline" placeholder="Add a description" rows="3" wire:model="description" :value="$description" />
<sigta:editor format="markdown" wire:model="body" :value="$body" :upload-url="route('images.store')" :resolve-url="route('links.resolve')" />
```

### `calendar`
- **Props:** `name`, `value` (Y-m-d), `min` (Y-m-d), `max` (Y-m-d)
- Standalone Mon-first month grid. Reactive Alpine nav, full keyboard support. Emits `calendar-change`.

### `date-picker`
- **Props:** `label`, `description`, `error`, `name`, `value` (Y-m-d), `min`, `max`, `placeholder` (default `Select a date`)
- Text input + calendar-icon button opening a calendar popover.

### `time-picker`
- **Props:** `label`, `description`, `error`, `name`, `value` (HH:MM), `min`, `max`, `step` (default `30`), `format` (default `24`), `placeholder` (default `Select a time`)
- **Formats:** `24`, `12`. Model stays 24h HH:MM regardless of display format.

### `duration-input`
- **Props:** `label`, `value` (initial whole minutes), `placeholder`, plus native attrs
- Compact `1h59m` field that swaps to an editable `HH:MM` on click; on blur/Enter it parses (forgiving: `H:MM`, `HH:MM`, or bare minutes) to whole minutes and emits them on the forwarded `wire:model` via a hidden input. Escape cancels. Pass a value-keyed `wire:key` so a reset gives fresh Alpine state.
```blade
<sigta:duration-input label="Estimate" :value="$task->estimate_minutes" wire:model="estimate" wire:key="est-{{ $task->estimate_minutes }}" />
```

---

## Layout (structural scaffolds)

### `section-header`
- **Props:** `heading`, `eyebrow`, `lede`
- Heading left + graphite lede right, baseline-aligned.

### `tile-grid` family

#### `tile-grid`
- **Props:** `cols` (default `3`)
- **Values:** `2`, `3`, `4`
- Hairline-gap grid (1px gap over `bg-line` ground reads as thin dividers).

#### `tile-grid.tile`
- **Props:** `num`. One cell; optional mono number on top.

### `shell` family

App shell grid: sidebar + main.

#### `shell`
- **Props:** `full` (bool, default `false`). `full=true` → full-viewport app; default → embeddable rounded frame. **Slots:** `header` (optional, a `shell.header`), `sidebar`, default (the main).

#### `shell.sidebar`
- **Props:** none. **Slots:** `brand`, `actions`, default (the nav). A full rail on desktop; below the tablet breakpoint it becomes a top bar whose burger slides the nav in as a **drawer** over the page, with a scrim behind it. Alpine-driven, no wiring needed.
- The open drawer is a modal dialog: focus is trapped in it, the page behind stops scrolling, and it closes on the scrim, on Escape, on its own close button, and after a `wire:navigate` visit. None of that applies to the desktop rail. Give the sidebar an `aria-label` to name both the landmark and the drawer (defaults to "App navigation").
- `actions` sits at the far end of the header row, hard against the burger, and stays visible when the nav is collapsed. Use it for the one or two controls that must be reachable without opening the nav (a search or quick-action trigger); everything else belongs in the nav. Keep it to icon-sized controls: the row is as narrow as the rail on desktop.

#### `shell.header`
- **Props:** none. **Slots:** `brand`, `actions`. Pass it in the shell's optional `header` slot: a full-width row above the sidebar and main, brand on the left, actions on the right. Below the tablet breakpoint it is the phone's top bar and carries the burger that opens the sidebar drawer (the sidebar then drops its own brand row, actions and burger, and is navigation alone). A shell without `header` is unchanged.
```blade
<sigta:shell full>
    <x-slot:header>
        <sigta:shell.header>
            <x-slot:brand><sigta:brand /></x-slot:brand>
            <x-slot:actions><sigta:avatar name="Ada Lovelace" size="sm" /></x-slot:actions>
        </sigta:shell.header>
    </x-slot:header>
    <x-slot:sidebar><sigta:shell.sidebar>…nav…</sigta:shell.sidebar></x-slot:sidebar>
    <sigta:shell.main>…</sigta:shell.main>
</sigta:shell>
```

#### `shell.bar`
- **Props:** `heading`, `sub`. Top bar: heading + mono sub left, actions slot right.

#### `shell.link`
- **Props:** `icon`, `current` (bool, default `false`). Mono graphite; inverts to ink when current.

#### `shell.main`
- **Props:** none. Padded main content column. Its bottom padding grows by a visible `floating-bar`'s footprint; if your app scrolls a different element, add `pb-[var(--sigta-floating-space,0px)]` to it.

### `paper`
- **Props:** `dots` (bool, default `true`), `grain` (bool, default `true`)
- Page-ground overlay (dot grid + paper grain). **Place once, directly inside `<body>`.**
```blade
<body><x-sigta::paper /> … </body>
```

---

## Data display

### `table` family

#### `table`
- **Props:** `caption`. Brand data table; mono uppercase caption + headers, hairline rows, tabular numerals.

#### `table.columns` / `table.column`
- Columns wrapper (`<thead>`) and `<th scope="col">` header cell.

#### `table.rows` / `table.row` / `table.cell`
- `<tbody>` (removes last-row bottom border), `<tr>`, `<td>` (tabular numerals).

```blade
<sigta:table caption="Q3 revenue">
    <sigta:table.columns>
        <sigta:table.column>Month</sigta:table.column>
        <sigta:table.column>Revenue</sigta:table.column>
    </sigta:table.columns>
    <sigta:table.rows>
        <sigta:table.row>
            <sigta:table.cell>July</sigta:table.cell>
            <sigta:table.cell>$12,400</sigta:table.cell>
        </sigta:table.row>
    </sigta:table.rows>
</sigta:table>
```

### `timeline` family

#### `timeline`
- **Props:** none. Vertical container (`<ol>`); rail + ink dot per entry.

#### `timeline.item`
- **Props:** `heading`, `meta`, `variant` (default `default`)
- **Variants:** `accent`, `default`. `accent` = accent-filled dot.

### `comment`
- **Props:** `author`, `meta` (timestamp line), `avatar` (name/initials; null = no avatar), `src` (avatar image), `editing` (bool, default `false`)
- **Slots:** default = body; `actions` = owner controls (edit/delete); `edit` = inline editor shown when `:editing`
- One authored comment in a feed. Presentational — persistence, validation, and who-may-edit auth stay in your Livewire component. Loop it for a feed; pair with `<sigta:composer>` or a `<sigta:textarea>` form.
```blade
<x-sigta::comment :author="$c->author?->name" :meta="$c->created_at?->diffForHumans()" :editing="$editingId === $c->id" wire:key="comment-{{ $c->id }}">
    {{ $c->body }}
    @if ($c->user_id === auth()->id())
        <x-slot:actions><sigta:button variant="ghost" size="sm" wire:click="edit({{ $c->id }})"><sigta:icon name="pencil" size="sm" /></sigta:button></x-slot:actions>
    @endif
</x-sigta::comment>
```

### `kanban` family

Native HTML5 drag-and-drop board.

#### `kanban`
- **Props:** none (x-data root owns drag state). On drop dispatches `sigta-kanban-move {card, from, to, position}`.

#### `kanban.column`
- **Props:** `label`, `id` (auto if null), `count` (counted from slot if null), `aria` (body accessible name; defaults to `label`)
- **Slots:** `heading` replaces the visible label with your own control (e.g. an inline-rename input) while the count and `actions` stay put; `actions` adds header controls (a menu, a delete button) to the left of the count, which stays pinned to the right. The body's `aria-label` comes from the string `label`/`aria`, so slotted header markup never leaks into it.

#### `kanban.card`
- **Props:** `id` (auto if null). `draggable="true"`.

### `chart` family

Hand-drawn-feel charts; `currentColor`-driven.

#### `chart.bars`
- **Props:** `values` (array, default `[]`), `labels`, `highlight`, `trend` (bool, default `false`), `height` (default `160`), `label`
- Bar chart with optional highlighter swipe behind one bar and optional dashed trend line.

#### `chart.line`
- **Props:** `values`, `labels`, `height` (default `200`), `label`
- Smooth ink line/area chart; latest point highlighted with accent halo + dashed drop line.

#### `chart.sparkline`
- **Props:** `values`, `label`. Tiny smooth sparkline (200×40); inherits `currentColor`. Decorative unless `label` given.

### `carousel` family

#### `carousel`
- **Props:** `label`. Scroll-snap track with prev/next chevrons and clickable dots; active slide derived from scroll position.

#### `carousel.slide`
- **Props:** `basis` (default `w-full`). Pass a width utility for multi-per-view layouts.

---

## Sections (content-shaped page regions)

Marketing sections with brand-default Danish copy. **Every prop is overridable.**
All render complete with defaults; override what you need. Tag form:
`<sigta:section.hero />`, `<sigta:section.features heading="…" />`, etc.

| Section | Key props (all optional) |
|---|---|
| `section.hero` | `eyebrow`, `heading`, `lede`, `primaryLabel`, `primaryHref`, `target`, `secondaryLabel`, `secondaryHref`, `secondaryTarget`, `stats`, visual slot |
| `section.proof` | `yearsValue`, `yearsLabel`, `logos` |
| `section.recognition` | `heading`, `lede`, `items`, `closingText`, `ctaLabel`, `ctaHref`, `target` |
| `section.features` | `eyebrow`, `heading`, `lede`, `cols` (2/3/4), `items` |
| `section.about` | `eyebrow`, `heading`, `paragraphs`, portrait slot |
| `section.quote` | `quote`, `cite` |
| `section.pricing` | `heading`, `lede`, `tiers` |
| `section.faq` | `heading`, `lede`, `name`, `items` |
| `section.cta` | `heading`, `lede`, `primaryLabel`, `primaryHref`, `target`, `secondaryLabel`, `secondaryHref`, `secondaryTarget`, `annotation` |
| `section.footer` | `tagline`, `signoff`, `columns` |

```blade
<sigta:section.hero heading="We build software that ships." />
<sigta:section.cta target="_blank" />
<sigta:navbar cta-label="Book a call" cta-href="https://calendly.com/sf-sigta/30min" target="_blank">
    <sigta:navlink href="#work">Work</sigta:navlink>
</sigta:navbar>
<sigta:section.faq :items="$faqs" />
```

---

## Cross-component events (cheat sheet)

| Event | Detail | Fired by | Listen in |
|---|---|---|---|
| `sigta-toast` | `{heading, text, variant, duration, link}` | `Sigta::toast()` / your code | toast region |
| `sigta-modal-show` | `{name}` | `modal.trigger` / your code | `modal` |
| `sigta-modal-hide` | `{name}` | `modal.close` / your code | `modal` |
| `sigta-composer-submit` | `{text}` | `composer` | your chat handler |
| `sigta-kanban-move` | `{card, from, to, position}` | `kanban` | your persistence layer |
| `calendar-change` | date | `calendar` | `date-picker` (internal) |
