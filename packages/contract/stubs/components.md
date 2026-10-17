<!-- Source of both editions of sigta-components.md (the component reference
     an LLM agent scans). The component entries are generated from the
     manifests at the @components marker; the rest is this template, with
     "@livewire" / "@svelte" … "@end" blocks per edition and {{tag:name}}. -->
# Sigta UI — Component Reference

<!-- @livewire -->
> Published into your project by `php artisan sigta:install`. Overwritten on
> every run — never hand-edit it here. This is the exhaustive API surface an LLM
> agent scans to pick the right component. Read the rules in `sigta-ui.md` first.

Every component is an anonymous Blade component. Either tag form works:

```blade
<sigta:button variant="cta">Save</sigta:button>      {{-- preferred terse form --}}
<x-sigta::button variant="cta">Save</x-sigta::button>  {{-- canonical form --}}
```

Subcomponents use a dot: `<sigta:dropdown.item>`, `<sigta:tabs.panel>`.
Multi-word props are written kebab-case (`primary-label="…"`); bind PHP values
with a colon (`:items="$faqs"`). Unknown attributes (`wire:model`, `x-*`, `id`,
`aria-*`, `name`) always forward to the native element. Props marked
**required** have no default.
<!-- @end -->
<!-- @svelte -->
> Published into your project by `npx sigta install`. Overwritten on every run —
> never hand-edit it here. This is the exhaustive API surface an LLM agent scans
> to pick the right component. Read the rules in `sigta-ui.md` first.

Every component is imported from `@sigta-hq/svelte`. A dotted name is a member
of its family (`<Dropdown.Item>`, `<Tabs.Panel>`, `<Section.Hero>`): import the
family (`import { Dropdown } from '@sigta-hq/svelte'`).

Two-way props are bindable (`bind:value`), events are callback props
(`onchange`), named slots are snippets of the same name and the default slot
is `children` (prose says "slot" for both). Unknown attributes
(`name`, `id`, `aria-*`, `data-*`) forward to the native element. Props marked
**required** have no default.

Prose names other components by their contract name, which maps to the import
mechanically: `dropdown.item` is `<Dropdown.Item>`, `save-state` is
`<SaveState>`, `tile-grid.tile` is `<TileGrid.Tile>`.
<!-- @end -->

A *family* heading groups the components one kit page shows together (it is
not always a namespace: {{tag:duration}} sits with {{tag:elapsed-clock}}). Each
entry gives the props with their types and defaults, then slots, events,
accessibility and usage notes, and an example.

<!-- @components -->

---

## Motion (behaviours, not components)

Ink on paper: strokes draw, paper settles under a press, a finished thing is crossed out and folds away. Every motion decorates state the markup already carries (text, ARIA, data attributes) and stops under `prefers-reduced-motion` with nothing lost — the work (a commit, a removal) still happens.

