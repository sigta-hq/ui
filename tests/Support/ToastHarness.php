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
    ): void {
        Sigta::toast($text, $heading, $variant, $duration, $link);
    }

    public function render(): string
    {
        return '<div>toast harness</div>';
    }
}
