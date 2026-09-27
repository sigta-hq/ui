<x-kit-shell title="Panel">
    <x-kit-example label="Bordered surface (static, no clip)">
    «div class="grid gap-[clamp(18px,2.2vw,28px)] md:grid-cols-2">
        «sigta:panel>
            «span class="mb-[1.1rem] block font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite">Spor 1 · Timepris«/span>
            «sigta:heading level="3" class="mb-4 text-[clamp(1.4rem,2.2vw,1.8rem)]">Klassisk: én lav, fast timepris«/sigta:heading>
            «p class="text-[0.98rem] leading-[1.6] text-graphite">Én timepris. Ingen tillæg, ingen seniorrater, ingen overraskelser.«/p>
        «/sigta:panel>

        «sigta:panel>
            «sigta:annot :rotate="-5" class="absolute -top-3 right-[8%] text-[1.3rem]">overhæng vises«/sigta:annot>
            «span class="mb-[1.1rem] block font-mono text-[0.66rem] tracking-[0.14em] uppercase text-graphite">Spor 2 · Fast månedspris«/span>
            «sigta:heading level="3" class="mb-4 text-[clamp(1.4rem,2.2vw,1.8rem)]">Skræddersyet software som abonnement«/sigta:heading>
            «p class="text-[0.98rem] leading-[1.6] text-graphite">Panelet klipper ikke den overhængende note, fordi der ikke er overflow-hidden.«/p>
        «/sigta:panel>
    «/div>
    </x-kit-example>

    <x-kit-example label="Own layout (a display class replaces the stack)">
    «div class="max-w-md">
        «sigta:panel class="flex items-center justify-between gap-3 p-4">
            «sigta:profile name="Søren Frisk" meta="Studio lead · Copenhagen" />
            «sigta:badge>Owner«/sigta:badge>
        «/sigta:panel>
    «/div>
    </x-kit-example>

    <x-kit-example label="Inverse (dark) surface">
    «div class="max-w-sm">
        «sigta:panel variant="inverse" class="gap-4">
            «div>
                «sigta:label variant="inverse" class="block">Logged this customer«/sigta:label>
                «p class="mt-1 font-head text-2xl tabular-nums">42h 10m«/p>
            «/div>
            «div>
                «sigta:label variant="inverse" class="block">Billable value«/sigta:label>
                «p class="mt-1 font-head text-2xl tabular-nums">29.750 kr.«/p>
            «/div>
        «/sigta:panel>
    «/div>
    </x-kit-example>
</x-kit-shell>
