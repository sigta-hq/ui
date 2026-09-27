{{-- Job — one piece of long-running work: an import, an export, a queue run.
     The compass draws its circle as the work progresses, the stage line says what
     is happening now, and the moment it turns done the mark completes with a
     burst. Its state is plain markup, so a Livewire poll that re-renders it
     (wire:poll) moves it along; in a [data-sigta-motion] list a finished job
     the server drops folds away.

       <sigta:job heading="Importing contacts" :value="$import->progress"
                  stage="Matching duplicates" :since="$import->started_at" />

     `value` null keeps the compass turning with no progress. --}}
@props([
    'heading' => null,
    'description' => null,
    'stage' => null,
    'value' => null,
    'state' => 'working',
    'since' => null,
])

@php
$state = in_array($state, ['queued', 'working', 'done', 'failed'], true) ? $state : 'working';

$status = match ($state) {
    'queued' => 'Queued',
    'done' => 'Done',
    'failed' => 'Failed',
    default => 'Running',
};

$statusClass = match ($state) {
    'done' => 'text-success',
    'failed' => 'text-danger',
    default => 'text-graphite',
};

$percent = $value === null ? null : ($state === 'done' ? 100 : min(100, max(0, (float) $value)));
@endphp

<div
    data-sigta="job"
    x-data="sigtaJob"
    data-state="{{ $state }}"
    {{ $attributes->merge(['class' => 'flex items-start gap-3.5 rounded-panel border border-line bg-surface-raised px-4 py-3.5']) }}
>
    <x-sigta::loader size="lg" :label="$heading ?? $status" :state="$state" :value="$percent" class="mt-0.5" />

    <div class="min-w-0 flex-1">
        <div class="flex items-baseline justify-between gap-3">
            @if ($heading)
                <p class="font-head text-[1.02rem] leading-snug text-ink truncate">{{ $heading }}</p>
            @endif
            @if ($percent !== null)
                <span class="shrink-0 font-mono text-[0.74rem] tabular-nums text-ink">{{ round($percent) }}%</span>
            @endif
        </div>

        <p class="mt-1 flex flex-wrap items-center gap-x-2 font-mono text-[0.64rem] uppercase tracking-[0.12em] text-graphite">
            <span class="{{ $statusClass }}" aria-live="polite">{{ $status }}</span>
            @if ($stage)
                <span aria-hidden="true">·</span>
                <span>{{ $stage }}</span>
            @endif
            @if ($since && in_array($state, ['queued', 'working'], true))
                <span aria-hidden="true">·</span>
                <x-sigta::elapsed-clock :since="$since" />
            @endif
        </p>

        @if ($description)
            <p class="mt-1.5 text-[0.9rem] leading-snug text-graphite">{{ $description }}</p>
        @endif

        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
