<x-kit-shell title="Calendar">
    <x-kit-example label="Standalone">
    «sigta:calendar name="day" value="2026-07-09" />
    </x-kit-example>

    <x-kit-example label="No selection (opens on today)">
    «sigta:calendar name="day2" />
    </x-kit-example>

    <x-kit-example label="Bounded — min/max disable out-of-range days">
    «sigta:calendar name="day3" value="2026-07-15" min="2026-07-06" max="2026-07-24" />
    </x-kit-example>

    <x-kit-example label="Keyboard">
    «p class="font-body text-[0.95rem] text-graphite max-w-prose">
        Focus a day and use arrow keys to move, «span class="font-mono text-[0.8rem]">Home«/span>/«span class="font-mono text-[0.8rem]">End«/span>
        for week ends, «span class="font-mono text-[0.8rem]">PageUp«/span>/«span class="font-mono text-[0.8rem]">PageDown«/span> to change month,
        and «span class="font-mono text-[0.8rem]">Enter«/span> to select. Only the active day is tabbable (roving tabindex).
    «/p>
    «div class="mt-5">
        «sigta:calendar name="day4" value="2026-07-09" />
    «/div>
    </x-kit-example>
</x-kit-shell>
