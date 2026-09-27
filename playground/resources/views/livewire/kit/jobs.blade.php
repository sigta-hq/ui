<div wire:poll.1s="tick" class="flex flex-col gap-4">
    <div class="flex items-center justify-between gap-4">
        <x-sigta::eyebrow>Background work</x-sigta::eyebrow>
        <x-sigta::button size="sm" wire:click="queue">Queue a job</x-sigta::button>
    </div>

    <div data-sigta-motion id="kit-jobs" class="space-y-2.5">
        @foreach ($jobs as $job)
            <x-sigta::job
                wire:key="job-{{ $job['id'] }}"
                :heading="$job['heading']"
                :state="$job['state']"
                :value="$job['state'] === 'queued' ? null : $job['progress']"
                :stage="\App\Livewire\Kit\Jobs::stage($job)"
                :since="$job['started']"
            />
        @endforeach
    </div>

    @if ($jobs === [])
        <x-sigta::text variant="muted" class="animate-sigta-settle">The queue is empty. Queue a job to watch one run.</x-sigta::text>
    @endif
</div>
