<?php

use Illuminate\Support\Facades\Blade;

it('derives initials from the first two words of the name', function () {
    $html = Blade::render('<x-sigta::avatar name="Søren Risbøll Frisk" />');

    expect($html)
        ->toContain('data-sigta="avatar"')
        ->toContain('SR')
        ->toContain('font-mono')
        ->toContain('bg-ink')
        ->toContain('text-surface')
        ->toContain('rounded-full')
        ->toContain('aria-label="Søren Risbøll Frisk"');
});

it('derives a single initial from a single-word name', function () {
    expect(Blade::render('<x-sigta::avatar name="Plumb" />'))->toContain('>P<');
});

it('renders an image when src is given', function () {
    $html = Blade::render('<x-sigta::avatar src="/img/sf.jpg" name="Søren Frisk" />');

    expect($html)
        ->toContain('<img')
        ->toContain('src="/img/sf.jpg"')
        ->toContain('alt="Søren Frisk"')
        ->toContain('object-cover')
        ->not->toContain('<span');
});

it('prefers an explicit alt over the name', function () {
    $html = Blade::render('<x-sigta::avatar src="/img/sf.jpg" name="Søren Frisk" alt="Portrait of Søren" />');

    expect($html)->toContain('alt="Portrait of Søren"');
});

it('supports the square shape and line tone', function () {
    $html = Blade::render('<x-sigta::avatar name="Søren Frisk" shape="square" tone="line" />');

    expect($html)
        ->toContain('rounded-base')
        ->toContain('bg-line')
        ->toContain('text-ink')
        ->not->toContain('rounded-full');
});

it('renders each size', function (string $size, string $class) {
    expect(Blade::render('<x-sigta::avatar name="Søren Frisk" size="'.$size.'" />'))->toContain($class);
})->with([
    ['xs', 'size-6'],
    ['sm', 'size-8'],
    ['base', 'size-10'],
    ['lg', 'size-14'],
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
        ->toContain('Søren Frisk')
        ->toContain('Studio lead')
        ->toContain('font-head')
        ->toContain('text-graphite');
});

it('reverses the profile layout', function () {
    $html = Blade::render('<x-sigta::profile name="Søren Frisk" meta="Studio lead" reverse />');

    expect($html)
        ->toContain('flex-row-reverse')
        ->toContain('text-right');
});

it('forwards attributes on the profile root', function () {
    expect(Blade::render('<x-sigta::profile name="Søren Frisk" data-test="profile" />'))
        ->toContain('data-test="profile"');
});
