<?php

/*
 * Renders Blade snippets with the package booted (Testbench), for the
 * conformance check and the docs build:
 *
 *   php scripts/render-blade.php fixtures.json > rendered.json
 *
 * The input maps ids to Blade source; the output maps the same ids to HTML,
 * or to {"error": "..."} when a snippet throws.
 */

use BladeUI\Icons\BladeIconsServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\LivewireServiceProvider;
use MallardDuck\LucideIcons\BladeLucideIconsServiceProvider;
use Orchestra\Testbench\Foundation\Application;
use Sigta\Ui\SigtaServiceProvider;

require __DIR__.'/../vendor/autoload.php';

$fixtures = json_decode(file_get_contents($argv[1] ?? 'php://stdin'), true, flags: JSON_THROW_ON_ERROR);

$app = Application::create(options: [
    'extra' => ['providers' => [
        LivewireServiceProvider::class,
        BladeIconsServiceProvider::class,
        BladeLucideIconsServiceProvider::class,
        SigtaServiceProvider::class,
    ]],
]);
$app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));

$out = [];
foreach ($fixtures as $id => $source) {
    try {
        $out[$id] = Blade::render($source);
    } catch (Throwable $e) {
        $out[$id] = ['error' => $e->getMessage()];
    }
}

echo json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
