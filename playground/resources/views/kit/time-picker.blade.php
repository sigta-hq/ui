<x-kit-shell title="Time Picker">
    <x-kit-example label="Basic (30-minute steps)">
    «div class="max-w-xs">
        «sigta:time-picker label="Start time" name="start" placeholder="Pick a time" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Pre-filled value">
    «div class="max-w-xs">
        «sigta:time-picker label="Standup" name="standup" value="09:30" />
    «/div>
    </x-kit-example>

    <x-kit-example label="12-hour labels (model stays 24h)">
    «div class="max-w-xs">
        «sigta:time-picker label="Lunch" name="lunch" value="13:30" format="12" />
    «/div>
    </x-kit-example>

    <x-kit-example label="15-minute steps, bounded 09:00–17:00">
    «div class="max-w-xs">
        «sigta:time-picker label="Meeting" name="meeting" step="15" min="09:00" max="17:00" value="10:15" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With description">
    «div class="max-w-xs">
        «sigta:time-picker label="Reminder" name="reminder"
            description="Type a time (e.g. 14:45) or choose from the list." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Error">
    «div class="max-w-xs">
        «sigta:time-picker label="Cutoff" name="cutoff" error="Choose a cutoff time." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="max-w-xs">
        «sigta:time-picker label="Locked" name="locked" value="12:00" disabled />
    «/div>
    </x-kit-example>
</x-kit-shell>
