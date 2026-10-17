<?php

namespace Sigta\Ui;

/**
 * The brand-default content of the page sections (FAQ entries, service tiles,
 * pricing tiers, footer columns…), kept once in packages/contract/src/sections
 * as JSON that the Svelte sections import too, so a default can never differ
 * between the runtimes.
 */
class Sections
{
    /** @var array<string, array<string, mixed>> */
    private static array $defaults = [];

    /** One default of a section: Sections::get('faq', 'items'). */
    public static function get(string $section, string $key): mixed
    {
        self::$defaults[$section] ??= json_decode(
            file_get_contents(__DIR__.'/../packages/contract/src/sections/'.$section.'.json'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        return self::$defaults[$section][$key];
    }
}
