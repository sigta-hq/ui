@props([
    'user' => null,        // the current assignee (object or array with id + name), or null
    'members' => [],       // iterable of assignable people (each object/array with id + name)
    'method' => null,      // Livewire method on the nearest component to call on select
    'args' => [],          // leading args passed before the selected user id
    'size' => 'xs',        // avatar size
    'showName' => false,   // render the name beside the avatar
    'label' => null,       // accessible label for the trigger
    'inline' => false,     // render the panel inline (absolute) instead of teleporting to <body>
])

{{--
    Assignee avatar that opens a searchable people dropdown. Selecting a person
    (or "Unassigned") calls the given Livewire method as `method(...args, userId)`,
    so one component drops into any context:
      board card    → method="assignTask" :args="[$task->id]"  → assignTask(taskId, id)
      task modal    → method="assign"                           → assign(id)
      modal subtask → method="assignSubtask" :args="[$sub->id]" → assignSubtask(subtaskId, id)

    Each consuming method should re-check org membership server-side, e.g.
    abort_unless($members->contains('id', $userId), 422).

    Positioning has two modes:
      - default: the panel is teleported to <body> and fixed-positioned so it
        escapes a draggable card and the board's overflow-x-auto clipping.
      - :inline: the panel renders absolutely inside the trigger. Use it inside a
        native <dialog> (top layer), where teleported nodes paint behind the
        dialog and Livewire's morph would strip them on re-render.

    Pass a value-keyed wire:key (by assignee id) so a reassign cleanly replaces
    the picker instead of leaving stale Alpine state. --}}
@php
$userName = $user !== null ? (string) data_get($user, 'name') : null;
$memberData = collect($members)
    ->map(fn ($m) => ['id' => data_get($m, 'id'), 'name' => (string) data_get($m, 'name')])
    ->values();
@endphp

<div
    data-sigta="assignee-picker"
    {{ $attributes->only(['wire:key']) }}
    x-data="{
        open: false,
        q: '',
        top: 0,
        left: 0,
        inline: {{ $inline ? 'true' : 'false' }},
        members: @js($memberData),
        get filtered() {
            const q = this.q.trim().toLowerCase();
            return q ? this.members.filter((m) => m.name.toLowerCase().includes(q)) : this.members;
        },
        initials(name) {
            return (name || '').split(/\s+/).filter(Boolean).slice(0, 2).map((w) => w[0].toUpperCase()).join('');
        },
        toggle() {
            if (this.open) { this.open = false; return; }
            if (! this.inline) {
                const r = this.$refs.trigger.getBoundingClientRect();
                this.top = Math.round(r.bottom + 6);
                this.left = Math.round(Math.max(12, Math.min(r.left, window.innerWidth - 256 - 12)));
            }
            this.q = '';
            this.open = true;
            this.$nextTick(() => this.$refs.search?.focus());
        },
        select(id) {
            @if ($method) $wire.{{ $method }}(...@js($args), id); @endif
            this.open = false;
        },
    }"
    x-on:keydown.escape.window="open = false"
>
    <button
        type="button"
        x-ref="trigger"
        x-on:click.stop="toggle()"
        x-on:dragstart.stop.prevent
        draggable="false"
        aria-label="{{ $label ?? __('Assignee') }}"
        data-scope="assignee-picker"
        data-part="trigger"
        {{ $attributes->except(['wire:key']) }}
    >
        @if ($user)
            <x-sigta::avatar :name="$userName" :size="$size" />
        @else
            <span aria-hidden="true" data-scope="assignee-picker" data-part="placeholder" data-size="{{ $size }}">
                <x-sigta::icon name="plus" size="xs" />
            </span>
        @endif
        @if ($showName)
            <span data-scope="assignee-picker" data-part="name">{{ $userName ?? __('Assign') }}</span>
        @endif
    </button>

    @if (! $inline)<template x-teleport="body">@endif
        <div
            x-show="open"
            x-cloak
            x-transition.opacity.duration.100ms
            x-on:click.outside="open = false"
            x-on:dragstart.stop
            draggable="false"
            @if (! $inline) :style="`position: fixed; top: ${top}px; left: ${left}px;`" @endif
            data-scope="assignee-picker"
            data-part="panel"
            @if ($inline) data-inline="" @endif
            data-test="assignee-dropdown"
        >
            {{-- x-if keeps the search + member list out of the DOM until opened, so N closed pickers stay cheap. --}}
            <template x-if="open">
                <div>
                    <input
                        x-ref="search"
                        x-model="q"
                        type="text"
                        placeholder="{{ __('Search people') }}"
                        data-scope="assignee-picker"
                        data-part="search"
                        data-test="assignee-search"
                    />
                    <div data-scope="assignee-picker" data-part="list">
                        <button type="button" x-on:click="select(null)" data-scope="assignee-picker" data-part="unassign">
                            <span aria-hidden="true" data-scope="assignee-picker" data-part="mark"><x-sigta::icon name="x" size="xs" /></span>
                            {{ __('Unassigned') }}
                        </button>
                        <template x-for="m in filtered" :key="m.id">
                            <button type="button" x-on:click="select(m.id)" data-scope="assignee-picker" data-part="option">
                                <x-sigta::avatar size="xs" aria-hidden="true" x-text="initials(m.name)" />
                                <span x-text="m.name" data-scope="assignee-picker" data-part="label"></span>
                            </button>
                        </template>
                        <p x-show="filtered.length === 0" data-scope="assignee-picker" data-part="empty">{{ __('No people found.') }}</p>
                    </div>
                </div>
            </template>
        </div>
    @if (! $inline)</template>@endif
</div>
