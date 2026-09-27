<?php

use Illuminate\Support\Facades\Blade;

it('renders the toast outlet with an action button template', function () {
    $html = Blade::render('<x-sigta::toast />');

    expect($html)
        ->toContain('data-sigta="toast"')
        ->toContain('x-data="sigtaToasts"')
        ->toContain('toast.action && toast.action.text')
        ->toContain('x-on:click="act(toast)"')
        ->toContain('x-text="toast.action.text"');
});
