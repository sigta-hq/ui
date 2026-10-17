---
title: Svelte, Inertia and any backend
order: 4
runtime: svelte
---

# Svelte, Inertia and any backend

The Svelte runtime uses the same component names, props, variants and parts
as the Livewire one; how state flows is Svelte's own.

## State

- Two-way props are bindable: `bind:value`, `bind:checked`, `bind:open`.
- Events are callback props: `onchange`, `onsubmit`, `onmove`.
- Named slots are snippets of the same name: `{#snippet footer()}…{/snippet}`.
- Data props take plain shapes: ISO date strings, numbers, plain objects.

## Wiring the feel

A few things Livewire does by itself are explicit in Svelte:

| Livewire | Svelte |
|---|---|
| A button with `wire:click` shows the compass once its action runs long | `<Button loading={form.processing}>` |
| `<sigta:save-state target="save">` follows the Livewire request | `<SaveState state={…}>`, `sigta.save(promise, name)`, or `connectInertia({ saveState: 'name' })` |
| Rows the server drops fold away (morph hooks) | `out:leave` on the rows of a keyed `{#each}` |
| `Sigta::toast()` from PHP | `sigta.toast()`, or the Inertia `sigta.toasts` page prop |

## The server boundary

`@sigta-hq/svelte/inertia` reads a `sigta` page prop — shared or flash data —
on every visit:

```json
{
  "sigta": {
    "toasts": [{ "text": "Invoice sent", "variant": "success", "action": { "text": "Undo", "event": "invoice-unsend", "detail": { "id": 7 } } }],
    "celebrate": { "level": "big", "text": "Inbox zero!", "target": "#inbox" }
  }
}
```

```js
import { connectInertia } from '@sigta-hq/svelte/inertia';
connectInertia({ saveState: 'profile' });   // also drive <SaveState name="profile"> from writing visits
```

A toast action with an `event` fires a window event of that name; listen for
it in your page and call your backend.

### From Go

Any Inertia adapter for Go can share the prop — with
[gonertia](https://github.com/romsar/gonertia), for example:

```go
i.ShareProp("sigta", map[string]any{
    "toasts": []map[string]any{{"text": "Invoice sent", "variant": "success"}},
})
```

### From Laravel

```php
Inertia::share('sigta', fn () => session()->pull('sigta'));
// in a controller
session()->flash('sigta', ['toasts' => [['text' => 'Saved', 'variant' => 'success']]]);
```

## Without Inertia

Everything works in plain Svelte or SvelteKit: call `sigta.toast()` after a
fetch, drive `<SaveState>` with `sigta.save(promise, name)`.

## Language

Sigta's own strings (aria labels such as "Toggle navigation", empty states
such as "No people found.") are English until the root layout gives Sigta a
message map. The keys are the English text, exactly like a Laravel
`lang/<locale>.json`, so whatever translation file your backend already has
works as is:

```svelte
<script>
    import { setMessages } from '@sigta-hq/svelte';
    import da from '../lang/da.json';
    let { children } = $props();
    setMessages(da);
</script>

{@render children()}
```

The map lives in Svelte context, so server renders for different languages
never share it.
