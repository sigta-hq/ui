<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

beforeEach(function () {
    app()->setLocale('da');
});

/** Every __('…') key in the package views, usage comments left out. */
function packageTranslationKeys(): array
{
    $keys = [];

    foreach (Finder::create()->files()->in(dirname(__DIR__).'/resources/views')->name('*.blade.php') as $file) {
        $source = preg_replace('/\{\{--.*?--\}\}/s', '', $file->getContents());

        preg_match_all("/__\\('((?:[^'\\\\]|\\\\.)*)'/", $source, $matches);

        foreach ($matches[1] as $key) {
            $keys[stripslashes($key)] = true;
        }
    }

    return array_keys($keys);
}

it('ships Danish for every word the components write, and nothing else', function () {
    $danish = json_decode(File::get(dirname(__DIR__).'/resources/lang/da.json'), true, flags: JSON_THROW_ON_ERROR);
    $keys = packageTranslationKeys();

    expect(array_values(array_diff($keys, array_keys($danish))))->toBe([], 'Missing from resources/lang/da.json')
        ->and(array_values(array_diff(array_keys($danish), $keys)))->toBe([], 'No longer used by any view');
});

it('keeps every placeholder in the Danish text', function () {
    $danish = json_decode(File::get(dirname(__DIR__).'/resources/lang/da.json'), true, flags: JSON_THROW_ON_ERROR);
    $placeholders = fn (string $text) => preg_match_all('/:([a-z]+)/', $text, $m) ? $m[1] : [];
    // A unit key glues its letter to the placeholder (":hoursh"), so compare by prefix.
    $covers = fn (array $from, array $to) => collect($from)->every(fn ($a) => collect($to)->contains(fn ($b) => str_starts_with($a, $b) || str_starts_with($b, $a)));

    foreach ($danish as $key => $text) {
        expect($text)->not->toBe('', "Empty Danish for \"{$key}\"")
            ->and($covers($placeholders($key), $placeholders($text)) && $covers($placeholders($text), $placeholders($key)))
            ->toBeTrue("\"{$text}\" does not keep the placeholders of \"{$key}\"");
    }
});

it('lets an app override a word in its own lang file', function () {
    $lang = sys_get_temp_dir().'/sigta-app-lang-'.bin2hex(random_bytes(4));
    File::ensureDirectoryExists($lang);
    File::put($lang.'/da.json', json_encode(['Previous month' => 'Sidste måned']));

    app()->useLangPath($lang);
    app()->forgetInstance('translation.loader');
    app()->forgetInstance('translator');
    app()->setLocale('da');

    try {
        expect(Blade::render('<x-sigta::calendar value="2026-05-04" />'))
            ->toContain('aria-label="Sidste måned"')
            ->toContain('aria-label="Næste måned"');
    } finally {
        File::deleteDirectory($lang);
    }
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
        ->and($carousel)->toContain("'Gå til dias :number'.replace(':number', i)");
});

it('translates duration units on the server and in the runtime', function () {
    $duration = Blade::render('<x-sigta::duration :minutes="75" />');
    $input = Blade::render('<x-sigta::duration-input :value="120" />');

    expect($duration)->toContain('>1 t 15 min</span>')
        ->and($input)->toContain('>2 t</button>')
        ->and($input)->toContain("hoursUnit: ':hours t'");
});

it('translates text the Alpine templates fall back to', function () {
    expect(Blade::render('<x-sigta::toast />'))->toContain("toast.link.text || 'Vis'")
        ->and(Blade::render('<x-sigta::autocomplete />'))->toContain('Ingen resultater.');
});

it('hands the runtime its translated words through @sigtaScripts', function () {
    $html = Blade::render('@sigtaScripts');

    preg_match('/data-sigta-lang="([^"]*)"/', $html, $match);
    $words = json_decode(html_entity_decode($match[1] ?? '', ENT_QUOTES), true);

    expect($words)->toMatchArray(['Done' => 'Færdig', 'nice!' => 'flot!', 'well done!' => 'godt gået!']);
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
