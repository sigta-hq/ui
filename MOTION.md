# Motion draft — smoothness, loading, press feedback, celebration

> **Status: draft for discussion.** A proposal, not a spec. Nothing in it is built.
> Companion: a live prototype of most effects below, with a reduced-motion toggle:
> https://claude.ai/artifact/4WRZsrVtQQXZd5h4gpeUdw
>
> Everything in `CONVENTIONS.md` still applies (tokens only, `data-sigta`, attribute
> forwarding, a11y, Livewire-morph safety).

## What this is

Sigta UI has a strong static brand: paper, ink, hand-drawn accents. Its *motion* is
thin: a hover lift and a handful of fades. This draft proposes a motion layer in four
parts, matching the ask:

1. **Animations** — transitions and choreography between states (open, close, switch,
   enter, leave, draw).
2. **Loading states** — what the UI does between "you pressed" and "it happened".
3. **Press reactions** — the immediate, physical acknowledgement of a touch.
4. **Celebrations** — what happens when the user finishes something.

Plus the foundations they share: tokens, reduced motion, accessibility, and a rollout
order.

## Where we are today

Audit of the current motion vocabulary, verified against the views on this branch.

| Area | Today |
|---|---|
| Tokens | `--sigta-ease-fast` / `--sigta-ease` / `--sigta-ease-slow` (140 / 180 / 220ms `ease`) exist in `theme.css`, but only `.sigta-prose a` uses them. Components hardcode `transition duration-150 ease-out` and friends (~40 occurrences of `duration-150` across the views). |
| Hover | Lift `-2px` + `shadow-paper` on button, card (`-3px`), kanban card; CTA arrows nudge `3px`; `navlink` underline grows left to right. Good, keep. |
| Press | Nothing. No `active:` state anywhere. A button lifts on hover and does nothing when pressed. |
| Popups | Dropdown, popover, context menu: enter fade + rise 150ms, **no leave** (they blink out). Tooltip: 150ms open delay, no transition either way. |
| Modal / command | Native `<dialog>`, no open or close animation; the backdrop appears instantly. |
| Tabs / segmented / accordion | Colour transitions only. Tab panels swap instantly, the segmented pill jumps, accordion content snaps open (only the `+` rotates). |
| Toast | Enter rise 200ms, leave fade 150ms. Fixed 5s timer, no pause on hover, no progress, no stack reflow when one leaves, no swipe. |
| Loading | `skeleton` (`animate-pulse`) and `progress` (width 300ms, indeterminate sweep). **No `wire:loading` anywhere, no spinner, no button loading state** (`PLAN.md` lists "loading" for Button; it was never built). Page navigation: Livewire's stock progress bar recoloured to `var(--sigta-accent)` in the playground config. |
| Controls | Checkbox and radio: check / dot appear instantly. Switch thumb slides 200ms. File-upload zone tints while a file is dragged over it. |
| Data | Stat, charts, sparkline, elapsed-clock: static (the clock ticks; nothing animates). |
| Kanban | Dragged card at 40% opacity, drop target tints. No reorder motion, no drop settle. |
| Reduced motion | `core.css` kills **all** `animation` and `transition` under `prefers-reduced-motion`. That also freezes skeleton and the progress sweep, so a loading state stops reading as loading. Only the carousel checks the preference in JS. |
| Celebration | None. |

The good news: the brand already has the *ingredients* for distinctive motion (pen
strokes as SVG paths, the highlighter mark, Caveat annotations, squiggle rules, paper
surfaces). Most of this proposal animates what already exists.

## Principles — how Sigta moves

1. **The pen, not the pixel.** Motion imitates a hand with a pen on paper. Strokes draw
   in (`stroke-dashoffset`), highlighter swipes left to right, things settle with a tiny
   overshoot like a pen lifting off. No rubbery bounces, no glossy shimmer, no 3D.
2. **Paper physics.** Surfaces are sheets: they lift (`-2px` + shadow), press down
   (`+1px`, shadow gone), slide in from an edge, fade and rise a few pixels. Dialogs scale
   from `0.98`, never from zero.
3. **Quiet by default, celebratory by exception.** Everyday acknowledgements are
   80–220ms. Celebrations are reserved for things the *user* finished, fire once per
   event, and clean up after themselves.
4. **Accent stays scarce, even in celebration.** Highlighter yellow is a mark, never a
   fill: one swipe, a few confetti bits, one stamp halo. A celebration must leave the
   view with at most one accent mark.
5. **Motion is information.** Direction encodes origin (menus grow from their trigger,
   toasts rise from their corner, drawers slide from their rail). Loading says "heard
   you" within 100ms, "working" after ~200ms, "this is slow" after ~3s.
6. **Reduced motion is a theme, not a kill switch.** Decorative motion goes away;
   essential indicators (spinner, progress, typing) stop moving but stay visibly alive;
   celebrations degrade to their final state. Meaning never depends on motion.
