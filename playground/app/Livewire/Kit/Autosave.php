<?php

namespace App\Livewire\Kit;

use Livewire\Component;

/**
 * Kit demo: a note that saves itself. <sigta:save-state target="title, body">
 * follows the property syncs on its own; a body containing "fail" comes back
 * with a validation error, so the failed state can be seen too.
 */
class Autosave extends Component
{
    public string $title = 'Field notes';

    public string $body = 'Paper, ink and a little patience.';

    public function updated(string $property): void
    {
        // Pretend the database is far away.
        $this->pause(700);

        $this->resetErrorBag('body');

        if (str_contains(strtolower($this->body), 'fail')) {
            $this->addError('body', 'This demo refuses any note containing “fail”.');
        }
    }

    public function save(): void
    {
        $this->pause(900);
    }

    private function pause(int $milliseconds): void
    {
        if (! app()->runningUnitTests()) {
            usleep($milliseconds * 1000);
        }
    }

    public function render()
    {
        return view('livewire.kit.autosave');
    }
}
