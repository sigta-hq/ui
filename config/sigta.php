<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Loader
    |--------------------------------------------------------------------------
    |
    | What <sigta:loader> draws, and so what every busy button, save-state and
    | job shows while work runs. null draws the Sigta compass.
    |
    | Set a Blade view name (e.g. 'components.spinner') to draw your own. The
    | loader keeps its box, size, data-state, progress and accessible name, and
    | renders your view inside it with $state, $value (0–100 or null), $size
    | and $label. State can change in the browser without a re-render, so key
    | motion off the box's data-state:
    |
    |   <svg class="group-data-[state=working]/loader:animate-spin" …>
    |
    | <sigta:loader variant="compass"> or variant="sketch" still draws the
    | built-in loader where you ask for it by name.
    |
    */

    'loader' => null,

];
