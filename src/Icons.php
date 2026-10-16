<?php

namespace Sigta\Ui;

use InvalidArgumentException;

/**
 * The pinned Lucide set both runtimes draw from (packages/contract/icons),
 * so <sigta:icon> and the Svelte <Icon> can never render different versions.
 */
class Icons
{
    /** @var array<string, string>|null */
    private static ?array $icons = null;

    /** The inner SVG markup of an icon. */
    public static function markup(string $name): string
    {
        self::$icons ??= require __DIR__.'/../packages/contract/icons/lucide.php';

        return self::$icons[$name] ?? throw new InvalidArgumentException("Unknown Sigta icon [{$name}].");
    }

    public static function has(string $name): bool
    {
        self::$icons ??= require __DIR__.'/../packages/contract/icons/lucide.php';

        return isset(self::$icons[$name]);
    }
}
