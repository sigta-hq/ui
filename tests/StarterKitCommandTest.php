<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\Finder;

// sigta:starter-kit runs against a fresh Laravel Livewire starter kit. These
// tests build a cut-down kit in a temp dir: the files the command reads or
// rewrites, in the shape the kit ships them (single-file components with the ⚡
// prefix, the previous UI library's markup, CSS and view overrides).

/**
 * Records what the command asked Composer to remove instead of running it.
 * It starts without a working path, as the container hands it out, so the
 * command has to point it at the app before asking what is installed.
 */
class FakeComposer extends Composer
{
    public array $removed = [];

    public function __construct()
    {
        parent::__construct(new Filesystem);
    }

    public function removePackages(array $packages, bool $dev = false, $output = null, $composerBinary = null)
    {
        $this->removed = [...$this->removed, ...$packages];

        return true;
    }
}

/**
 * A temp app shaped like the starter kit, installed with $features.
 *
 * @param  list<string>  $features
 */
function starterKit(array $features = ['registration', 'email-verification', '2fa', 'passkeys', 'password-confirmation']): string
{
    $root = sys_get_temp_dir().'/sigta-starter-kit-'.bin2hex(random_bytes(4));
    $files = new Filesystem;

    $put = function (string $path, string $content) use ($root, $files) {
        $files->ensureDirectoryExists(dirname($root.'/'.$path));
        $files->put($root.'/'.$path, $content);
    };

    $put('composer.json', json_encode(['require' => ['laravel/framework' => '^13.0', 'livewire/flux' => '^2.0']]));
    $put('resources/css/app.css', "@import 'tailwindcss';\n@import '../../vendor/livewire/flux/dist/flux.css';\n");
    $put('resources/views/flux/icon/layout-grid.blade.php', '<svg data-flux-icon></svg>');
    $put('resources/views/partials/head.blade.php', "@vite(['resources/css/app.css'])\n@fluxAppearance\n");
    $put('resources/views/layouts/app/sidebar.blade.php', "<flux:sidebar>{{ \$slot }}</flux:sidebar>\n@fluxScripts\n");
    $put('resources/views/pages/settings/layout.blade.php', '<flux:navlist>{{ $slot }}</flux:navlist>');
    $put('resources/views/pages/auth/login.blade.php', '<flux:input name="email" />');
    $put('resources/views/components/app-logo-icon.blade.php', '<svg {{ $attributes }}></svg>');

    $put('resources/views/pages/settings/⚡profile.blade.php', <<<'PHP'
        <?php

        use Flux\Flux;
        use Livewire\Component;

        new class extends Component {
            public function updateProfileInformation(): void
            {
                Flux::toast(variant: 'success', text: __('Profile updated.'));
            }
        }; ?>

        <section><flux:input wire:model="name" /></section>
        PHP);

    $put('resources/views/pages/settings/⚡appearance.blade.php', <<<'PHP'
        <?php

        use Livewire\Component;

        new class extends Component {}; ?>

        <flux:radio.group x-model="$flux.appearance" />
        PHP);

    $put('routes/settings.php', <<<'PHP'
        <?php

        use Illuminate\Support\Facades\Route;

        Route::middleware(['auth', 'verified'])->group(function () {
            Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

            Route::livewire('settings/security', 'pages::settings.security')->name('security.edit');
        });
        PHP);

    $put('tests/Feature/Auth/EmailVerificationTest.php', "<?php\n\$this->get(route('appearance.edit'));\n");

    $featureViews = [
        'registration' => 'resources/views/pages/auth/register.blade.php',
        'email-verification' => 'resources/views/pages/auth/verify-email.blade.php',
        '2fa' => 'resources/views/pages/auth/two-factor-challenge.blade.php',
        'passkeys' => 'resources/views/components/passkey-verify.blade.php',
        'password-confirmation' => 'resources/views/pages/auth/confirm-password.blade.php',
    ];

    foreach ($features as $feature) {
        $put($featureViews[$feature], '<flux:button>'.$feature.'</flux:button>');
    }

    return $root;
}

/** Point the app at $root and swap in the fake Composer. */
function useStarterKit(string $root): FakeComposer
{
    app()->setBasePath($root);
    app()->useAppPath($root.'/app');
    app()->usePublicPath($root.'/public');

    $composer = new FakeComposer;
    app()->instance(Composer::class, $composer);

    return $composer;
}

