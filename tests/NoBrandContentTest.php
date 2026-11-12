<?php

use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\Finder;

// The package is public: nothing in it may carry Sigta's own content, contact
// details, clients or logo. Sigta's site passes its copy in from outside
// (playground/resources/content/sigta-landing.php is the reference).

function shippedTextFiles(): Finder
{
    $root = dirname(__DIR__);

    return Finder::create()
        ->files()
        ->in([$root.'/src', $root.'/resources', $root.'/stubs'])
        ->append([$root.'/dist/sigta.js'])
        ->notName(['*.woff2', 'OFL.txt']);
}

it('ships no Sigta contact details, clients, copy or logo glyph', function (string $needle) {
    $hits = [];

    foreach (shippedTextFiles() as $file) {
        if (str_contains($file->getContents(), $needle)) {
            $hits[] = $file->getRelativePathname() ?: $file->getFilename();
        }
    }

    expect($hits)->toBe([], "\"{$needle}\" found in: ".implode(', ', $hits));
})->with([
    'email domain' => 'sigta.dk',
    'booking link' => 'calendly.com',
    'personal profile' => 'linkedin.com/in/',
    'company number' => 'CVR',
    'founder name' => 'Søren',
    'employer' => 'Donor Network',
    'client' => 'Park Styling',
    'client 2' => 'Jelling',
    'positioning copy' => 'tech-partner',
    'wordmark' => 'SIGTA',
    'logo glyph path' => 'M20 12 L11 29',
]);

it('ships no images', function () {
    $images = Finder::create()->files()->in(dirname(__DIR__).'/resources')->name(['*.svg', '*.png', '*.jpg', '*.jpeg', '*.webp', '*.gif']);

    expect(iterator_count($images))->toBe(0);
});

it('renders no visible text from a section given no props', function (string $section) {
    $text = trim(html_entity_decode(strip_tags(Blade::render("<x-sigta::section.{$section} />"))));

    expect($text)->toBe('');
})->with(['about', 'cta', 'faq', 'features', 'footer', 'hero', 'pricing', 'proof', 'quote', 'recognition']);

it('renders no logo or name from a bare brand lockup', function () {
    $text = trim(strip_tags(Blade::render('<x-sigta::brand />')));

    expect($text)->toBe('');
});
