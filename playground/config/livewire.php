<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Navigate (SPA mode)
    |--------------------------------------------------------------------------
    |
    | Livewire's default progress bar color is a raw hex (#2299dd) injected
    | into every response via @livewireStyles. Point it at the Sigta accent
    | token instead — no raw hex in rendered pages, and on-brand.
    |
    | Note: mergeConfigFrom() is shallow, so this array must carry every
    | `navigate` key we rely on, not just the color.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => 'var(--sigta-accent)',
    ],

];