<!-- @livewire -->
- **`data-sigta-motion`** on a list (rows keyed with `wire:key`): a row the server drops folds away (slides aside, then the gap closes) instead of vanishing; a row that arrives unfolds with a brief highlighter wash. Works for Livewire morphs, Alpine `x-for` and plain DOM inserts. Keep the list element rendered when it empties (render the empty state beside it, not inside it) so its last row can fold away.
- **`$sigta.complete(el, commit, options)`** — the moment an item is done: its checkbox ticks, a burst flicks out, every `[data-sigta-strike]` in the row is crossed out with a pen line, drawn through every line its text wraps onto, then the row folds away. `commit` does the real work (`() => $wire.complete(id)`, an array filter, a fetch); if it throws or rejects the row is restored and shakes. Inside Livewire the commit goes out at once and the row leaves only if the server drops it (a restored row, e.g. after Undo, stays); elsewhere the row folds first, then `commit` runs. Options: `stamp: 'Done'` presses a stamp onto the row; `leave: false` keeps it in place, crossed out; `defer: true` folds first and commits after even inside Livewire (for an Alpine `x-for` list within a component). The row is the closest `[data-sigta-row]`, `[data-sigta-motion] > *` or `[wire:key]`.
- **`data-sigta-done`** on a row (set by `complete`, or rendered by your server for finished items) crosses out its `[data-sigta-strike]` text and greys it; a row rendered done shows its line without drawing it again. Put `data-sigta-strike` on an inline element that holds only text, such as a `<span>` around the title: a block or flex item gets one line for all its lines, and a colour utility on the same element outranks the greying.
- **`$sigta.leave(el)`** folds any element away and removes it (`{ remove: false }` leaves removal to your framework).
- **`$sigta.celebrate(el, level, { text })`** — `small`: a burst of ink strokes; `medium`: a hand-drawn pen ring and a handwritten note (default "nice!"); `big`: paper confetti and a note (default "well done!"). Pass `text: false` for no note. Also `$sigta.burst(el)`, `$sigta.ring(el)`, `$sigta.note(el, 'saved!')`, `$sigta.confetti(el?)`. From Livewire: `Sigta::celebrate('big', text: 'Inbox zero!', target: '#inbox')`, with `text: false` for no note. Big is for milestones (inbox zero, a finished import), never every click.
- **`animate-sigta-settle`** — content lands like ink soaking into paper (fade, rise, unblur). Put it on content that replaces a skeleton; stagger siblings with `[animation-delay:70ms]`.
- **Easing utilities:** `ease-sigta-out` (arriving), `ease-sigta-pen` (drawn strokes), `ease-sigta-spring` (press release, stamps). Tokens: `--sigta-curve-out`, `--sigta-curve-pen`, `--sigta-curve-spring`, `--sigta-dur-press`, `--sigta-dur-draw`, `--sigta-dur-leave`, `--sigta-dur-loop`.
```blade
<ul data-sigta-motion id="inbox">
    @foreach ($tasks as $task)
        <li wire:key="task-{{ $task->id }}" class="flex items-center gap-3">
            <sigta:checkbox x-on:change="$sigta.complete($el, () => $wire.complete({{ $task->id }}))" />
            <span data-sigta-strike>{{ $task->title }}</span>
        </li>
    @endforeach
</ul>
```
```php
public function complete(int $id): void
{
    Task::find($id)->markDone();
    Sigta::toast('Marked done', variant: 'success', key: 'tasks',
        action: ['text' => 'Undo', 'event' => 'task-reopen', 'detail' => ['id' => $id]]);
    if ($this->tasks->isEmpty()) Sigta::celebrate('big', text: 'Inbox zero!', target: '#inbox');
}
```
<!-- @end -->
<!-- @svelte -->
- **`data-sigta-motion`** on a list: a row added after the page settled unfolds with a brief highlighter wash. Rows that leave the data fold away when they carry `out:leave` (`import { leave } from '@sigta-hq/svelte'`) inside a keyed `{#each}`. Keep the list element rendered when it empties (render the empty state beside it, not inside it) so its last row can fold away.
- **`sigta.complete(el, commit, options)`** — the moment an item is done: its checkbox ticks, a burst flicks out, every `[data-sigta-strike]` in the row is crossed out with a pen line, then the row folds away and `commit` runs (remove the item from your list, call your API). If `commit` throws or rejects, the row is restored and shakes. Options: `stamp: 'Done'` presses a stamp onto the row; `leave: false` keeps it in place, crossed out; `atOnce: true` commits first and lets your data (and `out:leave`) remove the row. The row is the closest `[data-sigta-row]` or `[data-sigta-motion] > *`.
- **`data-sigta-done`** on a row (set by `complete`, or rendered for finished items) crosses out its `[data-sigta-strike]` text and greys it; a row rendered done shows its line without drawing it again. Put `data-sigta-strike` on an inline element that holds only text, such as a `<span>` around the title.
- **`sigta.leave(el)`** folds any element away and removes it (`{ remove: false }` leaves removal to Svelte); **`out:leave` / `in:arrive`** are the same motion as Svelte transitions.
- **`sigta.celebrate(el, level, { text })`** — `small`: a burst of ink strokes; `medium`: a hand-drawn pen ring and a handwritten note (default "nice!"); `big`: paper confetti and a note (default "well done!"). Pass `text: false` for no note. Also `sigta.burst(el)`, `sigta.ring(el)`, `sigta.note(el, 'saved!')`, `sigta.confetti(el?)`. Big is for milestones (inbox zero, a finished import), never every click.
- **`animate-sigta-settle`** — content lands like ink soaking into paper (fade, rise, unblur). Put it on content that replaces a skeleton; stagger siblings with `[animation-delay:70ms]`.
- **Easing utilities:** `ease-sigta-out` (arriving), `ease-sigta-pen` (drawn strokes), `ease-sigta-spring` (press release, stamps). Tokens: `--sigta-curve-out`, `--sigta-curve-pen`, `--sigta-curve-spring`, `--sigta-dur-press`, `--sigta-dur-draw`, `--sigta-dur-leave`, `--sigta-dur-loop`.
```svelte
<script>
    import { Checkbox, sigta, leave } from '@sigta-hq/svelte';

    let { tasks = $bindable() } = $props();
    const done = (task) => () => {
        tasks = tasks.filter((t) => t.id !== task.id);
        sigta.toast('Marked done', { variant: 'success', key: 'tasks', action: { text: 'Undo', onClick: () => (tasks = [...tasks, task]) } });
    };
</script>

<ul data-sigta-motion id="inbox">
    {#each tasks as task (task.id)}
        <li class="flex items-center gap-3" out:leave>
            <Checkbox aria-label={`Done: ${task.title}`} onchange={(e) => sigta.complete(e.currentTarget, done(task))} />
            <span data-sigta-strike>{task.title}</span>
        </li>
    {/each}
</ul>
```
<!-- @end -->