function kitFile(string $root, string $path): string
{
    return file_get_contents($root.'/'.$path);
}

afterEach(function () {
    foreach (glob(sys_get_temp_dir().'/sigta-starter-kit-*') as $dir) {
        (new Filesystem)->deleteDirectory($dir);
    }
});

it('swaps the starter kit views for Sigta ones', function () {
    $root = starterKit();
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    expect(kitFile($root, 'resources/views/layouts/app/sidebar.blade.php'))
        ->toContain('<sigta:shell full>')
        ->toContain('<sigta:toast />')->not->toContain('flux');
    expect(kitFile($root, 'resources/views/partials/head.blade.php'))
        ->toContain('@sigtaScripts')->not->toContain('@flux');
    expect(kitFile($root, 'resources/views/pages/auth/login.blade.php'))
        ->toContain('<sigta:input')->not->toContain('<flux:');
    expect(kitFile($root, 'resources/css/app.css'))
        ->toContain('sigta/ui/resources/css/sigta.css')->not->toContain('flux');
});

it('keeps a single-file component\'s PHP and moves its toasts to Sigta', function () {
    $root = starterKit();
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    $profile = kitFile($root, 'resources/views/pages/settings/⚡profile.blade.php');

    expect($profile)
        ->toStartWith("<?php\n\nuse Sigta\\Ui\\Facades\\Sigta;\nuse Livewire\\Component;")
        ->toContain("Sigta::toast(variant: 'success', text: __('Profile updated.'));")
        ->toContain("}; ?>\n\n<section class=\"w-full\">")
        ->toContain('<sigta:input wire:model="name"')
        ->not->toContain('Flux')
        ->not->toContain('<flux:');
});

it('keeps the sections of features the app has and drops the rest', function () {
    $root = starterKit(['registration']);
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    $login = kitFile($root, 'resources/views/pages/auth/login.blade.php');
    $profile = kitFile($root, 'resources/views/pages/settings/⚡profile.blade.php');

    expect($login)
        ->toContain("route('register')")
        ->not->toContain('<x-passkey-verify')
        ->not->toContain('@feature');

    expect($profile)
        ->toContain('<livewire:pages::settings.delete-user-form />')
        ->not->toContain('hasUnverifiedEmail')
        ->not->toContain('showDeleteUser')
        ->not->toContain('@feature');

    // A feature's own views stay deleted.
    expect(file_exists($root.'/resources/views/pages/auth/verify-email.blade.php'))->toBeFalse()
        ->and(file_exists($root.'/resources/views/components/passkey-verify.blade.php'))->toBeFalse();
});

it('removes the appearance page with its route', function () {
    $root = starterKit();
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    expect(file_exists($root.'/resources/views/pages/settings/⚡appearance.blade.php'))->toBeFalse()
        ->and(kitFile($root, 'routes/settings.php'))
        ->not->toContain('appearance')
        ->toContain("group(function () {\n    Route::livewire('settings/security'")
        ->and(kitFile($root, 'tests/Feature/Auth/EmailVerificationTest.php'))
        ->toContain("route('dashboard')");
});

it('keeps the appearance page on a Sigta stub when its route cannot be taken out', function () {
    $root = starterKit();
    file_put_contents($root.'/routes/settings.php', "<?php\n// routes registered elsewhere\n");
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    expect(kitFile($root, 'resources/views/pages/settings/⚡appearance.blade.php'))
        ->toContain('<sigta:callout>')
        ->not->toContain('flux');
});

it('removes the previous UI package and its overrides once nothing uses them', function () {
    $root = starterKit();
    $composer = useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])->assertSuccessful();

    expect($composer->removed)->toBe(['livewire/flux'])
        ->and(is_dir($root.'/resources/views/flux'))->toBeFalse();
});

it('keeps the previous UI package while the app still uses it', function () {
    $root = starterKit();
    file_put_contents($root.'/resources/views/reports.blade.php', '<flux:table />');
    $composer = useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])
        ->expectsOutputToContain('resources/views/reports.blade.php')
        ->assertSuccessful();

    expect($composer->removed)->toBe([])
        ->and(is_dir($root.'/resources/views/flux'))->toBeTrue();
});

it('leaves the previous UI package alone with --keep-package', function () {
    $root = starterKit();
    $composer = useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true, '--keep-package' => true])->assertSuccessful();

    expect($composer->removed)->toBe([]);
});

