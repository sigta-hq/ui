<?php

use Illuminate\Support\Facades\Blade;

it('renders with zero props: root marker, an empty accordion and no header', function () {
    $html = Blade::render('<x-sigta::section.faq />');

    expect($html)
        ->toContain('data-sigta="section.faq"')
        ->toContain('data-sigta="accordion"')
        ->not->toContain('data-sigta="section-header"')
        ->not->toContain('data-sigta="accordion.item"');
});

it('renders the heading, lede and items from props', function () {
    $html = Blade::render('<x-sigta::section.faq heading="Questions" lede="And honest answers." :items="$items" />', [
        'items' => [
            ['q' => 'What does it cost?', 'a' => 'One fixed rate.'],
            ['q' => 'Are we tied in?', 'a' => 'No.'],
        ],
    ]);

    expect($html)
        ->toContain('data-sigta="section-header"')
        ->toContain('Questions')
        ->toContain('And honest answers.')
        ->toContain('What does it cost?')
        ->toContain('One fixed rate.');

    expect(substr_count($html, 'data-sigta="accordion.item"'))->toBe(2);
});

it('names the accordion group so it is single-open', function () {
    $html = Blade::render('<x-sigta::section.faq :items="[[\'q\' => \'Q?\', \'a\' => \'A.\']]" />');

    expect($html)->toContain('name="faq"');
});

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::section.faq data-test="faq" />');

    expect($html)->toContain('data-test="faq"');
});
