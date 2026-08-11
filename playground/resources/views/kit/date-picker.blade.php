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
</x-kit-shell>
