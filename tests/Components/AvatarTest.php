<?php

use Illuminate\Support\Facades\Blade;

it('derives initials from the first two words of the name', function () {
    $html = Blade::render('<x-sigta::avatar name="Søren Risbøll Frisk" />');

    expect($html)
        ->toContain('data-sigta="avatar"')
        ->toContain('SR')
        ->toContain('data-size="base"')
        ->toContain('data-shape="circle"')
        ->toContain('data-tone="ink"')
        ->toContain('role="img"')
        ->toContain('aria-label="Søren Risbøll Frisk"');

    expect(contractCss('avatar'))
        ->toContain('@apply font-mono;')
        ->toContain('&:not([data-tone="line"]) { @apply bg-ink text-surface; }')
        ->toContain('&:not([data-shape="square"]) { @apply rounded-full; }');
});

it('derives a single initial from a single-word name', function () {
    expect(Blade::render('<x-sigta::avatar name="Plumb" />'))->toContain('>P<');
});

it('hides nameless initials from assistive technology', function () {
    $html = Blade::render('<x-sigta::avatar />');

    expect($html)
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"');
});

it('renders an image when src is given', function () {
    $html = Blade::render('<x-sigta::avatar src="/img/sf.jpg" name="Søren Frisk" />');

    expect($html)
        ->toContain('<img')
        ->toContain('data-sigta="avatar"')
        ->toContain('src="/img/sf.jpg"')
        ->toContain('alt="Søren Frisk"')
        ->not->toContain('<span');

    expect(contractCss('avatar'))->toContain('&:is(img) { @apply object-cover; }');
});

it('prefers an explicit alt over the name', function () {
    $html = Blade::render('<x-sigta::avatar src="/img/sf.jpg" name="Søren Frisk" alt="Portrait of Søren" />');

    expect($html)->toContain('alt="Portrait of Søren"');
});

it('supports the square shape and line tone', function () {
    $html = Blade::render('<x-sigta::avatar name="Søren Frisk" shape="square" tone="line" />');

    expect($html)
        ->toContain('data-shape="square"')
        ->toContain('data-tone="line"');

    expect(contractCss('avatar'))
        ->toContain('&[data-shape="square"] { @apply rounded-base; }')
        ->toContain('&[data-tone="line"] { @apply bg-line text-ink; }');
});

it('renders each size', function (string $size, string $rule) {
    expect(Blade::render('<x-sigta::avatar name="Søren Frisk" size="'.$size.'" />'))->toContain('data-size="'.$size.'"');
    expect(contractCss('avatar'))->toContain($rule);
})->with([
    ['xs', '&[data-size="xs"] { @apply size-6'],
    ['sm', '&[data-size="sm"] { @apply size-8'],
    ['base', '[data-size="lg"])) { @apply size-10'],
    ['lg', '&[data-size="lg"] { @apply size-14'],
]);

it('forwards arbitrary attributes', function () {
    $html = Blade::render('<x-sigta::avatar name="Søren Frisk" data-test="avatar" class="ring-1" />');

    expect($html)
        ->toContain('data-test="avatar"')
        ->toContain('ring-1');
});

it('renders a profile with name, meta and avatar', function () {
    $html = Blade::render('<x-sigta::profile name="Søren Frisk" meta="Studio lead" />');

    expect($html)
        ->toContain('data-sigta="profile"')
        ->toContain('data-sigta="avatar"')
        ->toContain('data-scope="profile" data-part="name">Søren Frisk')
        ->toContain('data-scope="profile" data-part="meta">Studio lead')
        ->not->toContain('data-reverse');

    expect(contractCss('profile'))
        ->toContain('[data-scope="profile"][data-part="name"] {'."\n".'    @apply font-head')
        ->toContain('text-graphite');
});

it('hides the profile avatar beside the visible name', function () {
    $html = Blade::render('<x-sigta::profile name="Søren Frisk" />');

    expect($html)
        ->toContain('aria-hidden="true"')
        ->not->toContain('role="img"')
        ->not->toContain('data-part="meta"');
});

it('reverses the profile layout', function () {
    $html = Blade::render('<x-sigta::profile name="Søren Frisk" meta="Studio lead" reverse />');

    expect($html)->toContain('data-reverse');
    expect(contractCss('profile'))->toContain('&[data-reverse] { @apply flex-row-reverse text-right; }');
});

it('forwards attributes on the profile root', function () {
    expect(Blade::render('<x-sigta::profile name="Søren Frisk" data-test="profile" />'))
        ->toContain('data-test="profile"');
});
