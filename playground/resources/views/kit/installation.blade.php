{{-- Sigta UI — production installation guide.

     A kit page (so it sits in the kit sidebar under the "Docs" tier) covering
     the path that `composer require sigta/ui` alone cannot: this package is
     proprietary and not on Packagist, so you must wire a private VCS
     repository, authenticate Composer, publish fonts, and build.

     Code snippets live in a PHP heredoc block (no component tags inside → the
     sigta: prefix precompiler leaves them alone, no « sentinel needed). Mirrors
     the install-snippet pattern in docs.blade.php. --}}
@php
    // ── Code snippets ────────────────────────────────────────────────────────
    $codeRequirements = <<<'TEXT'
    PHP ^8.2
    Laravel ^12.0 | ^13.0
    Livewire ^4.0
    Tailwind CSS v4  (your app's build, via @tailwindcss/vite)
    TEXT;

    $codeRepoSsh = <<<'JSON'
    {
        "repositories": [{
            "type": "vcs",
            "url": "git@github.com:sigta-hq/ui.git"
        }]
    }
    JSON;

    $codeRepoHttps = <<<'JSON'
    {
        "repositories": [{
            "type": "vcs",
            "url": "https://github.com/sigta-hq/ui.git"
        }]
    }
    JSON;

    // The repository block goes inside your composer.json, alongside
    // "require". Shown in context so the reader sees where it sits.
    $codeRepoInContext = <<<'JSON'
    {
        "require": {
            "sigta/ui": "dev-main"
        },
        "repositories": [{
            "type": "vcs",
            "url": "git@github.com:sigta-hq/ui.git"
        }]
    }
    JSON;

    $codeAuthCli = <<<'SH'
    # HTTPS + token (CI-friendly, no SSH key needed):
    composer config --global github-oauth.github.com ghp_xxxxxxxxxxxxxxxxxxxx

    # …or, per-project (writes auth.json next to composer.json — keep it out of git):
    composer config github-oauth.github.com ghp_xxxxxxxxxxxxxxxxxxxx
    SH;

    $codeAuthJson = <<<'JSON'
    {
        "github-oauth": {
            "github.com": "ghp_xxxxxxxxxxxxxxxxxxxx"
        }
    }
    JSON;

    $codeCiEnv = <<<'SH'
    # CI / deploy runner — provide the same auth without committing auth.json.
    # Composer reads this JSON at install time. URL-encode the JSON value.
    export COMPOSER_AUTH='{"github-oauth":{"github.com":"ghp_xxx"}}'
    SH;

    $codeRequire = <<<'SH'
    composer require sigta/ui:dev-main
    SH;

    $codeStability = <<<'SH'
    # Only if your app's minimum-stability is not already "dev" or lower.
    composer config minimum-stability dev
    composer config prefer-stable true
    SH;

    $codeAppCss = <<<'CSS'
    /* resources/css/app.css */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + Sigta brand */
    CSS;

    $codeAppCssOwnBrand = <<<'CSS'
    /* Re-brand: import core.css (structure) + your own theme values. */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/core.css";
    @import "./brand.css";        /* your :root{--sigta-*} + @font-face */
    CSS;

    $codeLayout = <<<'BLADE'
    {{-- resources/views/components/layouts/app.blade.php --}}
    <head>
        …
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @sigtaScripts   {{-- + @sigtaEditorScripts if you use <sigta:editor> --}}
    </head>
    <body>
        …
        @livewireScripts
    </body>
    BLADE;

    $codeFonts = <<<'SH'
    php artisan sigta:install --only=fonts
    SH;

    $codeBuild = <<<'SH'
    npm run build
    SH;

    $codeDeploy = <<<'SH'
    composer install --no-dev --prefer-dist --optimize-autoloader
    php artisan sigta:install
    php artisan route:cache
    php artisan view:cache
    npm ci && npm run build
    SH;

    // ── sigta:install + LLM agent context ────────────────────────────────────
    $codeInstall = <<<'SH'
    php artisan sigta:install
    SH;

    $codeComposerHook = <<<'JSON'
    {
        "scripts": {
            "post-autoload-dump": [
                "@php artisan package:discover --ansi",
                "@php artisan sigta:install --ansi"
            ]
        }
    }
    JSON;

    $preClasses = 'm-0 overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.8rem] leading-relaxed text-inverse-fg';
    $stepLabel  = 'font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3';
@endphp

<x-kit-shell title="Installation">
    <x-sigta::text variant="lede" class="max-w-[58ch] -mt-4">
        How to install Sigta UI in a <b class="text-ink">production</b> Laravel app. The package
        is proprietary and not published to Packagist, so <code class="font-mono text-ink">composer require sigta/ui</code>
        alone will fail — you must point Composer at a private VCS repository first. This page covers that path end to end.
        For the local-dev quickstart, see <a href="/docs#install" class="text-ink underline underline-offset-[3px]">/docs</a>.
        Building with Svelte 5 instead? The same components, looks and behavior ship as the npm package
        <code class="font-mono text-ink">@sigta-hq/svelte</code>, with no PHP needed; its installation lives in the docs site's Svelte edition.
    </x-sigta::text>

    {{-- ===================== REQUIREMENTS ===================== --}}
    <section>
        <p class="{{ $stepLabel }}">Requirements</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeRequirements }}</code></pre>
    </section>

    <x-sigta::rule variant="1" />

    {{-- ===================== 1 · VCS REPOSITORY ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">01 · Private VCS repository</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Add a <code class="font-mono text-ink">repositories</code> entry pointing at the private Git remote.
            Without this, Composer has no way to find the package and <code class="font-mono text-ink">composer require</code>
            exits with “could not be found in any version”.
        </x-sigta::text>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div>
                <p class="{{ $stepLabel }}">SSH (recommended for dev machines)</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeRepoSsh }}</code></pre>
            </div>
            <div>
                <p class="{{ $stepLabel }}">HTTPS (CI / deploy runners)</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeRepoHttps }}</code></pre>
            </div>
        </div>

        <p class="{{ $stepLabel }} mt-6">In context — sits alongside <code class="font-mono text-ink">require</code></p>
        <pre class="{{ $preClasses }}"><code>{{ $codeRepoInContext }}</code></pre>
    </section>

    {{-- ===================== 2 · AUTH ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">02 · Composer authentication</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Composer must authenticate to read the private repository. Use a GitHub personal access token
            (with <code class="font-mono text-ink">repo</code> scope) for both HTTPS and CI; SSH keys work on
            machines that already have them.
        </x-sigta::text>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div>
                <p class="{{ $stepLabel }}">Via the CLI</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeAuthCli }}</code></pre>
            </div>
            <div>
                <p class="{{ $stepLabel }}">Or commit-free auth.json (gitignored)</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeAuthJson }}</code></pre>
            </div>
        </div>

        <div class="mt-5">
            <x-sigta::callout variant="info" heading="CI &amp; deploy runners.">
                Never commit the token. Inject it via the <code class="font-mono">COMPOSER_AUTH</code> environment
                variable — Composer reads it at install time.
            </x-sigta::callout>
            <pre class="{{ $preClasses }} mt-3"><code>{{ $codeCiEnv }}</code></pre>
        </div>
    </section>

    {{-- ===================== 3 · REQUIRE ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">03 · Require &amp; install</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Pin to <code class="font-mono text-ink">dev-main</code> — the repository has no tagged releases yet, so a
            version constraint like <code class="font-mono text-ink">^1.0</code> resolves to nothing. Once the package
            is tagged, switch to a stable constraint.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Install</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeRequire }}</code></pre>

        <div class="mt-5">
            <x-sigta::callout heading="Only if minimum-stability blocks dev-main.">
                <code class="font-mono">dev-main</code> requires a permissive stability setting. Run this only if the
                install above errors on stability.
            </x-sigta::callout>
            <pre class="{{ $preClasses }} mt-3"><code>{{ $codeStability }}</code></pre>
        </div>

        <x-sigta::text variant="muted" class="mt-5 text-[0.9rem] max-w-[60ch]">
            The service provider (<code class="font-mono text-ink">SigtaServiceProvider</code>) is auto-discovered via
            <code class="font-mono text-ink">extra.laravel.providers</code> — no manual registration in
            <code class="font-mono text-ink">bootstrap/providers.php</code>.
        </x-sigta::text>
    </section>

    <x-sigta::rule variant="2" />

    {{-- ===================== 4 · CSS ENTRY ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">04 · CSS entry</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Import the Sigta stylesheet into your app's CSS entry. The component styles ship prebuilt, so
            Tailwind needs no <code class="font-mono text-ink">@source</code> for the package's views (an existing one is harmless).
        </x-sigta::text>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div>
                <p class="{{ $stepLabel }}">Drop-in Sigta brand</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeAppCss }}</code></pre>
            </div>
            <div>
                <p class="{{ $stepLabel }}">Or: core + your own theme</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeAppCssOwnBrand }}</code></pre>
            </div>
        </div>

        <x-sigta::text variant="muted" class="mt-4 text-[0.9rem] max-w-[60ch]">
            Re-branding guidance and the full token contract live at
            <a href="/docs#theming" class="text-ink underline underline-offset-[3px]">/docs#theming</a> and the
            <a href="/kit/theming" class="text-ink underline underline-offset-[3px]">theme-switch kit page</a>.
        </x-sigta::text>
    </section>

    {{-- ===================== 5 · LAYOUT WIRING ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">05 · Layout wiring</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Add <code class="font-mono text-ink">@sigtaScripts</code> in <code class="font-mono text-ink">&lt;head&gt;</code>
            (after Livewire styles). It emits a <code class="font-mono text-ink">&lt;script&gt;</code> tag pointing at the
            route-served bundle — no publish step. Add <code class="font-mono text-ink">@sigtaEditorScripts</code> too,
            only if you use <code class="font-mono text-ink">&lt;sigta:editor&gt;</code>.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Your app layout</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeLayout }}</code></pre>

        <x-sigta::text variant="muted" class="mt-4 text-[0.9rem] max-w-[60ch]">
            Components are then available as <code class="font-mono text-ink">&lt;sigta:button&gt;</code> or
            <code class="font-mono text-ink">&lt;x-sigta::button&gt;</code> — both compile identically.
        </x-sigta::text>
    </section>

    {{-- ===================== 6 · FONTS ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">06 · Publish fonts</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            <code class="font-mono text-ink">@font-face</code> URLs must resolve under <code class="font-mono text-ink">public/</code>,
            so fonts are published rather than route-served. Run this once per deploy.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Publish</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeFonts }}</code></pre>
    </section>

    {{-- ===================== 7 · BUILD ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">07 · Build assets</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Vite compiles your app's CSS (the Sigta component styles arrive prebuilt
            inside it). The package's own JavaScript ships pre-built in
            <code class="font-mono text-ink">dist/</code> and is served by route — it needs <b class="text-ink">no build step</b>
            on your side.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Build</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeBuild }}</code></pre>

        <div class="mt-5">
            <x-sigta::callout variant="success" heading="No JS publish step.">
                <code class="font-mono">dist/sigta.js</code> and <code class="font-mono">dist/sigta-editor.js</code> are
                committed to the repository, so the route-served bundle works out of the box. Make sure
                <code class="font-mono">dist/</code> is present after <code class="font-mono">composer install</code> — it
                is, because the files ship in the package.
            </x-sigta::callout>
        </div>
    </section>

    <x-sigta::rule variant="3" />

    {{-- ===================== 8 · DEPLOY ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">08 · Production deploy sequence</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            A typical release runbook. The private VCS repo + auth must be reachable from wherever this runs.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Release commands</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeDeploy }}</code></pre>

        <div class="mt-5 flex flex-col gap-3">
            <x-sigta::callout variant="success" heading="Cache the routes.">
                <code class="font-mono">@sigtaScripts</code> calls <code class="font-mono">route()</code> to emit the
                asset URL. <code class="font-mono">php artisan route:cache</code> is recommended in production — and the
                Sigta asset routes are registered by the service provider, so they're included.
            </x-sigta::callout>
            <x-sigta::callout variant="danger" heading="Don't forget auth on the server.">
                <code class="font-mono">composer install</code> on the deploy runner must reach the private VCS
                repository. If <code class="font-mono">COMPOSER_AUTH</code> isn't set, the install fails at the
                <code class="font-mono">sigta/ui</code> requirement.
            </x-sigta::callout>
        </div>
    </section>

    <x-sigta::text variant="muted" class="text-[0.9rem] max-w-[60ch]">
        For the API surface, every component variant, and the re-branding workflow, continue to
        <a href="/docs" class="text-ink underline underline-offset-[3px]">/docs</a> or browse the
        <a href="/kit" class="text-ink underline underline-offset-[3px]">component kit</a>.
    </x-sigta::text>

    {{-- ===================== 9 · LLM AGENT CONTEXT ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">09 · LLM agent context</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            If this app will be built or edited by an LLM coding agent (ZCode, Claude Code, Cursor, etc.),
            publish the Sigta context files so the agent reads the rules instead of guessing. The same
            command handles fonts too — run it once and re-run it to update.
        </x-sigta::text>

        <div class="mt-5">
            <x-sigta::callout variant="info" heading="One command, fully idempotent.">
                <code class="font-mono">sigta:install</code> mirrors fonts, writes
                <code class="font-mono">sigta-ui.md</code> + <code class="font-mono">sigta-components.md</code> to the
                project root, and adds a reference line to <code class="font-mono">AGENTS.md</code> (creating it if absent,
                appending if present). Re-running is always safe — it overwrites the package-owned files and detects the
                existing <code class="font-mono">AGENTS.md</code> reference without duplicating it.
            </x-sigta::callout>
        </div>

        <p class="{{ $stepLabel }} mt-5">Publish &amp; sync</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeInstall }}</code></pre>

        <x-sigta::text variant="muted" class="mt-4 text-[0.9rem] max-w-[60ch]">
            The agent then reads <code class="font-mono text-ink">AGENTS.md</code> → follows the pointer to
            <code class="font-mono text-ink">sigta-ui.md</code> (the rules) → scans
            <code class="font-mono text-ink">sigta-components.md</code> (the full API) → writes
            <code class="font-mono text-ink">&lt;sigta:button&gt;</code> instead of
            <code class="font-mono text-ink">&lt;button class="bg-blue-600"&gt;</code>.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-6">Auto-sync on every composer update</p>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Because <code class="font-mono text-ink">sigta:install</code> is idempotent, you can wire it into your
            <code class="font-mono text-ink">composer.json</code> so every <code class="font-mono text-ink">composer install</code> /
            <code class="font-mono text-ink">update</code> re-syncs the context. Then no one has to remember to run it by hand.
        </x-sigta::text>
        <pre class="{{ $preClasses }} mt-3"><code>{{ $codeComposerHook }}</code></pre>
    </section>
</x-kit-shell>