7. **Livewire-morph safe.** Motion lives in CSS state (`:active`, `[data-*]`,
   `@starting-style`) and Alpine `x-transition`; overlays render into one outlet like
   Toast; nothing assumes a DOM node survives a morph unless it is `wire:key`ed.

## Motion tokens (proposal)

Split the current shorthand into durations, easings and distances so Tailwind can bridge
them; keep the three existing names for back-compat; mirror everything in
`theme.example.css`.

```css
/* theme.css — motion */
--sigta-duration-instant: 80ms;     /* press feedback, going down */
--sigta-duration-fast: 140ms;       /* hover, colour, tooltip */
--sigta-duration-base: 180ms;       /* menus, tabs, press release */
--sigta-duration-slow: 220ms;       /* modal, drawer, toast */
--sigta-duration-draw: 600ms;       /* one pen stroke: check, rule, sparkline */
--sigta-duration-celebrate: 1400ms; /* total budget for a celebration incl. exit */

--sigta-ease-out: cubic-bezier(0.2, 0.8, 0.2, 1);      /* arriving */
--sigta-ease-in: cubic-bezier(0.4, 0, 1, 1);           /* leaving */
--sigta-ease-pen: cubic-bezier(0.65, 0, 0.35, 1);      /* a stroke: slow, fast, slow */
--sigta-ease-settle: cubic-bezier(0.34, 1.4, 0.64, 1); /* lands with a little overshoot */

--sigta-lift: -2px;   /* hover */
--sigta-press: 1px;   /* active */
--sigta-rise: 8px;    /* enter offset for popups and toasts */

/* back-compat shorthands, now composed from the above */
--sigta-ease-fast: var(--sigta-duration-fast) var(--sigta-ease-out);
--sigta-ease: var(--sigta-duration-base) var(--sigta-ease-out);
--sigta-ease-slow: var(--sigta-duration-slow) var(--sigta-ease-out);
```

```css
/* core.css — Tailwind bridge additions */
@theme inline {
    --ease-sigta: var(--sigta-ease-out);       /* ease-sigta */
    --ease-pen: var(--sigta-ease-pen);         /* ease-pen */
    --ease-settle: var(--sigta-ease-settle);   /* ease-settle */
    --animate-sigta-draw: sigta-draw var(--sigta-duration-draw) var(--sigta-ease-pen) both;
    --animate-sigta-pen-loop: sigta-pen-loop 900ms linear infinite;
    --animate-sigta-sheen: sigta-sheen 1.6s linear infinite;
}
```

Durations have no Tailwind theme namespace; write `duration-(--sigta-duration-base)`.
Rule of thumb for components: **tokens for anything a theme might want to slow down or
speed up** (a calmer client brand should be able to set `--sigta-duration-base: 240ms`
and have every menu follow). Raw `duration-150` stays allowed for now; whether to extend
the CI token guard to durations is an open question below.

## A. Animations and choreography

Per component: what changes, and how. Durations reference the tokens above.

