<?php

namespace App\Livewire\Kit;

use Livewire\Component;

/**
 * Kit demo: a queue of long-running jobs, advanced by wire:poll. Each finished
 * job lands (the compass completes with a burst), shows its result for a few
 * ticks, then the server drops it and the [data-sigta-motion] list folds it
 * away; a newly queued job unfolds in.
 */
class Jobs extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $jobs = [];

    public int $next = 2;

    private const STAGES = ['Reading the file', 'Matching duplicates', 'Writing records', 'Tidying up'];

    public function mount(): void
    {
        $this->jobs = [
            self::job(1, 'Importing 1,204 contacts', 'working', 34),
            self::job(2, 'Exporting the Q3 ledger', 'queued', 0),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function job(int $id, string $heading, string $state, int $progress): array
    {
        return [
            'id' => $id,
            'heading' => $heading,
            'state' => $state,
            'progress' => $progress,
            'started' => now()->subSeconds($progress)->toIso8601String(),
            'shown' => 0,
        ];
    }

    public static function stage(array $job): ?string
    {
        return match ($job['state']) {
            'queued' => 'Waiting for a worker',
            'done' => 'All records written',
            default => self::STAGES[min(3, intdiv((int) $job['progress'], 25))],
        };
    }

    public function tick(): void
    {
        $working = collect($this->jobs)->contains('state', 'working');

        foreach ($this->jobs as &$job) {
            if ($job['state'] === 'done') {
                $job['shown']++;
            } elseif ($job['state'] === 'working') {
                $job['progress'] = min(100, $job['progress'] + random_int(6, 15));
                if ($job['progress'] >= 100) {
                    $job['state'] = 'done';
                }
            } elseif ($job['state'] === 'queued' && ! $working) {
                $job['state'] = 'working';
                $job['started'] = now()->toIso8601String();
                $working = true;
            }
        }
        unset($job);

        // A finished job stays on screen for a few ticks, then leaves the list.
        $this->jobs = array_values(array_filter($this->jobs, fn (array $job) => $job['shown'] < 4));
    }

    public function queue(): void
    {
        $headings = ['Exporting 312 invoices', 'Re-indexing the archive', 'Sending the October newsletter'];
        $this->jobs[] = self::job(++$this->next, $headings[$this->next % count($headings)], 'queued', 0);
    }

    public function render()
    {
        return view('livewire.kit.jobs');
    }
}
