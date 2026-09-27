<?php

namespace Tests\Support;

use Livewire\Component;
use Sigta\Ui\Facades\Sigta;

class ToastHarness extends Component
{
    public function triggerToast(
        ?string $text = null,
        ?string $heading = null,
        ?string $variant = null,
        ?int $duration = null,
        ?array $link = null,
        ?array $action = null,
        ?string $key = null,
    ): void {
        Sigta::toast($text, $heading, $variant, $duration, $link, $action, $key);
    }

    public function triggerCelebrate(string $level = 'small', ?string $text = null, ?string $target = null): void
    {
        Sigta::celebrate($level, $text, $target);
    }

    public function render(): string
    {
        return '<div>toast harness</div>';
    }
}
