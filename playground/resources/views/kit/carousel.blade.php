<x-kit-shell title="Carousel">
    <x-kit-example label="One per view">
    «sigta:carousel label="Selected work">
        «sigta:carousel.slide>
            «div class="flex min-h-48 flex-col justify-between rounded-panel border border-line bg-surface-raised p-6">
                «span class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite">Engagement«/span>
                «div>
                    «h3 class="font-head text-2xl text-ink">Marginalia«/h3>
                    «p class="mt-2 max-w-[48ch] text-graphite">A reading-and-notes app for teams — annotate any doc, search every margin.«/p>
                «/div>
            «/div>
        «/sigta:carousel.slide>
        «sigta:carousel.slide>
            «div class="flex min-h-48 flex-col justify-between rounded-panel border border-line bg-surface-raised p-6">
                «span class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite">Engagement«/span>
                «div>
                    «h3 class="font-head text-2xl text-ink">Halyard OS«/h3>
                    «p class="mt-2 max-w-[48ch] text-graphite">A dispatch and routing console for a freight client. 4,000 trucks, one calm screen.«/p>
                «/div>
            «/div>
        «/sigta:carousel.slide>
    «/sigta:carousel>
    </x-kit-example>

    <x-kit-example label="Multi per view (basis)">
    «sigta:carousel label="Snapshots">
        «sigta:carousel.slide basis="basis-1/2 md:basis-1/3">
            «div class="mr-0 flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised">
                «span class="font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite">Discovery«/span>
            «/div>
        «/sigta:carousel.slide>
        «sigta:carousel.slide basis="basis-1/2 md:basis-1/3">
            «div class="mr-0 flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised">
                «span class="font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite">Design«/span>
            «/div>
        «/sigta:carousel.slide>
        «sigta:carousel.slide basis="basis-1/2 md:basis-1/3">
            «div class="mr-0 flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised">
                «span class="font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite">Build«/span>
            «/div>
        «/sigta:carousel.slide>
        «sigta:carousel.slide basis="basis-1/2 md:basis-1/3">
            «div class="mr-0 flex h-32 items-center justify-center rounded-panel border border-line bg-surface-raised">
                «span class="font-mono text-[0.72rem] tracking-[0.14em] uppercase text-graphite">Launch«/span>
            «/div>
        «/sigta:carousel.slide>
    «/sigta:carousel>
    </x-kit-example>
</x-kit-shell>
