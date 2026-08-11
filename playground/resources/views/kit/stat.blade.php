<x-kit-shell title="Stat">
    <x-kit-example label="Trends — up, down & neutral">
    «div class="grid gap-3 sm:grid-cols-3">
        «sigta:stat label="Active accounts" value="2,418" delta="6.2% vs last 30d" trend="up" />
        «sigta:stat label="Time to value" value="3.1d" delta="0.4d slower" trend="down" />
        «sigta:stat label="Seats in use" value="14" delta="unchanged" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With sparkline slot">
    «div class="max-w-xs">
        «sigta:stat label="Monthly recurring" value="$48.2k" delta="9%" trend="up">
            «svg viewBox="0 0 200 38" preserveAspectRatio="none" class="w-full h-[38px]" aria-hidden="true">
                «path d="M0 30 C30 28 40 20 70 22 S120 12 150 14 190 4 200 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                «circle cx="200" cy="6" r="3.5" fill="currentColor"/>
            «/svg>
        «/sigta:stat>
    «/div>
    </x-kit-example>

    <x-kit-example label="Label + value only">
    «div class="max-w-xs">
        «sigta:stat label="Net retention" value="112%" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Inline variant — borderless hero-meta figure">
    «div class="flex flex-wrap gap-[2.2rem]">
        «sigta:stat variant="inline" value="14 år" label="bag tastaturet" />
        «sigta:stat variant="inline" value="1" label="fast lav timepris" />
        «sigta:stat variant="inline" value="2" label="måder at samarbejde på" />
    «/div>
    </x-kit-example>
</x-kit-shell>
