<?php

use Illuminate\Support\Facades\Blade;

it('renders a composer with an auto-growing textarea and send button', function () {
    $html = Blade::render('<x-sigta::composer placeholder="Reply…" name="message" />');

    expect($html)
        ->toContain('data-sigta="composer"')
        ->toContain('<textarea')
        ->toContain('placeholder="Reply…"')
        ->toContain('name="message"')
        ->toContain('aria-label="Message"')
        ->toContain('x-ref="input"')
        ->toContain('x-data="sigtaComposer"')
        ->toContain('x-on:input="resize()"')    // auto-grow behavior
        ->toContain('aria-label="Send"')
        ->toContain('aria-label="Attach file"') // default leading slot
        ->toContain('data-scope="composer"'."\n".'        data-part="input"')
        ->not->toContain('class=');

    expect(contractCss('composer'))
        ->toContain('focus-within:border-ink')
        ->toContain('@apply flex-1 min-w-0 resize-none bg-transparent');
});

it('wires Enter-to-send, Shift+Enter newline, and the submit event', function () {
    $html = Blade::render('<x-sigta::composer />');

    $runtime = file_get_contents(dirname(__DIR__, 2).'/resources/js/sigta.js');

    // The event goes out from the textarea, where a listener on the component lands.
    expect($runtime)->toContain("this.\$refs.input.dispatchEvent(new CustomEvent('sigta-composer-submit', { detail: { text: value }");

    expect($html)
        ->toContain('x-data="sigtaComposer"')                // dispatches the event
        ->toContain('x-on:keydown.enter=')                   // Enter handling
        ->toContain('$event.shiftKey')                       // Shift+Enter = newline
        ->toContain('x-on:click="submit()"')
        ->toContain('x-bind:disabled="! text.trim()"');      // disabled when empty
});

it('forwards wire:model and id onto the native textarea', function () {
    $html = Blade::render('<x-sigta::composer id="reply" wire:model="body" aria-label="Reply box" />');

    expect($html)
        ->toContain('wire:model="body"')
        ->toContain('id="reply"')
        ->toContain('aria-label="Reply box"');   // caller overrides the default
});

it('accepts a custom leading slot', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::composer>
            <x-slot:leading><button type="button" aria-label="Emoji">:)</button></x-slot:leading>
        </x-sigta::composer>
    BLADE);

    expect($html)
        ->toContain('aria-label="Emoji"')
        ->not->toContain('aria-label="Attach file"');
});
