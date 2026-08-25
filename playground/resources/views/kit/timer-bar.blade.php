<x-kit-shell title="Timer bar">
    <x-kit-example label="Running timer bar — default stop dispatches an event">
    «div x-data="{ stopped: false }" x-on:sigta-timer-stop="stopped = true">
        «div class="overflow-hidden rounded-panel border border-line">
            «sigta:timer-bar title="Write the customers module spec" project="Atlas" :since="now()->subMinutes(23)->toIso8601String()" />
        «/div>
        «p x-show="stopped" x-cloak class="mt-3 font-mono text-[0.7rem] text-success">sigta-timer-stop received ✓«/p>
    «/div>
    «p class="mt-4 max-w-[62ch] font-mono text-[0.7rem] text-graphite">
        Presentational only — your app owns which entry is running and how it stops.
        Catch «code class="text-[0.85em]">sigta-timer-stop«/code> on a wrapper: «code class="text-[0.85em]">x-on:sigta-timer-stop="$wire.stop()"«/code>.
    «/p>
    </x-kit-example>

    <x-kit-example label="With a Livewire-wired stop slot">
    «div class="overflow-hidden rounded-panel border border-line">
        «sigta:timer-bar title="Ledger rebuild" project="Halyard OS" :since="now()->subHours(1)->subMinutes(5)->toIso8601String()">
            «x-slot:stop>
                «sigta:button variant="inverse" size="sm">Stop«/sigta:button>
            «/x-slot:stop>
        «/sigta:timer-bar>
    «/div>
    </x-kit-example>
</x-kit-shell>
