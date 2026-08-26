<x-kit-shell title="Elapsed clock">
    <x-kit-example label="Live-ticking clock — counts up from a start instant">
    «div class="flex flex-wrap items-start gap-10">
        «div>
            «sigta:label class="mb-1 block">Since page load«/sigta:label>
            «sigta:elapsed-clock class="font-head text-2xl" />
        «/div>
        «div>
            «sigta:label class="mb-1 block">Since ~2h ago«/sigta:label>
            «sigta:elapsed-clock :since="now()->subHours(2)->toIso8601String()" class="font-head text-2xl" />
        «/div>
    «/div>
    «p class="mt-4 max-w-[60ch] font-mono text-[0.7rem] text-graphite">
        Pass «code class="text-[0.85em]">:since«/code> an ISO string or a Carbon/DateTime; omit it for "now".
        The value ticks client-side and cleans up its interval on teardown.
    «/p>
    </x-kit-example>

    <x-kit-example label="Duration display — static, compact or clock">
    «div class="flex flex-wrap items-baseline gap-10 font-head text-2xl">
        «sigta:duration :minutes="75" />
        «sigta:duration :minutes="120" />
        «sigta:duration :minutes="45" />
        «sigta:duration :minutes="605" format="clock" />
    «/div>
    «p class="mt-4 max-w-[60ch] font-mono text-[0.7rem] text-graphite">
        «code class="text-[0.85em]">&lt;sigta:duration :minutes&gt;«/code> renders whole minutes as
        «code class="text-[0.85em]">1h 15m«/code> (default) or «code class="text-[0.85em]">10:05«/code> (format="clock").
    «/p>
    </x-kit-example>
</x-kit-shell>
