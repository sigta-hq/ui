<?php

namespace Sigta\Ui;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sigta\Ui\Console\InstallCommand;

class SigtaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootComponents();
        $this->bootAssets();
        $this->bootPublishing();
    }

    public function register(): void
    {
        $this->app->singleton(Sigta::class);

        // sigta:install — idempotent fonts + LLM agent context sync. Safe to
        // re-run and to wire into a composer post-autoload-dump hook.
        $this->commands([
            InstallCommand::class,
        ]);
    }

    protected function bootComponents(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sigta');

        // Anonymous components: <x-sigta::button>, <x-sigta::input>, ...
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'sigta');

        // Ergonomic prefix: <sigta:button> compiles to <x-sigta::button>.
        // Must run before Blade's component-tag compiler, so it hooks the
        // raw-string stage. Matches opening, self-closing, and closing tags,
        // including dotted subcomponents (<sigta:dropdown.item>) — the `::`
        // guard leaves <x-slot:…> and already-namespaced tags untouched.
        Blade::prepareStringsForCompilationUsing(function (string $view): string {
            return preg_replace(
                ['/<sigta:([a-zA-Z0-9\.\-]+)/', '/<\/sigta:([a-zA-Z0-9\.\-]+)>/'],
                ['<x-sigta::$1', '</x-sigta::$1>'],
                $view,
            );
        });
    }

    protected function bootAssets(): void
    {
        // Route-served JS (Livewire style): no publish step, always in
        // sync with the installed package version.
        Route::get('/sigta/sigta.js', AssetController::class)
            ->name('sigta.assets.js');

        Route::get('/sigta/editor.js', EditorAssetController::class)
            ->name('sigta.assets.editor');

        Blade::directive('sigtaScripts', function () {
            return "<?php echo \Sigta\Ui\SigtaServiceProvider::scriptTag(); ?>";
        });

        Blade::directive('sigtaEditorScripts', function () {
            return "<?php echo \Sigta\Ui\SigtaServiceProvider::editorScriptTag(); ?>";
        });
    }

    protected function bootPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Fonts must live under public/ so @font-face URLs resolve.
        $this->publishes([
            __DIR__.'/../packages/contract/fonts' => public_path('vendor/sigta/fonts'),
        ], 'sigta-fonts');
    }

    public static function scriptTag(): string
    {
        $hash = substr(md5_file(__DIR__.'/../dist/sigta.js') ?: 'dev', 0, 8);
        $url = route('sigta.assets.js', ['v' => $hash], false);

        return '<script src="'.$url.'" defer></script>';
    }

    public static function editorScriptTag(): string
    {
        $hash = substr(md5_file(__DIR__.'/../dist/sigta-editor.js') ?: 'dev', 0, 8);
        $url = route('sigta.assets.editor', ['v' => $hash], false);

        return '<script src="'.$url.'" defer></script>';
    }
}
