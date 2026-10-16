<?php

uses(Tests\TestCase::class)->in(__DIR__);

/**
 * A component's stylesheet source in the contract — where its look lives now
 * that the markup only carries data-* contract attributes.
 */
function contractCss(string $component): string
{
    return file_get_contents(dirname(__DIR__).'/packages/contract/css/components/'.$component.'.css');
}
