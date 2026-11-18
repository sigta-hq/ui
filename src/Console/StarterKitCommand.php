<?php

namespace Sigta\Ui\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Finder\Finder;

/**
 * sigta:starter-kit — swap a fresh Laravel Livewire starter kit onto Sigta UI.
 *
 * The starter kit scaffolds its auth, settings and dashboard views with another
 * UI library. This replaces their markup with Sigta components from
 * stubs/starter-kit, leaving the kit's PHP (the Livewire classes at the top of
 * its single-file components) untouched, so its behaviour and its tests stay
 * the kit's own. Only files the app has are replaced: a feature turned off at
 * install (registration, email verification, 2FA, passkeys, password
 * confirmation) stays off, and the stubs' `@feature:<name>` sections follow the
 * same choice. Then it drops what only the previous library needed (the dark
 * mode Appearance page, its view overrides, its Composer package) and runs
 * sigta:install.
 */
class StarterKitCommand extends Command
{
    protected $signature = 'sigta:starter-kit
                            {--force : Replace the views without asking}
                            {--keep-package : Leave the starter kit\'s previous UI package installed}';

    protected $description = 'Replace the Laravel Livewire starter kit\'s views with Sigta UI ones.';

    /**
     * Each optional starter kit feature, and the view the kit deletes when it is
     * turned off. The view being there is how we know the feature is on.
     */
    private const FEATURES = [
        'registration' => 'pages/auth/register.blade.php',
        'email-verification' => 'pages/auth/verify-email.blade.php',
        '2fa' => 'pages/auth/two-factor-challenge.blade.php',
        'passkeys' => 'components/passkey-verify.blade.php',
        'password-confirmation' => 'pages/auth/confirm-password.blade.php',
    ];

    /** The starter kit's previous UI package, and where it kept its view overrides. */
    private const PREVIOUS_PACKAGE = 'livewire/flux';

    private const PREVIOUS_VIEWS = 'flux';

    /** Markup or PHP that still needs the previous package once the swap is done. */
    private const PREVIOUS_USAGE = '/<flux:|@flux|Flux::|Flux\\\\|flux\.css|livewire\/flux/';

    /** The stub that stands in for the Appearance page when its route cannot be removed. */
    private const APPEARANCE_STUB = 'resources/views/pages/settings/appearance.blade.php';

    public function handle(Filesystem $files, Composer $composer): int
    {
        if ($problem = $this->unsupported($files)) {
            $this->components->error($problem);

            return self::FAILURE;
        }

        $features = $this->features($files);
        $replacements = $this->replacements($files);
        $appearance = $this->appearanceViews($files);

        // A removable Appearance page is deleted rather than replaced.
        if ($appearance !== []) {
            unset($replacements[self::APPEARANCE_STUB]);
        }

        $this->components->info('Moving the Laravel starter kit onto Sigta UI');
        $this->components->twoColumnDetail('Features', implode(', ', $features) ?: 'none');
        $this->components->twoColumnDetail('Files to replace', (string) count($replacements));
        if ($appearance !== []) {
            $this->components->twoColumnDetail('Appearance page', 'removed (Sigta has one light theme)');
        }
        $this->newLine();

        if (! $this->option('force') && ! $this->components->confirm('Replace these files with Sigta UI versions? Changes you made to them are lost', false)) {
            $this->components->warn('Nothing changed.');

            return self::SUCCESS;
        }

        foreach ($replacements as $stub => $target) {
            $this->replace($files, $stub, $target, $features);
        }

        if ($appearance !== []) {
            $this->removeAppearance($files, $appearance);
        }

        $this->rewritePhpToasts($files);

        $this->newLine();
        $this->call('sigta:install');

        if (! $this->option('keep-package')) {
            $this->removePreviousPackage($files, $composer);
        }

        $this->callSilent('view:clear');

        $this->newLine();
        $this->components->info('Next: run npm run build (or npm run dev) to compile the new CSS.');

        return self::SUCCESS;
    }

    /**
     * Why this app is not a starter kit this command can convert, or null when
     * it is. Variants it has no stubs for are refused outright rather than
     * converted halfway.
     */
    protected function unsupported(Filesystem $files): ?string
    {
        $composerJson = json_decode((string) @file_get_contents(base_path('composer.json')), true) ?: [];
        $requires = array_keys(($composerJson['require'] ?? []) + ($composerJson['require-dev'] ?? []));

        if (in_array('laravel/workos', $requires, true)) {
            return 'This app uses the WorkOS edition of the starter kit, which sigta:starter-kit does not cover yet.';
        }

        if ($files->isDirectory(resource_path('views/pages/teams'))) {
            return 'This app uses the teams edition of the starter kit, which sigta:starter-kit does not cover yet.';
        }

        if ($files->isDirectory(resource_path('views/livewire/settings')) || $files->isDirectory(app_path('Livewire/Settings'))) {
            return 'This app uses the starter kit\'s class-based Livewire components; sigta:starter-kit covers the default single-file components only.';
        }

        if (! $files->exists(resource_path('views/pages/settings/layout.blade.php'))
            || ! $files->exists(resource_path('views/layouts/app/sidebar.blade.php'))) {
            return 'No Laravel Livewire starter kit found: expected resources/views/pages/settings/layout.blade.php and resources/views/layouts/app/sidebar.blade.php.';
        }

        return null;
    }

    /**
     * The starter kit features this app was installed with.
     *
     * @return list<string>
     */
    protected function features(Filesystem $files): array
    {
        return array_keys(array_filter(
            self::FEATURES,
            fn (string $view) => $files->exists(resource_path('views/'.$view)),
        ));
    }

    /**
     * Stub → app file, for every stub whose file the app has. A stub with no
     * counterpart belongs to a feature the app was installed without.
     *
     * @return array<string, string>
     */
    protected function replacements(Filesystem $files): array
    {
        $map = [];

        foreach (Finder::create()->files()->in($this->stubPath())->sortByName() as $stub) {
            $relative = str_replace('\\', '/', $stub->getRelativePathname());

            if ($target = $this->target($files, $relative)) {
                $map[$relative] = $target;
            }
        }

        return $map;
    }

    /**
     * The app file a stub replaces. Single-file components carry Livewire's ⚡
     * prefix when the kit's installer renamed them, and none when it did not.
     */
    protected function target(Filesystem $files, string $relative): ?string
    {
        $plain = base_path($relative);
        $prefixed = dirname($plain).'/⚡'.basename($plain);

        foreach ([$prefixed, $plain] as $candidate) {
            if ($files->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Write a stub over its app file. A single-file component keeps its PHP
     * block and only its markup is replaced.
     *
     * @param  list<string>  $features
     */
    protected function replace(Filesystem $files, string $stub, string $target, array $features): void
    {
        $markup = $this->resolveFeatures($files->get($this->stubPath($stub)), $features);
        $existing = $files->get($target);

        if (preg_match('/\A<\?php.*?\?>/s', $existing, $class)) {
            $markup = $class[0]."\n\n".ltrim($markup);
        }

        $files->put($target, $markup);

        $this->components->twoColumnDetail('Replaced', $this->relative($target));
    }

    /**
     * Keep each `@feature:<name>` section of a stub when the app has that
     * feature, and drop it when it does not. The marker lines go either way.
     *
     * @param  list<string>  $features
     */
    protected function resolveFeatures(string $content, array $features): string
    {
        return preg_replace_callback(
            '/^[ \t]*\{\{-- @feature:([a-z0-9-]+) --\}\}\R(.*?)^[ \t]*\{\{-- @endfeature:\1 --\}\}\R/ms',
            fn (array $section) => in_array($section[1], $features, true) ? $section[2] : '',
            $content,
        );
    }

    /**
     * The Appearance page only switches the previous library's dark mode, which
     * Sigta does not have. Its views, when its route can be taken out cleanly;
     * otherwise none, and the page keeps a Sigta stub instead.
     *
     * @return list<string>
     */
    protected function appearanceViews(Filesystem $files): array
    {
        $routes = base_path('routes/settings.php');

        if (! $files->exists($routes) || $this->withoutAppearanceRoute($files->get($routes)) === null) {
            return [];
        }

        return array_values(array_filter(
            [resource_path('views/pages/settings/⚡appearance.blade.php'), resource_path('views/pages/settings/appearance.blade.php')],
            fn (string $view) => $files->exists($view),
        ));
    }

    protected function withoutAppearanceRoute(string $routes): ?string
    {
        $without = preg_replace(
            '/^[ \t]*Route::livewire\(\s*[\'"]settings\/appearance[\'"][^;]*?->name\(\s*[\'"]appearance\.edit[\'"]\s*\);[ \t]*\R([ \t]*\R)?/m',
            '',
            $routes,
            1,
            $count,
        );

        return $count === 1 ? $without : null;
    }

    /**
     * Take the Appearance page out: its route, its views, and the kit's one test
     * that borrowed its URL to check the verified middleware (the dashboard sits
     * behind the same middleware).
     *
     * @param  list<string>  $views
     */
    protected function removeAppearance(Filesystem $files, array $views): void
    {
        $routes = base_path('routes/settings.php');
        $files->put($routes, $this->withoutAppearanceRoute($files->get($routes)));
        $this->components->twoColumnDetail('Removed route', 'appearance.edit from routes/settings.php');

        foreach ($views as $view) {
            $files->delete($view);
            $this->components->twoColumnDetail('Deleted', $this->relative($view));
        }

        if (! $files->isDirectory(base_path('tests'))) {
            return;
        }

        foreach (Finder::create()->files()->in(base_path('tests'))->name('*.php')->contains('appearance.edit') as $test) {
            $files->put($test->getRealPath(), preg_replace(
                '/route\(\s*([\'"])appearance\.edit\1\s*\)/',
                "route('dashboard')",
                $test->getContents(),
            ));
            $this->components->twoColumnDetail('Updated', $this->relative($test->getRealPath()));
        }
    }

    /**
     * Point the kit's toasts at Sigta: the previous library's toast calls
     * become `Sigta::toast(...)`, which takes the same named arguments, in
     * single-file components and in app/. A file that still calls that library
     * for anything else keeps its import, and is reported before the package
     * would be removed.
     */
    protected function rewritePhpToasts(Filesystem $files): void
    {
        $roots = array_filter([resource_path('views'), app_path()], fn (string $dir) => $files->isDirectory($dir));

        if ($roots === []) {
            return;
        }

        foreach (Finder::create()->files()->in($roots)->name('*.php')->contains('Flux::toast(') as $file) {
            $php = str_replace('Flux::toast(', 'Sigta::toast(', $file->getContents());
            $import = 'use Sigta\\Ui\\Facades\\Sigta;';
            $hasImport = str_contains($php, $import);

            // The old import goes once nothing calls the previous library; the
            // Sigta import takes its line, or follows the first import.
            if (! str_contains($php, 'Flux::')) {
                $php = preg_replace_callback('/^use Flux\\\\Flux;\R/m', function () use ($import, &$hasImport) {
                    $line = $hasImport ? '' : $import."\n";
                    $hasImport = true;

                    return $line;
                }, $php, 1);
            }

            if (! $hasImport) {
                $php = preg_replace_callback('/^use [^;]+;\R/m', fn (array $use) => $use[0].$import."\n", $php, 1);
            }

            $files->put($file->getRealPath(), $php);
            $this->components->twoColumnDetail('Updated toasts', $this->relative($file->getRealPath()));
        }
    }

    /**
     * Remove the previous UI package and its view overrides once nothing in the
     * app uses them any more. Anything left is listed instead, and the package
     * stays.
     */
    protected function removePreviousPackage(Filesystem $files, Composer $composer): void
    {
        // Resolved from the container it may not know where the app lives.
        $composer->setWorkingPath(base_path());

        if (! $composer->hasPackage(self::PREVIOUS_PACKAGE)) {
            return;
        }

        $this->newLine();

        if ($leftovers = $this->leftovers($files)) {
            $this->components->warn('These files still use the starter kit\'s previous UI package, so it stays installed:');
            $this->components->bulletList($leftovers);
            $this->line('  Move them to Sigta components, then run: <fg=cyan>composer remove '.self::PREVIOUS_PACKAGE.'</>');

            return;
        }

        $overrides = resource_path('views/'.self::PREVIOUS_VIEWS);
        if ($files->isDirectory($overrides)) {
            $files->deleteDirectory($overrides);
            $this->components->twoColumnDetail('Deleted', $this->relative($overrides));
        }

        $this->components->info('Removing '.self::PREVIOUS_PACKAGE.' — nothing uses it any more.');

        if (! $composer->removePackages([self::PREVIOUS_PACKAGE], false, $this->output)) {
            $this->components->warn('Composer could not remove it. Run: composer remove '.self::PREVIOUS_PACKAGE);
        }
    }

    /**
     * App files that still use the previous UI package, outside its own view
     * overrides (those go with the package).
     *
     * @return list<string>
     */
    protected function leftovers(Filesystem $files): array
    {
        $roots = array_filter(
            [resource_path('views'), resource_path('css'), resource_path('js'), app_path(), base_path('routes')],
            fn (string $dir) => $files->isDirectory($dir),
        );

        if ($roots === []) {
            return [];
        }

        $found = [];
        foreach (Finder::create()->files()->in($roots)->exclude(self::PREVIOUS_VIEWS)->contains(self::PREVIOUS_USAGE)->sortByName() as $file) {
            $found[] = $this->relative($file->getRealPath());
        }

        return $found;
    }

    protected function stubPath(string $path = ''): string
    {
        return rtrim(__DIR__.'/../../stubs/starter-kit/'.$path, '/');
    }

    protected function relative(string $path): string
    {
        return ltrim(str_replace(base_path(), '', $path), '/\\');
    }
}
