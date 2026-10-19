<x-kit-shell title="Date Picker">
    <x-kit-example label="Basic">
    «div class="max-w-sm">
        «sigta:date-picker label="Due date" name="due" placeholder="Pick a date" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Pre-filled value">
    «div class="max-w-sm">
        «sigta:date-picker label="Kickoff" name="kickoff" value="2026-07-09" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With description">
    «div class="max-w-sm">
        «sigta:date-picker label="Invoice date" name="invoice" value="2026-07-09"
            description="Type a date or pick one from the calendar." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bounded (min/max)">
    «div class="max-w-sm">
        «sigta:date-picker label="Delivery window" name="delivery"
            value="2026-07-15" min="2026-07-06" max="2026-07-24" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Error">
    «div class="max-w-sm">
        «sigta:date-picker label="Start date" name="start" error="Choose a start date." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="max-w-sm">
        «sigta:date-picker label="Locked" name="locked" value="2026-07-09" disabled />
    «/div>
    </x-kit-example>

    <x-kit-example label="Inline (a value in a detail view)">
    «div class="grid max-w-md grid-cols-[7rem_1fr] items-center gap-x-4 gap-y-1">
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Due date«/span>
        «sigta:date-picker variant="inline" aria-label="Due date" placeholder="No due date" />
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Owner«/span>
        «span class="font-body text-[0.98rem] text-ink">Line Bak«/span>
    «/div>
    </x-kit-example>
</x-kit-shell>