| Component | Proposed | How |
|---|---|---|
| **Modal** | Panel fades in and rises `8px` from `scale(0.98)` over `slow`; the backdrop fades to `ink/45`. Close reverses in `fast`. The full-screen phone variant slides up `24px` like a sheet. | Pure CSS: `@starting-style` + `transition-behavior: allow-discrete` on `dialog[open]` and `::backdrop` (sketch in the appendix). No JS, unaffected by the `wire:ignore.self` dialog. Browsers without `@starting-style` show it instantly, as today. |
| **Dropdown / Popover / Context menu** | Add the missing **leave** (`fast`, fade + `2px` back toward the trigger). Enter grows from the trigger corner: `transform-origin` follows `position`, `scale(0.98 → 1)`. `top-*` positions rise *up* toward the trigger instead of dropping down. The context menu grows from the pointer. | `x-transition:leave` classes; `origin-top-left` etc. from the existing `match` on `$position`. |
| **Tooltip** | Enter `fast` fade + `2px` rise toward the trigger (direction by `position`), leave `instant` fade. Keep the 150ms open delay, but skip it when another tooltip closed less than 300ms ago, so scanning a toolbar feels continuous. | `x-transition` + a module-level "last closed" timestamp in `sigta.js`. |
| **Tabs** | One **sliding ink underline** travels between tabs (`base`, `ease-sigta`). Panels crossfade: outgoing fades `instant`, incoming fades + rises `4px` over `base`. Opacity and transform only, so no layout jump. | Indicator element positioned from the active tab's `offsetLeft` / `offsetWidth` inside `sigtaTabs` (`$watch('active')` + `resize`). Reduced motion: per-tab border as today. |
| **Segmented** | The ink pill slides between options instead of swapping (`base`); the pressed option sinks `1px`. | Same measuring trick as tabs (options are not equal width); instant fallback. |
| **Accordion** | Content height animates open and closed (`base`); the `+` keeps rotating. Stays zero-JS. | `interpolate-size: allow-keywords` + `transition: height` on `details::details-content` (progressive: browsers without it snap as today). `x-collapse` (bundled with Livewire's Alpine) is the JS fallback if we want it everywhere now. |
| **Toast** | Leave slides *toward its edge* (`8px` + fade) so exits have direction. A hairline **progress rule** along the bottom drains over `duration` and **pauses on hover** (the timer too). Remaining toasts **reflow** smoothly when one leaves. **Swipe to dismiss** on touch. The success icon's check draws in. | Progress: a `rule` squiggle path un-drawing via `stroke-dashoffset`. Reflow: FLIP on the siblings, or `document.startViewTransition` where available. Swipe: pointer events; dismiss past 40% of the width. |
| **Shell sidebar (drawer)** | Already good. Nicety: nav links stagger in `30ms` apart (cap 8). | `style="--i: n"` + `animation-delay: calc(var(--i) * 30ms)`. |
| **Navbar (marketing)** | The sticky bar gains `shadow-paper` after `8px` of scroll. The mobile menu fades + slides down over `base` (today: instant); burger ↔ X crossfade over `fast`. | `x-on:scroll.window.passive`, `x-transition`. |
| **Page navigation (`wire:navigate`)** | Restyle the progress bar as a `2px` **ink line with an accent pen-tip dot** at its end (the sparkline end-dot motif); drop the glow peg. New page content fades + rises `4px` over `fast` on `livewire:navigated`. Optional: `view-transition-name` on `shell.main` so supporting browsers crossfade natively. | `#nprogress .bar` / `.peg` rules in `core.css`; a class toggled on `livewire:navigating` / `livewire:navigated`. |
| **Lists entering and leaving** (table rows, comments, timeline items, kanban cards) | New items fade + rise `6px`; removed items collapse over `fast`. First paint of a list staggers `30ms` per item (cap 8), opt-in. | `@starting-style` on `[data-sigta="table.row"]` etc. for inserts; `wire:transition` (View Transitions) on the container for Livewire removals; a `stagger` prop on `table.rows` / `timeline`. |
| **Kanban** | **Grab**: the card lifts `4px`, gains a stronger shadow, tilts `1.5°`. **Make room**: neighbours slide smoothly as the optimistic `insertBefore` runs (FLIP, ~20 lines on top of the existing rect reduce). **Drop**: the card settles with `ease-settle` (`scale 1.02 → 1`). The column count badge ticks. An empty column shows a faint Caveat "drop here" only while something is being dragged. | FLIP in `over()`; `[data-dragging]` styles; `x-show` on the hint bound to `draggedId`. |
| **Charts** (`line`, `bars`, `sparkline`) | **Draw in** on first view: line strokes draw over `draw` with `ease-pen`, the area fades in after, the end dot and accent halo pop with `ease-settle`. Bars grow from the baseline with a `40ms` stagger. | `pathLength="1"` on the path + `stroke-dasharray: 1; stroke-dashoffset: 1 → 0` in CSS, no measuring. Trigger via `x-intersect.once` (bundled) or a `draw` prop. Static under reduced motion. |
| **Stat** | Opt-in **count-up** from 0 (or from the previous value) over `draw`; `tabular-nums` keeps it steady. Prefix and suffix preserved (`2,418`, `112%`, `3.1d`, `14 år`). The delta arrow nudges once. | Parse the numeric part, format with the same separators, `requestAnimationFrame`, `x-intersect.once`. |
| **Elapsed clock / running timer** | The colon pulses at 1Hz while running (the classic "alive" affordance), paired with a pulsing-dot pill for "recording". On stop, the dot stops and the total gets a highlighter swipe (see D). | `steps(2)` opacity animation on the `:`; essential-motion class (see E). |
| **Skeleton** | New `sheen` variant: a soft diagonal light band sweeps left to right across all placeholders in sync (paper catching the light). Skeleton → content handoff fades the content in over `fast` with a `4px` rise. | `linear-gradient(100deg, …)` at `200%` size, animate `background-position`; same duration and no delay keeps them in sync. Static `bg-line/60` under reduced motion. |
| **Progress** | The fill eases with `ease-sigta` over `400ms`. On reaching 100%: a brief accent tint (`300ms`) then back to ink, and the `%` swaps to a drawn check. | `celebrate` prop, default on (100% is unambiguous). |
| **Landing sections** | Sections fade + rise `8px` as they enter the viewport. `rule` dividers draw in as you scroll past. **The hero `mark` swipes** left to right on first view (the one signature reveal per screen). `annot` settles from `-10°` into its rotation. The hero søkort's dashed route draws itself once. | `x-intersect.once`; `mark::after` `scaleX(0 → 1)` with `transform-origin: left` over `draw`; `pathLength` on the SVGs. |
| **Composer** (chat) | A new message bubble rises and fades in; the send icon flicks right `4px` and fades on submit; the textarea shrinks back smoothly. | `x-transition`; `transition: height` over `fast` on the textarea. |
| **Command palette** | The dialog enters from the top (`-8px`) over `base`. Filtering stays instant (keyboard responsiveness); the "Nothing found" state waits `120ms` so it doesn't flash between keystrokes. | Modal CSS + `x-transition.delay`. |
| **Editor (inline variant)** | The toolbar slides down `6px` and fades in when editing starts. | `x-transition`. |
| **Calendar / date-picker** | The month grid slides `±12px` with a crossfade in the direction of navigation; the selected day pops with `ease-settle`. | `x-transition` keyed on a `direction` flag. |
| **Assignee picker / avatar** | On assign, the avatar pops in (`scale 0.8 → 1`, `ease-settle`); the dashed "unassigned" circle crossfades out. | `@starting-style` on the avatar. |
| **Money / duration input** | On commit, the parsed display value gets a `300ms` highlighter tint ("I read 1h30m"). Garbage input: a `2px` horizontal shake × 3 in danger (off under reduced motion). | `data-state="committed"` for 300ms inside the existing `commit()`. |

## B. Loading states

### The ladder

What happens after a press, by elapsed time. Every loading pattern below follows this.

| Time | The UI | Mechanism |
|---|---|---|
| 0–100ms | Press feedback only (section C). Nothing else. | CSS `:active`. |
| 100–200ms | Still nothing new. A spinner here flickers on fast requests. | `wire:loading.delay` (200ms). |
| ≥200ms | A local indicator appears *where the action happened*: button spinner, field "saving…", table dim. | `wire:loading` + `wire:target`. |
| ≥300ms visible | Once shown, an indicator stays at least 300ms even if the request finishes (no one-frame flashes). | A small minimum-display helper in `sigta.js` (nice-to-have). |
| ≥3s | Reassurance copy in mono graphite ("Still working — large report."). Never block unrelated UI. | `wire:loading.delay.longest` (1s) on a second element, or a timer. |
| Done | The indicator resolves into a **drawn check** that holds ~800ms before returning to idle, so even instant saves are visibly confirmed. Errors resolve into danger copy, never a silent revert. | `sigta-done` event (below). |

### Patterns

| Pattern | Proposed | How |
|---|---|---|
| **Button `loading`** | New prop. The label stays visible and in place; the spinner takes the leading icon's slot, or animates a slot open at the leading edge so nothing jumps; the label dims to 60%; `aria-busy="true"`, `pointer-events-none`, `cursor: progress`. Keeps focus (not `disabled`). `<sigta:button wire:click="save" loading>` auto-wires `wire:loading.attr="aria-busy" wire:target="save"`; `:loading="$saving"` sets it from the server. | Class hooks on `aria-busy`; the `wire:loading` shorthand is generated in `button.blade.php` when `loading` is boolean-true and a `wire:click` exists. |
| **Button success** | After the request, the arc closes into a check (`draw`), holds `800ms`, reverts. | `Sigta::done('save')` → `sigta-done {target}`; buttons whose `wire:target` matches flash. Alpine: `$sigta.done('save')`. |
| **`<sigta:spinner>`** | New element: the **pen loop**, a 3/4 ink arc with round caps rotating at `900ms` while its length breathes (a hand tracing a loop). Sizes match `icon`. `currentColor`. `label` → `role="status"` + sr-only text, else `aria-hidden`. Reduced motion: no rotation, a gentle opacity pulse. | Inline SVG, `stroke-dasharray` keyframes + rotate; `sigta-essential` class (E). |
| **Field autosave** | For `wire:model.live.blur` forms the label row shows mono microcopy: "saving…" with a tiny pen loop → "saved ✓" (the check draws, holds 1.2s, fades). Unsaved edits show a small accent-line dot by the label. Errors keep the existing danger text. | `<sigta:field status="saving|saved|dirty|error">` prop plus a documented `wire:loading` / `wire:dirty` recipe. |
| **Table / list refetch** | Sorting, paging and filtering **keep the rows visible**, dim them to 60%, and run a hairline indeterminate sweep under the header. The clicked column header shows a small spinner in place of its sort arrow. Skeleton rows are for **first load only**. | `wire:loading.class="opacity-60 pointer-events-none"` on `table.rows`; `<sigta:table.skeleton :rows :cols>` renders matching skeleton rows. |
| **Panel / card busy** | Content dims to 60% and a centred pen loop appears after the delay. | `busy` prop or `wire:loading.class`. |
| **App-level bar** | A `2px` ink line at the top of `shell.main` for slow Livewire actions only (`.delay.long`). Styled like the navigate bar so there is exactly one "the app is working" signal. | `wire:loading.delay.long` on a root-level element. |
| **File upload** | Per-file `progress` with `%` driven by Livewire's upload events; finish → the size text swaps to a drawn check; error → the row shakes `2px` and turns danger with a retry link. While a file hovers over the zone the dashed border **marches** slowly (receptive); on drop the zone settles `0.99 → 1`. | `livewire-upload-progress` / `-finish` / `-error` listeners in the component's `x-data`; animated `background-position` on a repeating-gradient border. |
| **Optimistic UI** (kanban, toggles, assignee) | Move instantly; show a quiet pending state (85% opacity, pen loop in the corner) until the round-trip confirms; on failure animate back (FLIP) and toast danger. Documented as *the* pattern for drag and toggle interactions. | The existing kanban optimistic move + `wire:loading` on the card. |
| **Skeleton → content** | Content fades in over `fast` with a `4px` rise, staggered `30ms` per item (cap 8). | `@starting-style` on the content wrapper; `--i` on items. |
| **Empty states** | A hand-drawn `currentColor` illustration draws in once; the copy is a Caveat annotation pointing at the primary action ("nothing here yet ↘"). | New `<sigta:empty>` component; `pathLength` draw. |
| **Offline** | A slim ink banner slides down: "Offline — changes send when you're back"; slides up on reconnect, followed by a success toast. | `wire:offline` + `x-transition`. |
| **Streaming (chat)** | Promote the chat page's three dots to `<sigta:typing>` (ink dots, "pen tapping"). Streamed text ends in a blinking ink caret `▍` while `wire:stream` is active; the brand glyph pulses gently while thinking. | `steps(2)` opacity on the caret; essential class. |

## C. Press reactions

Every interactive element responds on `pointerdown`, within one frame, physically (sink or
ink) rather than with colour alone. Down is `instant` with `ease-in`; back up is `base`
with `ease-sigta`.

| Element | Proposed | How |
|---|---|---|
| **Button** (all variants) | Press: sinks from the hover lift to `+1px` (a `3px` travel), the shadow goes, the fill darkens 4–8% (`active:bg-ink/90` primary, `active:bg-ink/8` secondary and ghost). Space and Enter trigger `:active` too. | Add `active:` utilities to `$classes` in `button.blade.php`. CSS only. |
| **Ink bloom** (opt-in) | A soft radial ink spot expands from the pointer under the label and fades as it grows, `350ms`, max 12% opacity (ink soaking into paper). Try on `primary` / `cta` only; off under reduced motion. | `data-sigta-ink` handled in `sigta.js`: set `--x` / `--y` on `pointerdown`, toggle a class; `::before` radial-gradient; `overflow: hidden`. |
| **Checkbox** | The check **draws** (`200ms`, `ease-pen`) and the box pops `1 → 1.08 → 1` (`ease-settle`). Unchecking clears instantly (undoing isn't celebrated). Indeterminate draws a dash. | The check becomes an inline SVG sibling with `pathLength="1"` driven by `peer-checked`; the bare variant gains a `relative inline-flex` wrapper (the label variant already has one). Attributes still forward onto the input. |
| **Radio** | The dot pops in `0 → 1` with `ease-settle` (`base`); the previously selected dot shrinks over `instant`. | `transform` on `::after`. |
| **Switch** | Squash and stretch: while pressed the thumb widens to `22px` toward the direction of travel, then slides and settles back to `18px` (`ease-settle`). The track colour crossfades as today. | `active:after:w-[22px]` + `ease-settle` on `after:`. |
| **Segmented** | The pill slides (A) and the pressed option sinks `1px`. | `active:translate-y-px`. |
| **Icon / ghost buttons** | Quick pop: `scale 0.92` on press, back to `1` with `ease-settle`. | `active:scale-[0.92]`. |
| **Copy to clipboard** | Promote the kit page's "copy → copied" to `<sigta:copy>`: the icon crossfades to a drawn check and mono "copied" shows for 1.4s. | New element on `sigta.js`. |
| **Menu / command items** | Stay crisp. Add `active:bg-ink/10` only. | CSS. |
| **Tabs** | The pressed tab label sinks `1px`; the indicator glides. | CSS + A. |
| **Kanban card** | Grab: lift `4px`, tilt `1.5°`, `cursor-grabbing`; release: settle. | `[data-dragging]` + `active:`. |
| **Slider** | The thumb grows to `22px` while dragging; an ink value bubble rises above the thumb and follows it during the drag. | `:active` on the thumb pseudo; bubble position `calc((value - min) / (max - min) * 100%)` when `showValue`. |
| **Pagination / links** | Sink `1px`; `active:text-ink`. | CSS. |
| **Accordion summary** | The `+` scales to `0.9` briefly on press; rotates on open as today. | `active:after:scale-90`. |
| **Form submit via keyboard** | The form's submit button flashes its pressed state for `120ms` when Enter submits from an input, so it's visible *which* button acted. | `x-on:submit` toggles `data-pressed` on `button[type=submit]`. |
| **Composer send** | The send icon flicks right and the button pops. | `x-transition` on the icon. |
| **Card (clickable)** | Press flattens the lift to `0` and the shadow to none (paper pressed onto the desk). | `active:translate-y-0 active:shadow-none`. |
| **Shortcut hints** | When ⌘K (or any shown shortcut) is pressed, its `kbd` badge flashes pressed for `100ms`. | A global keydown listener matches `kbd[data-key]`. |
| **Haptics / sound** | No sound. `navigator.vibrate(8)` on confirmations is Android-only; propose **off by default**, opt-in per app (`Sigta.haptics = true`). | Open question. |

## D. Celebrations

Sigta is a studio brand and a B2B SaaS foundation. Celebrations should feel like **a
teacher's pen on a finished worksheet**, not a casino: a drawn check, a highlighter
swipe, a scribbled "done", a stamp. The library provides the vocabulary; each app
decides what deserves which tier.

### Vocabulary

| Effect | What it looks like | Duration | Use for |
|---|---|---|---|
| `check` | A hand-drawn tick draws itself; the box pops `1.08`. | 200–300ms | Any unit of work done; a confirmed save. |
| `strike` | A pencil line scribbles through the completed title left to right (slight wobble); the title fades to graphite. | 300ms | A task marked done in a list. |
| `swipe` | A highlighter `mark` sweeps across a word or row left to right, then fades out after ~1.5s (the view returns to at most one accent). | 400ms + hold | The value that just changed: a stopped timer's total, a stat that hit target, a saved row. |
| `note` | A Caveat annotation ("Done.", "All clear.", "Sådan!") fades in with a rotation settle (`-10° → -6°`), holds 1.6s, fades. | ~2.2s total | Finishing a set: all subtasks, a cleared column, a completed checklist. |
| `circle` | A hand-drawn circle draws itself around a number or element (a teacher circling the right answer). | 500ms | A goal reached: hours target, 100%, zero left. |
| `underline` | A squiggle `rule` draws under a heading. | 400ms | An onboarding step or section completed. |
| `stamp` | A mono uppercase rubber stamp ("DONE", "SENT", "PAID"), `1.5px` ink border, rotated `-8°`, lands from `scale 1.6` with a 1px ink-bleed blur clearing, `ease-settle`. Can persist as a badge state. | 260ms + hold | Documents changing state: invoice sent or paid, report published, project archived. |
| `confetti` | 24–40 **torn paper bits** (4–8px; ink / raised paper with a hairline / line / at most 20% accent) burst from the trigger, fall with gravity and drift, rotate, fade by 1.4s. The only effect that leaves its local context. | 1.4s | Milestones only. |
| `nod` | The brand glyph nods (`±4°`, 400ms). | 400ms | A playful acknowledgement on the chat surface when a job finishes. |

### Tiers (recommendation for the apps)

| Tier | When | Effects | Budget |
|---|---|---|---|
| **0 Acknowledge** | Every action | Press feedback (C) + `check` on success | ≤300ms, silent, no copy |
| **1 Micro** | One unit of work done: task done, timer stopped, upload finished, assignee set | `check` + `strike` (lists) or `check` + `swipe` (a value) | ≤500ms, local, no copy |
| **2 Set complete** | A group is done: all subtasks, a column emptied, the day's hours reached, a checklist completed | Tier 1 + `note`, optionally `circle` on the number that hit 0 or 100% | ≤1.6s, local, one line of copy |
| **3 Milestone** | Rare and meaningful: project completed or archived, invoice sent or paid, first task ever, month closed, onboarding finished, a fun counter (100th hour) | `stamp` or `confetti` + a success toast with a heading | ≤1.4s + toast; once per milestone, never twice a day for the same kind |

**Never celebrate:** the system's own work (a deploy finishing is a toast), deletions,
undo or redo, repeated saves, 50 rows at once (batch → one celebration), while the user
is typing (defer until input idles 500ms), or with two celebrations on screen.

### API sketch

- **Outlet:** `<sigta:celebration />` once per layout (like Toast): a fixed `aria-hidden`
  layer that hosts confetti, stamps and notes so page morphs never tear them down. With
  `wire:navigate`, wrap it in `@persist`.
- **Livewire:** `Sigta::celebrate('confetti')`,
  `Sigta::celebrate('stamp', text: 'SENT', target: '#invoice-123')`. Low-level:
  `$this->dispatch('sigta-celebrate', effect: 'note', text: 'Done.', target: '…')`.
- **Alpine / JS:** `$sigta.celebrate('note', { target: $el, text: 'Done.' })`,
  `window.Sigta.celebrate(...)`.
- **Payload:** `sigta-celebrate {effect, target?, text?, tone?, hold?}`. `target` is a
  selector or element; effects anchor to its `getBoundingClientRect()`; if it is gone
  (morphed away) they fall back to the viewport centre or skip.
- **Component opt-ins:** `<sigta:checkbox celebrate>` strikes its label on check;
  `<sigta:progress celebrate>` (default on) flashes at 100%; `<sigta:stat :goal="…">`
  circles the value when reached; `<sigta:kanban.column>` shows "nothing left — nice."
  when it empties during a session.
- **Copy:** English defaults, overridable per app (`Sigta.config.celebrate.copy`), with a
  Danish set shipped (`Færdig.`, `Sådan!`, `Alt klaret.`). Open question below.

### Rules

- Celebrations are `aria-hidden`; meaning must already be carried by the state change
  (the checkbox is checked, the toast has `role="status"`). They never move focus, never
  trap, never block input, and dismiss on navigation.
- `transform` / `opacity` only; confetti is at most 40 pooled nodes removed on
  `animationend`.
- Reduced motion mapping: `check` / `strike` / `swipe` / `underline` / `circle` → the
  final state instantly; `note` / `stamp` → fade only; `confetti` → `note`.

## E. Reduced motion, accessibility, performance, Livewire

- **Replace the kill switch with a policy.** Keep the global rule in `core.css` for
  decorative motion and add one exception class, `sigta-essential`, for indicators whose
  *presence* is the information (spinner, indeterminate progress, typing dots, streaming
  caret). Under `prefers-reduced-motion` those switch to a non-vestibular opacity pulse
  instead of stopping dead:

  ```css
  @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; transition: none !important; }
      .sigta-essential { animation: sigta-breathe 1.2s ease-in-out infinite !important; }
  }
  ```
- Motion never carries meaning alone: every animated state has a static equivalent
  (checked, `aria-busy`, `aria-current`, text).
- No parallax, no large-distance motion; shakes are at most `2px` and off under reduced
  motion.
- Livewire: state in attributes and Alpine; overlays in outlets; `wire:key` anything
  that must animate across a morph; `wire:transition` (View Transitions) is the tool
  for Livewire-driven list changes.
- Performance: composite-only properties, `will-change` only for the duration of an
  effect, a single `IntersectionObserver` per page for intersect-triggered draws.

## F. Rollout

Each phase follows the component Definition of done in `CONVENTIONS.md` (view, tests,
kit page, stubs, CI green). Add to that: a **`motion` kit page** under the Docs tier
showing the principles, tokens, the ladder and every effect live with a reduced-motion
toggle (it doubles as the motion regression surface), and a **"Motion" section in
`stubs/sigta-ui.md`** so consumer LLMs use the tokens and the loading pattern instead of
inventing their own.

| Phase | Scope | Kind | Rough effort |
|---|---|---|---|
| **1 Feel** | Tokens + Tailwind bridge; button press states; popup leave + origin; tooltip fade; modal `@starting-style`; toast leave direction; tabs panel crossfade; switch squash; radio pop; checkbox draw; reduced-motion policy; navigate bar restyle. No API changes. | CSS + small view edits | 1–2 days |
| **2 Loading** | `<sigta:spinner>`; button `loading` + `sigta-done`; field `status`; `table.skeleton` + refetch dim; panel `busy`; file-upload progress; offline banner; `<sigta:typing>`; the ladder documented in the stub. | New elements + recipes | 3–4 days |
| **3 Celebrate** | `<sigta:celebration>` outlet; `Sigta::celebrate` / `$sigta.celebrate`; `check`, `strike`, `swipe`, `note`, `stamp`, `confetti`, `circle`, `underline`; progress 100%; kanban column-clear; checkbox `celebrate`; kit page. | Runtime + Blade | 3–5 days |
| **4 Choreography** | Sliding indicators (tabs, segmented); accordion height; toast progress / swipe / reflow; kanban FLIP + tilt; chart draw-in; stat count-up; calendar slide; section reveals + hero mark sweep + søkort draw; list stagger; view transitions. | Per product need | ongoing |

Testing: Pest render tests assert the hooks (`aria-busy`, `data-sigta="spinner"`, the
`active:` classes, `sigta-celebrate` dispatched from `Sigta::celebrate`). Alpine behaviour
(indicator position, the celebration outlet) goes through Pest browser tests when they
land; eyeballs on the kit page until then.

## Open questions

1. **Which events count as "completing something"** in the apps built on this kit? The
   tiering needs the real list (task done, timer stop, invoice paid, …).
2. **How loud may Tier 3 be** in a B2B tool: confetti at all, or stamp + toast only?
3. **Copy language** for notes and stamps: English default with a Danish set, or Danish
   first?
4. **Ink bloom** on primary buttons: try it, or keep press feedback purely physical?
5. **Haptics** as an opt-in?
6. **Reduced-motion policy**: OK to keep essential indicators breathing instead of frozen?
7. **Enforce motion tokens in CI** like colours (fail on raw `duration-*`), or keep it a
   convention?
8. **Accordion**: accept the progressive CSS approach (snaps in older browsers) or ship
   `x-collapse` now?

## Appendix — technique sketches

### Modal open and close without JS

```css
/* core.css — the dialog is a sheet that rises onto the paper */
[data-sigta="modal"] > dialog {
    opacity: 0;
    translate: 0 var(--sigta-rise);
    scale: 0.98;
    transition:
        opacity var(--sigta-duration-fast) var(--sigta-ease-in),
        translate var(--sigta-duration-fast) var(--sigta-ease-in),
        scale var(--sigta-duration-fast) var(--sigta-ease-in),
        overlay var(--sigta-duration-fast) allow-discrete,
        display var(--sigta-duration-fast) allow-discrete;
}
[data-sigta="modal"] > dialog[open] {
    opacity: 1;
    translate: 0 0;
    scale: 1;
    transition-duration: var(--sigta-duration-slow);
    transition-timing-function: var(--sigta-ease-out);
}
@starting-style {
    [data-sigta="modal"] > dialog[open] { opacity: 0; translate: 0 var(--sigta-rise); scale: 0.98; }
}
[data-sigta="modal"] > dialog::backdrop {
    background: transparent;
    transition: background var(--sigta-duration-slow), overlay var(--sigta-duration-slow) allow-discrete, display var(--sigta-duration-slow) allow-discrete;
}
[data-sigta="modal"] > dialog[open]::backdrop {
    background: color-mix(in srgb, var(--sigta-fg) 45%, transparent);
}
@starting-style {
    [data-sigta="modal"] > dialog[open]::backdrop { background: transparent; }
}
```

### The check that draws itself

```blade
{{-- checkbox.blade.php (bare variant) — the input keeps every forwarded attribute --}}
<span class="relative inline-flex">
    <input type="checkbox" {{ $attributes->merge(['class' => $classes.' peer']) }} />
    <svg class="pointer-events-none absolute inset-0 text-surface [--draw:1] peer-checked:[--draw:0]"
         viewBox="0 0 18 18" aria-hidden="true">
        <path d="M4.5 9.5 L7.6 12.4 L13.6 5.8" pathLength="1" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              style="stroke-dasharray: 1; stroke-dashoffset: var(--draw); transition: stroke-dashoffset var(--sigta-duration-base) var(--sigta-ease-pen)" />
    </svg>
</span>
```

`pathLength="1"` normalises the path, so the same CSS draws a check, a squiggle rule, a
sparkline or a circle: `stroke-dasharray: 1; stroke-dashoffset: 1 → 0`.

### The pen loop (spinner)

```html
<svg data-sigta="spinner" class="sigta-essential size-4 animate-sigta-pen-loop" viewBox="0 0 20 20" fill="none" aria-hidden="true">
    <circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
            class="animate-[sigta-pen-dash_1.4s_ease-in-out_infinite]" />
</svg>
```

```css
@keyframes sigta-pen-loop { to { rotate: 360deg; } }
@keyframes sigta-pen-dash {            /* circumference ≈ 47 */
    0%   { stroke-dasharray: 6 41;  stroke-dashoffset: 0; }
    50%  { stroke-dasharray: 30 41; stroke-dashoffset: -8; }
    100% { stroke-dasharray: 6 41;  stroke-dashoffset: -47; }
}
@keyframes sigta-breathe { 50% { opacity: 0.35; } }
```

### Paper confetti (spec)

- 24–40 `<span>` pieces in the celebration outlet, pooled and reused.
- Sizes 4–8px, `rounded-[1px]`, random rotation; colours `--sigta-fg` (40%),
  `--sigta-surface-raised` with a hairline `--sigta-fg` border (25%), `--sigta-line`
  (20%), `--sigta-accent` (15%, never more).
- Each piece gets custom properties: `--dx` (−140…140px), `--peak` (−40…−120px),
  `--fall` (180…320px), `--rot` (−540…540deg), `--delay` (0…120ms).
- One keyframe: `0% { translate: 0 0 }`, `35% { translate: calc(var(--dx) * .5) var(--peak) }`
  (ease-out), `100% { translate: var(--dx) var(--fall); opacity: 0; rotate: var(--rot) }`
  (ease-in). Duration `--sigta-duration-celebrate`.
- Origin is the trigger's rect (or the viewport centre); the layer is `pointer-events-none`
  and `aria-hidden`.
- Reduced motion → the effect becomes a `note`.

### Celebrate payload

```php
Sigta::celebrate('stamp', text: 'PAID', target: '#invoice-4021 [data-stamp-anchor]');
// dispatches:
// sigta-celebrate { effect: 'stamp', text: 'PAID', target: '#invoice-4021 [data-stamp-anchor]', tone: 'ink', hold: 1600 }
```
