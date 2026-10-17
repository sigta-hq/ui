---
title: Motion
order: 3
---

# Motion — ink on paper

Waits draw (the compass loader, never a spinner), a finished thing is ticked,
crossed out and folds away, a milestone gets a flourish. Motion decorates
state the markup already carries, so reduced motion loses nothing. The motion
itself is one implementation shared by both runtimes.

## A loader never moves

Keep a loader rendered and change its `state`; let the kit hold the space
(save state, busy button, job). See `sigta-ui.md`, MUST 5.

## Completing a row

<!-- @livewire -->
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

Inside Livewire the commit goes out at once and the row folds away when the
server drops it; a row the server keeps (an Undo) stays.
<!-- @end -->
<!-- @svelte -->
```svelte
<script>
    import { Checkbox, sigta, leave } from '@sigta-hq/svelte';
    let tasks = $state([...]);
</script>

<ul data-sigta-motion id="inbox">
    {#each tasks as task (task.id)}
        <li class="flex items-center gap-3" out:leave>
            <Checkbox aria-label={`Done: ${task.title}`}
                onchange={(e) => sigta.complete(e.currentTarget, () => (tasks = tasks.filter((t) => t.id !== task.id)))} />
            <span data-sigta-strike>{task.title}</span>
        </li>
    {/each}
</ul>
```

The row ticks, is crossed out and folds; then `commit` runs and the item
leaves your list. Rows that leave the data any other way fold too, through
`out:leave`.
<!-- @end -->

## Celebrating

<!-- @livewire -->
`$sigta.celebrate(el, 'small' | 'medium' | 'big', { text })` in Alpine,
`Sigta::celebrate('big', text: 'Inbox zero!', target: '#inbox')` from PHP.
<!-- @end -->
<!-- @svelte -->
`sigta.celebrate(el, 'small' | 'medium' | 'big', { text })`, or a
`sigta.celebrate` page prop through the Inertia bridge.
<!-- @end -->
Big is for milestones (inbox zero, a finished import), never every click.

## Easing and timing

`ease-sigta-out`, `ease-sigta-pen`, `ease-sigta-spring`, `animate-sigta-settle`
and the `--sigta-dur-*` tokens. Never hand-roll keyframes.
