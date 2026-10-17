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

$percent = $value === null ? null : ($state === 'done' ? 100 : min(100, max(0, (float) $value)));
@endphp

<div
    data-sigta="job"
    x-data="sigtaJob"
    data-state="{{ $state }}"
    {{ $attributes }}
>
    <x-sigta::loader size="lg" :label="$heading ?? $status" :state="$state" :value="$percent" data-scope="job" data-part="loader" />

    <div data-scope="job" data-part="main">
        <div data-scope="job" data-part="header">
            @if ($heading)
                <p data-scope="job" data-part="heading">{{ $heading }}</p>
            @endif
            @if ($percent !== null)
                {{-- A fixed width, so a growing percentage never nudges the heading. --}}
                <span data-scope="job" data-part="percent">{{ round($percent) }}%</span>
            @endif
        </div>

        <p data-scope="job" data-part="meta">
            <span data-scope="job" data-part="status" aria-live="polite">{{ $status }}</span>
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
            <p data-scope="job" data-part="description">{{ $description }}</p>
        @endif

        {{ $slot }}
    </div>

    @isset($actions)
        <div data-scope="job" data-part="actions">{{ $actions }}</div>
    @endisset
</div>
