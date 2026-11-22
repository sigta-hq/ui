<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

// Apps translate the package's own words in their lang/<locale>.json. The
// package ships none, so each test writes a throwaway one.
beforeEach(function () {
    $this->langPath = sys_get_temp_dir().'/sigta-lang-'.bin2hex(random_bytes(4));
    File::ensureDirectoryExists($this->langPath);
    File::put($this->langPath.'/da.json', json_encode([
        'Previous month' => 'Forrige måned',
        'May' => 'maj',
        'Mo' => 'Ma',
        'Monday' => 'mandag',
        'Select a date' => 'Vælg en dato',
        'Page :number' => 'Side :number',
        'Pagination Navigation' => 'Sidenavigation',
        'Go to slide :number' => 'Vis slide :number',
        'No matches.' => 'Ingen resultater.',
        'View' => 'Se',
        ':hoursh' => ':hourst',
        ':minutesm' => ':minutesmin',
        'Done' => 'Færdig',
        'nice!' => 'flot!',
    ]));

    app('translator')->addJsonPath($this->langPath);
    app()->setLocale('da');
});

afterEach(function () {
    File::deleteDirectory($this->langPath);
});

it('translates the calendar chrome, month and day names', function () {
    $html = Blade::render('<x-sigta::calendar value="2026-05-04" />');

    expect($html)
        ->toContain('aria-label="Forrige måned"')
        ->toContain('maj 2026')
        ->toContain('aria-label="4 maj 2026"')
        ->toContain('aria-label="mandag"')
        ->toContain('>Ma</span>')
        ->not->toContain('Previous month')
        ->not->toContain('May 2026');
});

it('shows the picked date with a translated month and placeholder', function () {
    $html = Blade::render('<x-sigta::date-picker value="2026-05-04" />');
    $empty = Blade::render('<x-sigta::date-picker />');

    expect($html)->toContain('4 maj 2026')->not->toContain('4 May 2026')
        ->and($empty)->toContain('placeholder="Vælg en dato"');
});

it('translates labels that carry a number', function () {
    $pagination = Blade::render('<x-sigta::pagination :pages="5" :current="1" url="?page=%d" />');
    $carousel = Blade::render('<x-sigta::carousel><x-sigta::carousel.slide>One</x-sigta::carousel.slide></x-sigta::carousel>');

    expect($pagination)
        ->toContain('aria-label="Sidenavigation"')
        ->toContain('aria-label="Side 2"')
        ->and($carousel)->toContain("'Vis slide :number'.replace(':number', i)");
});

it('translates duration units on the server and in the runtime', function () {
    $duration = Blade::render('<x-sigta::duration :minutes="75" />');
    $input = Blade::render('<x-sigta::duration-input :value="120" />');

    expect($duration)->toContain('>1t 15min</span>')
        ->and($input)->toContain('>2t</button>')
        ->and($input)->toContain("hoursUnit: ':hourst'");
});

it('translates text the Alpine templates fall back to', function () {
    expect(Blade::render('<x-sigta::toast />'))->toContain("toast.link.text || 'Se'")
        ->and(Blade::render('<x-sigta::autocomplete />'))->toContain('Ingen resultater.');
});

it('hands the runtime its translated words through @sigtaScripts', function () {
    $html = Blade::render('@sigtaScripts');

    preg_match('/data-sigta-lang="([^"]*)"/', $html, $match);
    $words = json_decode(html_entity_decode($match[1] ?? '', ENT_QUOTES), true);

    expect($words)->toMatchArray(['Done' => 'Færdig', 'nice!' => 'flot!', 'well done!' => 'well done!']);
});

it('keeps the runtime words and the script tag in step', function () {
    $runtime = File::get(dirname(__DIR__).'/resources/js/sigta.js');
    $tag = File::get(dirname(__DIR__).'/resources/views/scripts.blade.php');

    preg_match_all("/trans\\('([^']+)'\\)/", $runtime, $used);

    foreach (array_unique($used[1]) as $text) {
        expect($tag)->toContain("'{$text}' => __('{$text}')");
    }
});

it('writes no literal words into text attributes', function () {
    $hits = [];
    $patterns = [
        // A static attribute whose value is words, not a Blade echo.
        '/(?<![\w:.-])(aria-label|aria-roledescription|aria-description|aria-valuetext|title|placeholder|alt)="(?!https?:\/\/")[^"{]*[A-Za-z]/',
        // An Alpine binding that starts with a quoted literal.
        '/(?:x-bind)?:(aria-label|aria-roledescription|title|placeholder|alt)="\'[A-Za-z]/',
        // A text default in a props or attribute array.
        "/'(aria-label|placeholder|label|title|helper)' => '[A-Za-z]/",
    ];

    foreach (Finder::create()->files()->in(dirname(__DIR__).'/resources/views')->name('*.blade.php') as $file) {
        // Usage examples in comments show app code, which passes its own text.
        $source = preg_replace('/\{\{--.*?--\}\}/s', '', $file->getContents());

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $source, $matches)) {
                foreach ($matches[0] as $match) {
                    $hits[] = $file->getRelativePathname().': '.$match;
                }
            }
        }
    }

    expect($hits)->toBe([], "Wrap these in __():\n".implode("\n", $hits));
});
