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
