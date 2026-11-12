@props(['title' => 'Kit'])

@php
// Existing kit pages (the navigable units).
$existing = collect(\Illuminate\Support\Facades\File::files(resource_path('views/kit')))
    ->map(fn ($f) => str($f->getFilename())->before('.blade.php')->toString())
    ->reject(fn ($p) => in_array($p, ['index', 'theming']))
    ->all();

// Tier catalog ships with the package (resources/catalog.php via the vendor symlink).
$catalog = require base_path('vendor/sigta/ui/resources/catalog.php');

// Build grouped nav: only link items that actually have a kit page; keep the
// catalog's order. Track which pages the catalog covers so none go missing.
$grouped = collect($catalog)->map(function ($tier) use ($existing) {
    $tier['links'] = array_values(array_intersect($tier['items'], $existing));
    return $tier;
})->filter(fn ($tier) => count($tier['links']) > 0);

$claimed = $grouped->flatMap(fn ($t) => $t['links'])->all();
$uncategorised = array_values(array_diff($existing, $claimed));

// Sections (and pages) are full-width regions — give them a wide canvas so
// they aren't smushed into the reading column that suits elements/components.
$slug = request()->segment(2);
$wide = in_array($slug, array_merge($catalog['sections']['items'] ?? [], $catalog['pages']['items'] ?? []), true);
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Sigta UI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @sigtaScripts
    {{ $head ?? '' }}
</head>
<body>
    <x-sigta::paper />

    <div class="relative z-10 flex min-h-screen">
        <aside class="w-60 shrink-0 border-r border-line px-5 py-8 hidden md:block overflow-y-auto max-h-screen sticky top-0">
            <a href="/kit" class="no-underline"><x-sigta::brand name="SIGTA" size="sm"><x-slot:logo><x-logo /></x-slot:logo></x-sigta::brand></a>

            <nav class="mt-6 flex flex-col gap-6" aria-label="Kit pages">
                @foreach ($grouped as $tier)
                    <div>
                        <p class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite mb-2">{{ $tier['label'] }}</p>
                        <div class="flex flex-col gap-0.5">
                            @foreach ($tier['links'] as $page)
                                <a href="/kit/{{ $page }}"
                                   class="font-mono text-[0.76rem] no-underline px-2 py-1.5 rounded-base {{ request()->is('kit/'.$page) ? 'bg-ink text-surface' : 'text-graphite hover:text-ink hover:bg-ink/5' }}">
                                    {{ str($page)->headline() }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if (count($uncategorised))
                    <div>
                        <p class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite mb-2">Other</p>
                        <div class="flex flex-col gap-0.5">
                            @foreach ($uncategorised as $page)
                                <a href="/kit/{{ $page }}"
                                   class="font-mono text-[0.76rem] no-underline px-2 py-1.5 rounded-base {{ request()->is('kit/'.$page) ? 'bg-ink text-surface' : 'text-graphite hover:text-ink hover:bg-ink/5' }}">
                                    {{ str($page)->headline() }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>
        </aside>

        <main class="flex-1 py-10 {{ $wide ? 'px-6 md:px-8 max-w-[1400px]' : 'px-6 md:px-12 max-w-[980px]' }}">
            <x-sigta::eyebrow>Sigta UI · Kit</x-sigta::eyebrow>
            <x-sigta::heading level="1" size="xl">{{ $title }}</x-sigta::heading>
            <div class="mt-8 flex flex-col gap-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
