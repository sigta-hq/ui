<?php

/**
 * The fields each Alpine component in the runtime declares at the top of its
 * object, and the fields its code writes through `this`.
 *
 * @return array<string, array{declared: list<string>, assigned: list<string>}>
 */
function runtimeAlpineComponents(string $source): array
{
    preg_match_all("/Alpine\\.data\\('(\\w+)',\\s*\\([^)]*\\)\\s*=>\\s*\\(\\{/", $source, $matches, PREG_OFFSET_CAPTURE);

    $components = [];

    foreach ($matches[0] as $index => [$opening, $offset]) {
        $start = $offset + strlen($opening);
        $depth = 1;
        $end = $start;

        while ($depth > 0 && $end < strlen($source)) {
            $depth += match ($source[$end]) {
                '{' => 1,
                '}' => -1,
                default => 0,
            };
            $end++;
        }

        $body = substr($source, $start, $end - $start - 1);
        $declared = [];
        $level = 0;

        foreach (explode("\n", $body) as $line) {
            if ($level === 0 && preg_match('/^\s*(?:get\s+|async\s+)?([A-Za-z_$][\w$]*)\s*[:(,]/', $line, $key)) {
                $declared[] = $key[1];
            }

            $level += substr_count($line, '{') - substr_count($line, '}');
        }

        preg_match_all('/this\.([A-Za-z_$][\w$]*)\s*(?:=(?!=)|\+\+|--|[-+|&?]{1,2}=)/', $body, $assigned);

        $components[$matches[1][$index][0]] = [
            'declared' => array_values(array_unique($declared)),
            'assigned' => array_values(array_unique($assigned[1])),
        ];
    }

    return $components;
}

it('declares every field a component writes, so none lands on an ancestor scope', function () {
    $components = runtimeAlpineComponents(file_get_contents(__DIR__.'/../resources/js/sigta.js'));

    expect(array_keys($components))->toContain('sigtaModal', 'sigtaFloatingBar', 'sigtaCount', 'sigtaSaveState', 'sigtaJob');

    foreach ($components as $name => $component) {
        expect(array_values(array_diff($component['assigned'], $component['declared'])))
            ->toBe([], "{$name} writes fields it does not declare");
    }
});

it('declares every field the editor runtime writes', function () {
    $components = runtimeAlpineComponents(file_get_contents(__DIR__.'/../resources/js/editor.js'));

    expect($components)->toHaveKey('sigtaEditor');

    expect(array_values(array_diff($components['sigtaEditor']['assigned'], $components['sigtaEditor']['declared'])))
        ->toBe([], 'sigtaEditor writes fields it does not declare');
});

it('ships the runtime verbatim in dist', function () {
    expect(file_get_contents(__DIR__.'/../dist/sigta.js'))
        ->toBe(file_get_contents(__DIR__.'/../resources/js/sigta.js'));
});

it('leaves a nested component its own rows when the parent morphs', function () {
    // The parent's new HTML never carries a child component's rows, so they
    // would all read as dropped and fold away on every parent re-render.
    expect(file_get_contents(__DIR__.'/../resources/js/sigta.js'))
        ->toContain("const owner = el.closest('[wire\\\\:id]');")
        ->toContain(".filter((row) => row.parentElement?.closest('[wire\\\\:id]') === owner)");
});

it('merges the text either side of a row that leaves', function () {
    // Two text nodes in a row make the next morph move the row after them into
    // line, which replays that row's animations (an inbox strike drawn twice).
    expect(file_get_contents(__DIR__.'/../resources/js/sigta.js'))
        ->toContain('if (options.remove !== false) detach(el);')
        ->toContain('before.nodeValue += after.nodeValue;');
});

it('marks a button busy only once its action outlasts a beat', function () {
    // A CSS delay would not do: reduced motion drops every animation, and the
    // compass would flash on every quick click.
    expect(file_get_contents(__DIR__.'/../resources/js/sigta.js'))
        ->toContain("attributeFilter: ['data-loading']")
        ->toContain("duration('--sigta-dur-busy', 500, target)")
        ->toContain("button.setAttribute('data-sigta-busy', '')");

    expect(file_get_contents(__DIR__.'/../resources/css/core.css'))
        ->toContain('[data-sigta="button"][data-sigta-busy] > .sigta-button-busy')
        ->not->toContain('> .sigta-button-label {');
});

it('crosses out every line a struck title wraps onto, over its words', function () {
    // A positioned line covers one box spanning a wrapped title's lines, so it
    // landed under the first line and read as an underline.
    $css = file_get_contents(__DIR__.'/../resources/css/core.css');

    expect($css)
        ->not->toContain('[data-sigta-strike]::after')
        ->toContain('background-size: 100% 0.11em, 100% 100%;')
        ->toContain('background-clip: border-box, text;')
        ->toContain('[data-sigta-done] [data-sigta-strike] { text-decoration: line-through; }');
});

