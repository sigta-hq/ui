<?php

namespace App\Livewire\Kit;

use Livewire\Attributes\On;
use Livewire\Component;
use Sigta\Ui\Facades\Sigta;

/**
 * Kit demo: an inbox whose finished items are ticked, crossed out and folded
 * away ($sigta.complete in a [data-sigta-motion] list), with an Undo toast, a
 * count that bumps, and confetti at inbox zero. State lives on the component,
 * so the demo needs no database.
 */
class Inbox extends Component
{
    /** @var array<int, array{id: int, title: string, from: string, meta: string}> */
    public array $items = [];

    /** @var array<int, array{id: int, title: string, from: string, meta: string}> */
    public array $done = [];

    public int $next = 100;

    public function mount(): void
    {
        $this->items = self::seed();
    }

    /**
     * @return array<int, array{id: int, title: string, from: string, meta: string}>
     */
    public static function seed(): array
    {
        return [
            ['id' => 1, 'title' => 'Approve the Plumb onboarding copy', 'from' => 'Ida', 'meta' => 'Review · due today'],
            ['id' => 2, 'title' => 'Reply to Halyard about the routing console', 'from' => 'Mads', 'meta' => 'Client · 2h ago'],
            ['id' => 3, 'title' => 'Sign off the Q3 invoice run', 'from' => 'Finance', 'meta' => 'Approval · yesterday'],
            ['id' => 4, 'title' => 'Book the Marginalia launch call', 'from' => 'Sofie', 'meta' => 'Calendar · this week'],
        ];
    }

    public function complete(int $id): void
    {
        // A little real latency, so the demo shows the optimistic path.
        $this->pause(250);

        $item = collect($this->items)->firstWhere('id', $id);

        if (! $item) {
            return;
        }

        $this->items = array_values(array_filter($this->items, fn (array $i) => $i['id'] !== $id));
        $this->done[$id] = $item;

        Sigta::toast(
            'Marked done: '.$item['title'],
            variant: 'success',
            action: ['text' => 'Undo', 'event' => 'kit-inbox-reopen', 'detail' => ['id' => $id]],
            key: 'kit-inbox-done',
        );

        if ($this->items === []) {
            Sigta::celebrate('big', text: 'inbox zero!', target: '[data-inbox-heading]');
        }
    }

    #[On('kit-inbox-reopen')]
    public function reopen(int $id): void
    {
        $item = $this->done[$id] ?? null;

        if (! $item) {
            return;
        }

        unset($this->done[$id]);
        $this->items[] = $item;
        usort($this->items, fn (array $a, array $b) => $a['id'] <=> $b['id']);
    }

    public function arrive(): void
    {
        $arrivals = [
            ['title' => 'New brief: Almanac changelog polish', 'from' => 'Jonas', 'meta' => 'Brief · just now'],
            ['title' => 'Plumb trial wants a second seat', 'from' => 'Plumb', 'meta' => 'Account · just now'],
            ['title' => 'Ledger rebuild: budget engine review', 'from' => 'Karla', 'meta' => 'Review · just now'],
        ];

        $item = $arrivals[$this->next % count($arrivals)];
        array_unshift($this->items, ['id' => ++$this->next] + $item);
    }

    public function refill(): void
    {
        $this->items = self::seed();
        $this->done = [];
    }

    private function pause(int $milliseconds): void
    {
        if (! app()->runningUnitTests()) {
            usleep($milliseconds * 1000);
        }
    }

    public function render()
    {
        return view('livewire.kit.inbox');
    }
}