---

## Cross-component events (cheat sheet)

<!-- @livewire -->
| Event | Detail | Fired by | Listen in |
|---|---|---|---|
| `sigta-toast` | `{heading, text, variant, duration, link, action, key}` | `Sigta::toast()` / your code | toast region |
| `sigta-celebrate` | `{level, text, target}` | `Sigta::celebrate()` / your code | the runtime |
| `sigta-save-state` | `{name, state}` | `$sigta.save()` / `$sigta.saveState()` / your code | `save-state` with that `name` |
| `sigta-modal-show` | `{name}` | `modal.trigger` / your code | `modal`, `command` |
| `sigta-modal-hide` | `{name}` | `modal.close` / your code | `modal`, `command` |

Component events — custom DOM events; listen on the component (`x-on:sigta-kanban-move="…"`) or on the window (`x-on:sigta-kanban-move.window="…"`):

| Event | Detail | Component | What it means |
|---|---|---|---|
<!-- @end -->
<!-- @svelte -->
| Event | Detail | Fired by | Listen in |
|---|---|---|---|
| `sigta-toast` | `{heading, text, variant, duration, link, action, key}` | `sigta.toast()` / your code / Inertia `sigta.toasts` | `<Toast />` |
| `sigta-celebrate` | `{level, text, target}` | `sigta.celebrate()` / your code / Inertia `sigta.celebrate` | the runtime |
| `sigta-save-state` | `{name, state}` | `sigta.save()` / `sigta.saveState()` / your code | `<SaveState name>` |
| `sigta-modal-show` | `{name}` | `<Modal.Trigger>` / your code | `<Modal name>`, `<Command name>` |
| `sigta-modal-hide` | `{name}` | `<Modal.Close>` / your code | `<Modal name>`, `<Command name>` |

Component callbacks — a component's own events are callback props (the
components that also dispatch a DOM event say so in their entry, and say when
the callback receives less than the event's detail):

| Callback prop | Detail | Component | What it means |
|---|---|---|---|
<!-- @end -->
<!-- @events -->