it('publishes the agent context and fonts through sigta:install', function () {
    $root = starterKit();
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])
        ->doesntExpectOutputToContain('Next steps')
        ->assertSuccessful();

    expect(file_exists($root.'/sigta-ui.md'))->toBeTrue()
        ->and(file_exists($root.'/AGENTS.md'))->toBeTrue();
});

it('changes nothing when the swap is declined', function () {
    $root = starterKit();
    $before = kitFile($root, 'resources/views/pages/auth/login.blade.php');
    useStarterKit($root);

    $this->artisan('sigta:starter-kit')
        ->expectsConfirmation('Replace these files with Sigta UI versions? Changes you made to them are lost', 'no')
        ->assertSuccessful();

    expect(kitFile($root, 'resources/views/pages/auth/login.blade.php'))->toBe($before);
});

it('refuses editions of the starter kit it has no stubs for', function (Closure $edition, string $message) {
    $root = starterKit();
    $edition($root);
    $before = kitFile($root, 'resources/views/pages/auth/login.blade.php');
    useStarterKit($root);

    $this->artisan('sigta:starter-kit', ['--force' => true])
        ->expectsOutputToContain($message)
        ->assertFailed();

    expect(kitFile($root, 'resources/views/pages/auth/login.blade.php'))->toBe($before);
})->with([
    'teams' => [fn (string $root) => mkdir($root.'/resources/views/pages/teams'), 'teams edition'],
    'class components' => [fn (string $root) => mkdir($root.'/app/Livewire/Settings', 0755, true), 'class-based Livewire components'],
    'WorkOS' => [fn (string $root) => file_put_contents($root.'/composer.json', json_encode(['require' => ['laravel/workos' => '^1.0']])), 'WorkOS edition'],
    'not a starter kit' => [fn (string $root) => unlink($root.'/resources/views/layouts/app/sidebar.blade.php'), 'No Laravel Livewire starter kit found'],
]);

it('ships stubs built only from components that exist', function () {
    $components = collect(Finder::create()->files()->in(dirname(__DIR__).'/resources/views/components')->name('*.blade.php'))
        ->map(fn ($file) => str_replace(['/index.blade.php', '.blade.php', '/'], ['', '', '.'], $file->getRelativePathname()))
        ->all();

    foreach (Finder::create()->files()->in(dirname(__DIR__).'/stubs/starter-kit')->name('*.blade.php') as $stub) {
        preg_match_all('/<sigta:([a-z0-9.\-]+)/', $stub->getContents(), $tags);

        foreach (array_unique($tags[1]) as $tag) {
            expect(in_array($tag, $components, true))->toBeTrue("{$stub->getRelativePathname()} uses <sigta:{$tag}>, which does not exist");
        }
    }
});

it('ships stubs whose feature sections all close and that compile', function () {
    $views = dirname(__DIR__).'/stubs/starter-kit/resources/views';

    // The app's own components and layouts resolve as they would in the app:
    // from the stubs, plus the kit files the stubs leave as they are.
    $kept = starterKit([]).'/kept';
    (new Filesystem)->ensureDirectoryExists($kept.'/layouts');
    file_put_contents($kept.'/app-logo-icon.blade.php', '<svg></svg>');
    file_put_contents($kept.'/placeholder-pattern.blade.php', '<svg></svg>');
    file_put_contents($kept.'/layouts/auth.blade.php', '{{ $slot }}');

    Blade::anonymousComponentPath($views.'/components');
    Blade::anonymousComponentPath($kept);
    Blade::anonymousComponentPath($views.'/layouts', 'layouts');
    Blade::anonymousComponentPath($kept.'/layouts', 'layouts');
    Blade::anonymousComponentPath($views.'/pages', 'pages');

    foreach (Finder::create()->files()->in(dirname(__DIR__).'/stubs/starter-kit')->name('*.blade.php') as $stub) {
        $content = $stub->getContents();

        preg_match_all('/\{\{-- @feature:([a-z0-9-]+) --\}\}/', $content, $opens);
        preg_match_all('/\{\{-- @endfeature:([a-z0-9-]+) --\}\}/', $content, $closes);

        expect($opens[1])->toBe($closes[1], "{$stub->getRelativePathname()} has unbalanced feature sections");

        Blade::compileString($content);
    }
});
