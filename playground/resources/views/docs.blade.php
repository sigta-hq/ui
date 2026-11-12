{{-- Sigta UI — internal usage & theming docs. Standalone hostable page (own
     <!doctype>, no kit-shell). Teaches how to USE the framework; the /kit/*
     gallery shows every component variant.

     ── Escaping note ─────────────────────────────────────────────────────────
     Blade's <sigta:…> prefix precompiler rewrites any literal "<sigta:" in this
     file — even inside PHP strings — to <x-sigta::…>. To display component tags
     as *source text* we author them with « standing in for "<" and unwrap it at
     render time via $src(). Blade @directives inside a nowdoc are emitted as
     plain text, so those need no sentinel. --}}
@php
    use Illuminate\Support\Facades\File;

    $src = fn (string $s) => str_replace('«', '<', $s);

    // Components index — mirrors kit-shell's directory scan, minus non-component pages.
    $components = collect(File::files(resource_path('views/kit')))
        ->map(fn ($f) => str($f->getFilename())->before('.blade.php')->toString())
        ->reject(fn ($p) => in_array($p, ['index', 'docs', 'landing', 'dashboard'], true))
        ->sort()
        ->values();

    // ── Install snippets (no component tags → no sentinel needed) ──────────────
    $codeComposer = 'composer require sigta/ui';

    $codeAppCss = <<<'CSS'
    /* resources/css/app.css */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/sigta.css";   /* core + Sigta brand */
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

    // ── Usage snippets (« = "<", unwrapped by $src) ───────────────────────────
    $codeButton = <<<'HTML'
    «sigta:button variant="cta">Book a project «sigta:icon name="arrow-right" size="sm" />«/sigta:button>
    «sigta:button>Preview«/sigta:button>
    «sigta:button variant="ghost">Cancel«/sigta:button>
    HTML;

    $codeInput = <<<'HTML'
    «sigta:input
        label="Email"
        type="email"
        icon="mail"
        placeholder="you@studio.co"
        description="We only use this for the invoice." />
    HTML;

    $codeCallout = <<<'HTML'
    «sigta:callout variant="success" heading="Deploy complete.">
        Plumb 2.4 is live for all workspaces — no migration needed.
    «/sigta:callout>
    HTML;

    $codeBadge = <<<'HTML'
    «sigta:badge>Draft«/sigta:badge>
    «sigta:badge variant="solid">Live«/sigta:badge>
    «sigta:badge variant="accent">New«/sigta:badge>
    HTML;

    // ── Theming snippets ──────────────────────────────────────────────────────
    $codeTokens = <<<'CSS'
    :root {
      /* surfaces & text */
      --sigta-surface: …;         /* page background */
      --sigta-surface-raised: …;  /* cards, inputs, panels */
      --sigta-fg: …;              /* primary text, borders, solid fills */
      --sigta-fg-muted: …;        /* secondary text */
      --sigta-line: …;            /* hairlines, dividers, input borders */

      /* brand & status */
      --sigta-accent: …;          /* the one emphasis colour (used sparingly) */
      --sigta-accent-line: …;     /* border for accent-filled elements */
      --sigta-success: …;
      --sigta-danger: …;

      /* inverse (dark bands: CTA sections, spotlights) */
      --sigta-inverse: …;
      --sigta-inverse-fg: …;
      --sigta-inverse-fg-muted: …;
      --sigta-inverse-line: …;

      /* type */
      --sigta-font-head: …;       /* headings */
      --sigta-font-body: …;       /* prose */
      --sigta-font-mono: …;       /* UI chrome, labels, data */
      --sigta-font-hand: …;       /* decorative handwriting */

      /* shape, depth & layout */
      --sigta-radius: …;  --sigta-radius-lg: …;  --sigta-radius-xl: …;
      --sigta-shadow: …;
      --sigta-maxw: …;    --sigta-gutter: …;

      /* focus ring & motion */
      --sigta-focus-width: …;  --sigta-focus-offset: …;
      --sigta-ease-fast: …;  --sigta-ease: …;  --sigta-ease-slow: …;
    }
    CSS;

    $codeDropIn = <<<'CSS'
    /* Mode A — drop-in Sigta brand */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/sigta.css";
    @source "../../vendor/sigta/ui/resources/views";
    CSS;

    $codeOwnTheme = <<<'CSS'
    /* Mode B — core structure + your own brand */
    @import "tailwindcss";
    @import "../../vendor/sigta/ui/resources/css/core.css";
    @import "./brand.css";        /* your :root{--sigta-*} + @font-face */
    @source "../../vendor/sigta/ui/resources/views";
    CSS;

    $codeScope = <<<'CSS'
    /* Multi-brand: scope a theme, flip it on any subtree */
    [data-sigta-theme="studio"] { --sigta-accent: …; --sigta-radius: …; … }
    CSS;

    $preClasses = 'm-0 overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.8rem] leading-relaxed text-inverse-fg';
@endphp
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sigta UI — Usage & Theming</title>
    <meta name="description" content="How to install, use, and re-brand Sigta UI — the Sigta component system. One brand, every surface.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @sigtaScripts
</head>
<body>
<x-sigta::paper />

<a href="#install" class="absolute top-0 -left-[9999px] focus:left-0 z-[100] bg-ink text-surface font-mono text-[0.8rem] no-underline px-[1em] py-[0.6em] rounded-br-base">Skip to content</a>

<div class="relative z-10 mx-auto max-w-[1080px] px-[clamp(20px,5vw,56px)]">

    {{-- ===================== HEADER / NAV ===================== --}}
    <header class="flex flex-wrap items-center justify-between gap-4 py-6 border-b border-line">
        <a href="/docs" class="no-underline"><x-sigta::brand name="SIGTA"><x-slot:logo><x-logo /></x-slot:logo></x-sigta::brand></a>
        <nav class="flex flex-wrap items-center gap-1 font-mono text-[0.72rem] tracking-[0.04em]" aria-label="Docs sections">
            <a href="#install" class="no-underline px-2 py-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5">Install</a>
            <a href="#usage" class="no-underline px-2 py-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5">Usage</a>
            <a href="#theming" class="no-underline px-2 py-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5">Theming</a>
            <a href="#components" class="no-underline px-2 py-1.5 rounded-base text-graphite hover:text-ink hover:bg-ink/5">Components</a>
            <a href="/kit" class="no-underline px-2 py-1.5 rounded-base text-ink border border-line hover:bg-ink/5 ml-1">Open the kit →</a>
        </nav>
    </header>

    {{-- ===================== HERO ===================== --}}
    <section class="py-[clamp(48px,8vw,96px)]">
        <x-sigta::eyebrow class="mb-6">Sigta UI · Internal docs</x-sigta::eyebrow>
        <x-sigta::brand name="SIGTA" size="lg" class="mb-6"><x-slot:logo><x-logo /></x-slot:logo></x-sigta::brand>
        <x-sigta::heading level="1" size="display" class="max-w-[16ch]">The Sigta component system — one brand, <x-sigta::mark>every surface</x-sigta::mark>.</x-sigta::heading>
        <x-sigta::text variant="lede" class="mt-6 max-w-[52ch]">A Livewire/Blade library that carries the Sigta brand — paper, ink, and hand-drawn accents — as the shared foundation for landing pages and SaaS platforms. Semantic tokens mean the same markup re-skins for any brand.</x-sigta::text>
        <div class="mt-8 flex flex-wrap items-center gap-3">
            <x-sigta::button variant="cta" href="/kit">Browse the component kit <x-sigta::icon name="arrow-right" size="sm" /></x-sigta::button>
            <x-sigta::button href="#theming">Read the theming guide</x-sigta::button>
        </div>
    </section>

    <x-sigta::rule variant="1" class="text-ink/60" />

    {{-- ===================== INSTALL ===================== --}}
    <section id="install" class="scroll-mt-8 py-[clamp(40px,6vw,80px)]">
        <x-sigta::eyebrow class="mb-4">01 · Install</x-sigta::eyebrow>
        <x-sigta::heading level="2" size="xl">Add the package</x-sigta::heading>
        <x-sigta::text variant="muted" class="mt-3 max-w-[56ch]">Requires Tailwind v4. Pull the package, point your CSS entry at the Sigta stylesheet, and wire the runtime into your layout.</x-sigta::text>

        <div class="mt-5">
            <x-sigta::callout variant="info" heading="Installing in production?">
                <code class="font-mono">composer require sigta/ui</code> pulls the package from Packagist. For fonts, the build, and the deploy sequence, see the <a href="/kit/installation" class="text-ink underline underline-offset-[3px]">production installation guide</a>.
            </x-sigta::callout>
        </div>

        <div class="mt-8 flex flex-col gap-8">
            <div>
                <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">1. Require via Composer</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeComposer }}</code></pre>
            </div>

            <div>
                <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">2. Import the stylesheet in your CSS entry</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeAppCss }}</code></pre>
            </div>

            <div>
                <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">3. Wire the runtime into your layout</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeLayout }}</code></pre>
                <x-sigta::text variant="muted" class="mt-3 text-[0.9rem] max-w-[56ch]">Components are then available as <code class="font-mono text-ink">{{ $src('«sigta:button>') }}</code> or the equivalent <code class="font-mono text-ink">&lt;x-sigta::button&gt;</code> — both forms compile identically. This page prefers the terse prefix.</x-sigta::text>
            </div>
        </div>
    </section>

    <x-sigta::rule variant="2" class="text-ink/60" />

    {{-- ===================== USAGE ===================== --}}
    <section id="usage" class="scroll-mt-8 py-[clamp(40px,6vw,80px)]">
        <x-sigta::eyebrow class="mb-4">02 · Usage</x-sigta::eyebrow>
        <x-sigta::heading level="2" size="xl">Write a tag, get a component</x-sigta::heading>
        <x-sigta::text variant="muted" class="mt-3 max-w-[56ch]">Every example below is <b class="text-ink">live on the left</b> and its <b class="text-ink">source on the right</b>. Props follow conventional naming: <code class="font-mono text-ink">variant</code>, <code class="font-mono text-ink">size</code>, <code class="font-mono text-ink">icon</code>, <code class="font-mono text-ink">label</code>, <code class="font-mono text-ink">description</code>, <code class="font-mono text-ink">error</code>.</x-sigta::text>

        <div class="mt-8 flex flex-col gap-6">
            <x-doc-example label="Button — variants, icons, sizes">
                <x-sigta::button variant="cta">Book a project <x-sigta::icon name="arrow-right" size="sm" /></x-sigta::button>
                <x-sigta::button>Preview</x-sigta::button>
                <x-sigta::button variant="ghost">Cancel</x-sigta::button>
                <x-slot:code>{{ $src($codeButton) }}</x-slot:code>
            </x-doc-example>

            <x-doc-example label="Input — label, icon, description (auto-wired field)">
                <div class="w-full max-w-sm">
                    <x-sigta::input label="Email" type="email" icon="mail" placeholder="you@studio.co" description="We only use this for the invoice." />
                </div>
                <x-slot:code>{{ $src($codeInput) }}</x-slot:code>
            </x-doc-example>

            <x-doc-example label="Callout — status messaging">
                <div class="w-full">
                    <x-sigta::callout variant="success" heading="Deploy complete.">Plumb 2.4 is live for all workspaces — no migration needed.</x-sigta::callout>
                </div>
                <x-slot:code>{{ $src($codeCallout) }}</x-slot:code>
            </x-doc-example>

            <x-doc-example label="Badge — outline, solid, accent">
                <x-sigta::badge>Draft</x-sigta::badge>
                <x-sigta::badge variant="solid">Live</x-sigta::badge>
                <x-sigta::badge variant="accent">New</x-sigta::badge>
                <x-slot:code>{{ $src($codeBadge) }}</x-slot:code>
            </x-doc-example>
        </div>
    </section>

    <x-sigta::rule variant="3" class="text-ink/60" />

    {{-- ===================== THEMING ===================== --}}
    <section id="theming" class="scroll-mt-8 py-[clamp(40px,6vw,80px)]">
        <x-sigta::eyebrow class="mb-4">03 · Theming</x-sigta::eyebrow>
        <x-sigta::heading level="2" size="xl">One markup, any brand</x-sigta::heading>
        <x-sigta::text variant="muted" class="mt-3 max-w-[60ch]">Every component reads semantic <code class="font-mono text-ink">var(--sigta-*)</code> tokens — never a raw colour. A theme is just a values file. Names are roles (<code class="font-mono text-ink">--sigta-surface</code>, <code class="font-mono text-ink">--sigta-fg</code>), so you pick values that fit your brand and the component markup never changes.</x-sigta::text>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div>
                <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">Mode A — drop in the Sigta brand</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeDropIn }}</code></pre>
            </div>
            <div>
                <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">Mode B — core + your own theme</p>
                <pre class="{{ $preClasses }}"><code>{{ $codeOwnTheme }}</code></pre>
            </div>
        </div>

        <div class="mt-6">
            <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-3">The token contract (from <code class="font-mono text-ink">theme.example.css</code> — every one is required)</p>
            <pre class="{{ $preClasses }}"><code>{{ $codeTokens }}</code></pre>
            <div class="mt-3 flex items-start gap-3">
                <div class="flex-1"><x-sigta::callout heading="Scope for multi-brand.">Wrap a theme under <code class="font-mono">[data-sigta-theme="x"]</code> and set the attribute on any subtree to run several brands in one build.</x-sigta::callout></div>
            </div>
            <pre class="{{ $preClasses }} mt-4"><code>{{ $codeScope }}</code></pre>
        </div>

        {{-- LIVE proof: identical markup, two brands, side by side --}}
        <div class="mt-10">
            <p class="font-mono text-[0.68rem] tracking-[0.12em] uppercase text-graphite mb-4">Live: the same cluster, two brands</p>
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-panel border border-line bg-surface-raised p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="font-mono text-[0.7rem] tracking-[0.1em] uppercase text-graphite">Sigta</span>
                        <x-sigta::badge>default</x-sigta::badge>
                    </div>
                    @include('partials.sigta-cluster')
                </div>
                <div data-sigta-theme="studio" class="rounded-panel border border-line bg-surface-raised p-5">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="font-mono text-[0.7rem] tracking-[0.1em] uppercase text-graphite">Studio (example rebrand)</span>
                        <x-sigta::badge>data-sigta-theme="studio"</x-sigta::badge>
                    </div>
                    @include('partials.sigta-cluster')
                </div>
            </div>
            <x-sigta::text variant="muted" class="mt-4 text-[0.9rem] max-w-[60ch]">Byte-for-byte the same component markup on both sides — only the surrounding token values differ. See it toggle at runtime on the <a href="/kit/theming" class="text-ink underline underline-offset-[3px]">theme-switch page</a>.</x-sigta::text>
        </div>
    </section>

    <x-sigta::rule variant="1" class="text-ink/60" />

    {{-- ===================== COMPONENTS INDEX ===================== --}}
    <section id="components" class="scroll-mt-8 py-[clamp(40px,6vw,80px)]">
        <x-sigta::eyebrow class="mb-4">04 · Components</x-sigta::eyebrow>
        <x-sigta::heading level="2" size="xl">Every component in the kit</x-sigta::heading>
        <x-sigta::text variant="muted" class="mt-3 max-w-[56ch]">{{ $components->count() }} pages, each covering every variant and state. Tap a tile for the full gallery.</x-sigta::text>

        <div class="mt-8 grid gap-3 grid-cols-2 sm:grid-cols-3 md:grid-cols-4">
            @foreach ($components as $component)
                <a href="/kit/{{ $component }}"
                   class="group flex items-center justify-between gap-2 rounded-panel border border-line bg-surface px-4 py-3 no-underline transition duration-150 ease-out hover:-translate-y-[2px] hover:shadow-paper">
                    <span class="font-mono text-[0.78rem] text-ink">{{ str($component)->headline() }}</span>
                    <span class="font-mono text-graphite transition-transform duration-150 group-hover:translate-x-[2px]" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="border-t border-line py-8 mt-6 flex flex-wrap items-center justify-between gap-4 font-mono text-[0.72rem] text-graphite">
        <span class="inline-flex items-center gap-2"><x-sigta::badge variant="solid">internal</x-sigta::badge> Sigta UI docs</span>
        <span class="flex flex-wrap items-center gap-4">
            <a href="https://github.com/sigta-hq/ui#readme" class="text-ink no-underline hover:underline hover:underline-offset-[3px]">README</a>
            <a href="/kit" class="text-ink no-underline hover:underline hover:underline-offset-[3px]">Component kit</a>
            <a href="/kit/theming" class="text-ink no-underline hover:underline hover:underline-offset-[3px]">Theme switch</a>
        </span>
    </footer>

</div>

@livewireScripts
</body>
</html>
