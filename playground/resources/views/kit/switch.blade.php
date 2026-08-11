<x-kit-shell title="Switch">
    <x-kit-example label="Basic">
    «div class="flex flex-col gap-4">
        «sigta:switch label="Grayscale mode" />
        «sigta:switch label="Weekly digest" checked />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="flex flex-col gap-4">
        «sigta:switch label="Locked on" checked disabled />
        «sigta:switch label="Locked off" disabled />
    «/div>
    </x-kit-example>

    <x-kit-example label="Without visible label">
    «sigta:switch aria-label="Toggle paper grain" checked />
    </x-kit-example>

    <x-kit-example label="In a field">
    «div class="max-w-sm">
        «sigta:field label="Notifications" description="Delivered every Friday, never more.">
            «sigta:switch label="Weekly digest" checked />
        «/sigta:field>
    «/div>
    </x-kit-example>
</x-kit-shell>
