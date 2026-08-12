<?php

namespace Sigta\Ui\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void toast(?string $text = null, ?string $heading = null, ?string $variant = null, ?int $duration = null, ?array $link = null)
 *
 * @see \Sigta\Ui\Sigta
 */
class Sigta extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sigta\Ui\Sigta::class;
    }
}
