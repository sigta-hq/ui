{{-- Sigta UI — production installation guide.

     A kit page (so it sits in the kit sidebar under the "Docs" tier) covering
     the full production path: require from Packagist, wire CSS and layout,
     publish fonts, build, and deploy.

     Code snippets live in a PHP heredoc block (no component tags inside → the
     sigta: prefix precompiler leaves them alone, no « sentinel needed). Mirrors
     the install-snippet pattern in docs.blade.php. --}}
@php
    // ── Code snippets ────────────────────────────────────────────────────────
    $codeRequirements = <<<'TEXT'
    PHP ^8.4
    Laravel ^12.0 | ^13.0
    Livewire ^4.0
    Tailwind CSS v4  (your app's build, via @tailwindcss/vite)
    TEXT;

    $codeRequire = <<<'SH'
    composer require sigta/ui
    SH;

    // Apps that installed sigta/ui from the private GitHub repository before
    // it went public: drop the repository entry and token, then re-require.
    $codeMigrate = <<<'SH'
    # 1. Delete the sigta/ui "vcs" entry from "repositories" in composer.json
    # 2. Drop the GitHub token (auth.json / COMPOSER_AUTH) if nothing else needs it
    composer require sigta/ui   # replaces dev-main with a tagged constraint
    SH;

    $codeAppCss = <<<'CSS'
    /* resources/css/app.css */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + Sigta brand */
    @source "../../vendor/sigta/ui/resources/views";
    CSS;

    $codeAppCssOwnBrand = <<<'CSS'
    /* Re-brand: import core.css (structure) + your own theme values. */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/core.css";
    @import "./brand.css";        /* your :root{--sigta-*} + @font-face */
    @source "../../vendor/sigta/ui/resources/views";
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
        How to install Sigta UI in a <b class="text-ink">production</b> Laravel app. The package is published on
        Packagist, so <code class="font-mono text-ink">composer require sigta/ui</code> is all Composer needs. This page
        covers the rest of the path end to end.
        For the local-dev quickstart, see <a href="/docs#install" class="text-ink underline underline-offset-[3px]">/docs</a>.
    </x-sigta::text>

    {{-- ===================== REQUIREMENTS ===================== --}}
    <section>
        <p class="{{ $stepLabel }}">Requirements</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeRequirements }}</code></pre>
    </section>

    <x-sigta::rule variant="1" class="text-ink/60" />

    {{-- ===================== 1 · REQUIRE ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">01 · Require &amp; install</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Composer resolves the latest tagged release from Packagist and writes a caret constraint
            to your <code class="font-mono text-ink">composer.json</code>. No repository entry or token needed.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Install</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeRequire }}</code></pre>

        <x-sigta::text variant="muted" class="mt-5 text-[0.9rem] max-w-[60ch]">
            The service provider (<code class="font-mono text-ink">SigtaServiceProvider</code>) is auto-discovered via
            <code class="font-mono text-ink">extra.laravel.providers</code> — no manual registration in
            <code class="font-mono text-ink">bootstrap/providers.php</code>.
        </x-sigta::text>

        <div class="mt-5">
            <x-sigta::callout variant="info" heading="Installed from the private repository before?">
                Apps that pulled <code class="font-mono">sigta/ui</code> from the GitHub repository on
                <code class="font-mono">dev-main</code> can drop the <code class="font-mono">vcs</code> repository entry and the
                GitHub token, then re-require to move onto tagged releases.
            </x-sigta::callout>
            <pre class="{{ $preClasses }} mt-3"><code>{{ $codeMigrate }}</code></pre>
        </div>
    </section>

    <x-sigta::rule variant="2" class="text-ink/60" />

    {{-- ===================== 2 · CSS ENTRY ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">02 · CSS entry</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Import the Sigta stylesheet into your app's CSS entry, and add <code class="font-mono text-ink">@source</code>
            so Tailwind v4 scans the package's views for class names to keep.
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

    {{-- ===================== 3 · LAYOUT WIRING ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">03 · Layout wiring</x-sigta::eyebrow>
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

    {{-- ===================== 4 · FONTS ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">04 · Publish fonts</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            <code class="font-mono text-ink">@font-face</code> URLs must resolve under <code class="font-mono text-ink">public/</code>,
            so fonts are published rather than route-served. Run this once per deploy.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Publish</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeFonts }}</code></pre>
    </section>

    {{-- ===================== 5 · BUILD ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">05 · Build assets</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            Vite compiles your app's CSS (Tailwind scans the package views via
            <code class="font-mono text-ink">@source</code>). The package's own JavaScript ships pre-built in
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

    <x-sigta::rule variant="3" class="text-ink/60" />

    {{-- ===================== 6 · DEPLOY ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">06 · Production deploy sequence</x-sigta::eyebrow>
        <x-sigta::text variant="muted" class="mt-2 max-w-[60ch]">
            A typical release runbook.
        </x-sigta::text>

        <p class="{{ $stepLabel }} mt-5">Release commands</p>
        <pre class="{{ $preClasses }}"><code>{{ $codeDeploy }}</code></pre>

        <div class="mt-5 flex flex-col gap-3">
            <x-sigta::callout variant="success" heading="Cache the routes.">
                <code class="font-mono">@sigtaScripts</code> calls <code class="font-mono">route()</code> to emit the
                asset URL. <code class="font-mono">php artisan route:cache</code> is recommended in production — and the
                Sigta asset routes are registered by the service provider, so they're included.
            </x-sigta::callout>
        </div>
    </section>

    <x-sigta::text variant="muted" class="text-[0.9rem] max-w-[60ch]">
        For the API surface, every component variant, and the re-branding workflow, continue to
        <a href="/docs" class="text-ink underline underline-offset-[3px]">/docs</a> or browse the
        <a href="/kit" class="text-ink underline underline-offset-[3px]">component kit</a>.
    </x-sigta::text>

    {{-- ===================== 7 · LLM AGENT CONTEXT ===================== --}}
    <section>
        <x-sigta::eyebrow class="mb-3">07 · LLM agent context</x-sigta::eyebrow>
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
