<x-kit-shell title="Composer">
    <x-kit-example label="Default">
    «div
        class="max-w-xl"
        x-data="{ log: [] }"
        x-on:sigta-composer-submit="log.push($event.detail.text)"
    >
        «sigta:composer name="message" placeholder="Message the studio…" />

        «template x-if="log.length">
            «ul class="mt-4 flex flex-col gap-1.5 font-mono text-[0.72rem] text-graphite">
                «template x-for="(line, i) in log" :key="i">
                    «li class="border-b border-line pb-1.5">
                        «span class="text-graphite">sent →«/span> «span class="text-ink" x-text="line">«/span>
                    «/li>
                «/template>
            «/ul>
        «/template>

        «p class="mt-3 font-mono text-[0.66rem] tracking-[0.06em] text-graphite">
            Enter sends · Shift+Enter for a newline · grows to six rows.
        «/p>
    «/div>
    </x-kit-example>

    <x-kit-example label="Custom leading slot">
    «div class="max-w-xl">
        «sigta:composer placeholder="Add a comment…">
            «x-slot:leading>
                «sigta:button variant="ghost" aria-label="Mention someone">
                    «sigta:icon name="at-sign" size="sm" />
                «/sigta:button>
            «/x-slot:leading>
        «/sigta:composer>
    «/div>
    </x-kit-example>
</x-kit-shell>
