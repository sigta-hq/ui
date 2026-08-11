<x-kit-shell title="Radio">
    <x-kit-example label="Basic group">
    «div class="flex flex-col gap-3">
        «sigta:radio name="kit-density" label="Comfortable density" checked />
        «sigta:radio name="kit-density" label="Compact density" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="flex flex-col gap-3">
        «sigta:radio name="kit-locked" label="Locked selection" checked disabled />
        «sigta:radio name="kit-locked" label="Unavailable option" disabled />
    «/div>
    </x-kit-example>

    <x-kit-example label="Group in a field with error">
    «div class="max-w-sm">
        «sigta:field label="Billing cadence" error="Choose a cadence to continue.">
            «div class="flex flex-col gap-2">
                «sigta:radio name="kit-cadence" label="Monthly" />
                «sigta:radio name="kit-cadence" label="Quarterly" />
                «sigta:radio name="kit-cadence" label="Per milestone" />
            «/div>
        «/sigta:field>
    «/div>
    </x-kit-example>
</x-kit-shell>
