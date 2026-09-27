<?php

namespace Tests\Feature;

use App\Livewire\Kit\Autosave;
use App\Livewire\Kit\Inbox;
use App\Livewire\Kit\Jobs;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MotionPagesTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        return [
            'loader' => ['loader', 'data-sigta="loader"'],
            'save-state' => ['save-state', 'data-sigta="save-state"'],
            'job' => ['job', 'data-sigta="job"'],
            'motion' => ['motion', 'data-sigta-motion'],
            'inbox' => ['inbox', 'data-sigta-strike'],
        ];
    }

    #[DataProvider('pages')]
    public function test_motion_kit_page_renders(string $page, string $marker): void
    {
        $response = $this->get('/kit/'.$page);

        $response->assertStatus(200);
        $response->assertSee($marker, false);
        $this->assertDoesNotMatchRegularExpression('/#[0-9A-Fa-f]{6}\b/', $response->getContent());
    }

    public function test_inbox_completes_an_item_with_an_undo_toast(): void
    {
        Livewire::test(Inbox::class)
            ->call('complete', 2)
            ->assertCount('items', 3)
            ->assertDispatched('sigta-toast', variant: 'success', key: 'kit-inbox-done')
            ->assertNotDispatched('sigta-celebrate')
            ->call('reopen', 2)
            ->assertCount('items', 4)
            ->assertSet('items.1.id', 2);
    }

    public function test_inbox_zero_is_celebrated(): void
    {
        $inbox = Livewire::test(Inbox::class);

        foreach ([1, 2, 3, 4] as $id) {
            $inbox->call('complete', $id);
        }

        $inbox->assertCount('items', 0)
            ->assertDispatched('sigta-celebrate', level: 'big')
            ->assertSee('All clear.');
    }

    public function test_a_new_arrival_lands_on_top(): void
    {
        Livewire::test(Inbox::class)
            ->call('arrive')
            ->assertCount('items', 5)
            ->assertSet('items.0.id', 101);
    }

    public function test_autosave_reports_a_failing_body_as_a_validation_error(): void
    {
        Livewire::test(Autosave::class)
            ->set('body', 'this will fail')
            ->assertHasErrors('body')
            ->set('body', 'fine again')
            ->assertHasNoErrors('body');
    }

    public function test_jobs_run_finish_and_leave_the_queue(): void
    {
        $jobs = Livewire::test(Jobs::class);

        for ($i = 0; $i < 30; $i++) {
            $jobs->call('tick');
        }

        $jobs->assertCount('jobs', 0);
    }

    public function test_a_queued_job_joins_the_queue(): void
    {
        Livewire::test(Jobs::class)
            ->call('queue')
            ->assertCount('jobs', 3)
            ->assertSet('jobs.2.state', 'queued');
    }
}
