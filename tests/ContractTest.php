<?php

/*
 * The Blade runtime against the contract: every manifest component has a
 * Blade view whose @props are exactly the manifest's props (names and
 * defaults), so the two runtimes cannot drift apart on their API.
 */

function contractManifest(): array
{
    static $manifest;

    return $manifest ??= json_decode(file_get_contents(dirname(__DIR__).'/packages/contract/manifest.json'), true)['components'];
}

function bladeViewPath(string $component): string
{
    $base = dirname(__DIR__).'/resources/views/components/'.str_replace('.', '/', $component);

    return is_file($base.'.blade.php') ? $base.'.blade.php' : $base.'/index.blade.php';
}

/** The @props array of a Blade view: name => default, or name => REQUIRED. */
function bladeProps(string $source): array
{
    $start = strpos($source, '@props(');
    if ($start === false) {
        return [];
    }
    $i = $start + strlen('@props(');
    $depth = 0;
    for ($j = $i; $j < strlen($source); $j++) {
        $depth += match ($source[$j]) {
            '[', '(' => 1,
            ']', ')' => -1,
            default => 0,
        };
        if ($depth === 0) {
            break;
        }
    }
    $props = eval('return '.substr($source, $i, $j - $i + 1).';');
    $out = [];
    foreach ($props as $key => $value) {
        is_int($key) ? $out[$value] = 'REQUIRED' : $out[$key] = $value;
    }

    return $out;
}

it('gives every manifest component a Blade view', function () {
    foreach (contractManifest() as $component) {
        expect(is_file(bladeViewPath($component['name'])))->toBeTrue("No Blade view for {$component['name']}");
    }
});

it('declares exactly the manifest props, with the manifest defaults, in each Blade view', function () {
    foreach (contractManifest() as $component) {
        $expected = [];
        foreach ($component['props'] as $name => $spec) {
            if (($spec['blade'] ?? null) === false || ($spec['native'] ?? false)) {
                continue;
            }
            $key = $spec['blade']['name'] ?? $name;
            $expected[$key] = ($spec['required'] ?? false) ? 'REQUIRED' : ($spec['default'] ?? null);
        }
        $actual = bladeProps(file_get_contents(bladeViewPath($component['name'])));

        expect(array_keys($actual))->toEqualCanonicalizing(array_keys($expected), "{$component['name']}: @props names differ from the manifest");
        foreach ($expected as $key => $default) {
            expect($actual[$key])->toEqual($default, "{$component['name']}: default of {$key} differs from the manifest");
        }
    }
});
