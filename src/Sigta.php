<?php

namespace Sigta\Ui;

use Livewire\Livewire;
use RuntimeException;

class Sigta
{
    /**
     * Trigger a toast notification on the current page.
     *
     * An action adds a button that fires `event` (with `detail` as its
     * parameters, so an #[On] listener hears it) and closes the toast. A
     * `key` replaces the showing toast with the same key instead of stacking.
     *
     * @param  array{text?: string, href?: string, target?: string, rel?: string, download?: bool|string, navigate?: bool}|null  $link
     * @param  array{text: string, event: string, detail?: array<string, mixed>}|null  $action
     */
    public function toast(
        ?string $text = null,
        ?string $heading = null,
        ?string $variant = null,
        ?int $duration = null,
        ?array $link = null,
        ?array $action = null,
        ?string $key = null,
    ): void {
        $this->dispatch('sigta-toast', [
            'heading' => $heading,
            'text' => $text,
            'variant' => $variant,
            'duration' => $duration,
            'link' => $link,
            'action' => $action,
            'key' => $key,
        ], 'Sigta::toast()');
    }

    /**
     * Celebrate a finished thing on the current page: `small` flicks a burst
     * from the target, `medium` rings it with a handwritten note, `big` throws
     * paper confetti. `target` is a CSS selector; `text` is the note, and
     * false leaves it out.
     */
    public function celebrate(
        string $level = 'small',
        string|false|null $text = null,
        ?string $target = null,
    ): void {
        $this->dispatch('sigta-celebrate', [
            'level' => $level,
            'text' => $text,
            'target' => $target,
        ], 'Sigta::celebrate()');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function dispatch(string $event, array $payload, string $caller): void
    {
        $component = Livewire::current();

        if (! $component) {
            throw new RuntimeException(
                $caller.' requires an active Livewire component. '
                .'Call it from a Livewire action or use window.Sigta in JavaScript.'
            );
        }

        $component->dispatch($event, ...array_filter($payload, fn ($value) => $value !== null));
    }
}
