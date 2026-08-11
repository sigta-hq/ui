<x-kit-shell title="Tile grid">
    <x-kit-example label="Two columns — recognition cards">
    «sigta:tile-grid cols="2">
        «sigta:tile-grid.tile num="01 · Tilbuddet i indbakken" class="gap-[0.9rem]">
            «p class="font-head italic font-normal text-[clamp(1.15rem,1.7vw,1.4rem)] leading-[1.3]">"Vi har fået et tilbud på 750.000 kr. Er det rimeligt?"«/p>
            «p class="flex items-baseline gap-[0.55em] text-[0.95rem] text-graphite before:content-['→'] before:shrink-0 before:font-mono before:text-ink">Vi læser det med dig, linje for linje.«/p>
        «/sigta:tile-grid.tile>
        «sigta:tile-grid.tile num="02 · Projektet skrider" class="gap-[0.9rem]">
            «p class="font-head italic font-normal text-[clamp(1.15rem,1.7vw,1.4rem)] leading-[1.3]">"Udviklingen er bagud, og ingen kan forklare hvorfor."«/p>
            «p class="flex items-baseline gap-[0.55em] text-[0.95rem] text-graphite before:content-['→'] before:shrink-0 before:font-mono before:text-ink">Vi finder ud af, hvor det knirker.«/p>
        «/sigta:tile-grid.tile>
    «/sigta:tile-grid>
    </x-kit-example>

    <x-kit-example label="Four columns — services (4 → 2 → 1)">
    «sigta:tile-grid cols="4">
        «sigta:tile-grid.tile num="01" class="min-h-[200px]">
            «sigta:heading level="3" class="mt-[18px] mb-2">Teknisk ledelse«/sigta:heading>
            «p class="text-[0.95rem] leading-[1.5] text-graphite">Strategi, roadmap og arkitektur på deltid.«/p>
        «/sigta:tile-grid.tile>
        «sigta:tile-grid.tile num="02" class="min-h-[200px]">
            «sigta:heading level="3" class="mt-[18px] mb-2">Din mand i rummet«/sigta:heading>
            «p class="text-[0.95rem] leading-[1.5] text-graphite">Med til leverandørmøder og gennemgang af tilbud.«/p>
        «/sigta:tile-grid.tile>
        «sigta:tile-grid.tile num="03" class="min-h-[200px]">
            «sigta:heading level="3" class="mt-[18px] mb-2">Teknisk vurdering«/sigta:heading>
            «p class="text-[0.95rem] leading-[1.5] text-graphite">Sundhedstjek af kodebase, projekt eller leverandør.«/p>
        «/sigta:tile-grid.tile>
        «sigta:tile-grid.tile num="04" class="min-h-[200px]">
            «sigta:heading level="3" class="mt-[18px] mb-2">Hands-on udvikling«/sigta:heading>
            «p class="text-[0.95rem] leading-[1.5] text-graphite">MVP'er, prototyper og kritiske systemer.«/p>
        «/sigta:tile-grid.tile>
    «/sigta:tile-grid>
    </x-kit-example>
</x-kit-shell>
