<?php

namespace Sigta\Ui;

use Livewire\Livewire;
use RuntimeException;

class Sigta
{
    /**
     * Trigger a toast notification on the current page.
     *
     * @param  array{text?: string, href?: string, target?: string, rel?: string, download?: bool|string, navigate?: bool}|null  $link
     */
    public function toast(
        ?string $text = null,
        ?string $heading = null,
        ?string $variant = null,
        ?int $duration = null,
        ?array $link = null,
    ): void {
        $payload = array_filter([
            'heading' => $heading,
            'text' => $text,
            'variant' => $variant,
            'duration' => $duration,
            'link' => $link,
        ], fn ($value) => $value !== null);

        $component = Livewire::current();

        if (! $component) {
            throw new RuntimeException(
                'Sigta::toast() requires an active Livewire component. '
                .'Call it from a Livewire action or use window.Sigta.toast() in JavaScript.'
            );
        }

        $component->dispatch('sigta-toast', ...$payload);
    }
}
