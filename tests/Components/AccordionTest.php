<?php

use Illuminate\Support\Facades\Blade;

it('renders native details/summary items with headings', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::accordion>
            <x-sigta::accordion.item heading="What does it cost?">
                <p>One flat hourly rate.</p>
            </x-sigta::accordion.item>
        </x-sigta::accordion>
        BLADE);

    expect($html)
        ->toContain('data-sigta="accordion"')
        ->toContain('data-sigta="accordion.item"')
        ->toContain('<details')
        ->toContain('<summary')
        ->toContain('What does it cost?')
        ->toContain('One flat hourly rate.')
        ->toContain('data-scope="accordion.item" data-part="summary"')
        ->toContain('data-scope="accordion.item" data-part="content"');

    // The "+" turns into a cross while the item is open.
    expect(contractCss('accordion'))
        ->toContain('[data-sigta="accordion.item"][open] > &')
        ->toContain('after:rotate-45')
        ->toContain("after:content-['+']");
});

it('carries no design classes, only contract attributes', function () {
    $html = Blade::render('<x-sigta::accordion><x-sigta::accordion.item heading="Q">A</x-sigta::accordion.item></x-sigta::accordion>');

    expect($html)->not->toContain('class=');
});

it('forwards the group name to each item for native exclusive-open', function () {
    $html = Blade::render(<<<'BLADE'
        <x-sigta::accordion name="faq">
            <x-sigta::accordion.item heading="First">A</x-sigta::accordion.item>
            <x-sigta::accordion.item heading="Second">B</x-sigta::accordion.item>
        </x-sigta::accordion>
        BLADE);

    expect(substr_count($html, 'name="faq"'))->toBe(2);
});

it('renders without a name attribute when the group has none', function () {
    $html = Blade::render('<x-sigta::accordion><x-sigta::accordion.item heading="Solo">A</x-sigta::accordion.item></x-sigta::accordion>');

    expect($html)->not->toContain('name=');
});

it('forwards attributes onto the details element', function () {
    $html = Blade::render('<x-sigta::accordion><x-sigta::accordion.item heading="Open me" open data-test="item">A</x-sigta::accordion.item></x-sigta::accordion>');

    expect($html)
        ->toMatch('/<details[^>]*\sopen[\s>=]/')
        ->toMatch('/<details[^>]*data-test="item"/');
});

it('lets an item\'s own name win over the group\'s', function () {
    $html = Blade::render('<x-sigta::accordion name="faq"><x-sigta::accordion.item heading="Q" name="own">A</x-sigta::accordion.item></x-sigta::accordion>');

    expect($html)->toContain('name="own"')->not->toContain('name="faq"');
});
