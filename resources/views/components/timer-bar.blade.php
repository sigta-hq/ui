@props([
    'title' => null,       // running task title
    'project' => null,     // optional project / context name
    'since' => null,       // start instant (ISO string or DateTimeInterface) for the clock
    'stopLabel' => null,   // label for the default stop button
])

{{--
    Dark running-timer bar: a pulsing status dot, the running task (+ optional
    project), a live-ticking clock, and a Stop control. Presentational only —
    your app owns which entry is running and how it stops. Mount it from a
    Livewire component that renders it while a timer runs (e.g. in the layout).

    Stop wiring (pick one):
      - default: the Stop button dispatches a bubbling `sigta-timer-stop` event.
        Catch it on your wrapper: x-on:sigta-timer-stop="$wire.stop()".
      - or pass an <x-slot:stop> with your own control (e.g. wire:click="stop").

        <div x-on:sigta-timer-stop="$wire.stop()">
            <x-sigta::timer-bar
                :title="$entry->task->title"
                :project="$entry->task->project?->name"
                :since="$entry->started_at"
            />
        </div>
--}}
<div
    data-sigta="timer-bar"
    x-data="{}"
    {{ $attributes->merge(['class' => 'flex items-center justify-between gap-4 bg-inverse px-4 py-2.5 text-inverse-fg md:px-6']) }}
>
    <div class="flex min-w-0 items-center gap-3">
        <span class="size-2 shrink-0 animate-pulse rounded-full bg-danger" aria-hidden="true"></span>
        @if ($title)
            <span class="truncate font-head text-[0.95rem]">{{ $title }}</span>
        @endif
        @if ($project)
            <span class="hidden truncate font-mono text-[0.72rem] uppercase tracking-[0.12em] text-inverse-fg-muted sm:inline">{{ $project }}</span>
        @endif
    </div>

    <div class="flex shrink-0 items-center gap-4">
        <x-sigta::elapsed-clock :since="$since" class="text-lg text-inverse-fg" data-test="timer-clock" />

        @isset($stop)
            {{ $stop }}
        @else
            <button
                type="button"
                x-on:click="$dispatch('sigta-timer-stop')"
                class="flex items-center gap-1.5 rounded-base border-[1.5px] border-inverse-line px-3 py-1.5 font-mono text-[0.72rem] uppercase tracking-[0.1em] text-inverse-fg transition hover:bg-inverse-fg hover:text-ink"
                data-test="timer-stop"
            >
                <x-sigta::icon name="square" size="xs" /> {{ $stopLabel ?? __('Stop') }}
            </button>
        @endisset
    </div>
</div>
