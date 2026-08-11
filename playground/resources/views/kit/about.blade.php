<x-kit-shell title="Section · About">
    <x-kit-example label="Om Sigta — zero-config (SVG portrait placeholder)">
    «sigta:section.about />
    </x-kit-example>

    <x-kit-example label="Custom copy + your own framed portrait">
    «sigta:section.about
        eyebrow="Om studiet · Holdet bag"
        heading="Vi er et lille studie med store idéer.">
        «x-slot:portrait>
            «div class="overflow-hidden rounded-frame border border-line bg-surface-raised shadow-paper aspect-[686/800] grid place-items-center text-graphite font-mono text-[0.8rem] tracking-[0.14em] uppercase">Dit portræt her«/div>
        «/x-slot:portrait>
    «/sigta:section.about>
    </x-kit-example>
</x-kit-shell>
