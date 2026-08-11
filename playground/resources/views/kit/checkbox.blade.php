<x-kit-shell title="Checkbox">
    <x-kit-example label="Basic">
    «div class="flex flex-col gap-3">
        «sigta:checkbox label="Paper grain overlay" checked />
        «sigta:checkbox label="High-contrast ink" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="flex flex-col gap-3">
        «sigta:checkbox label="Locked on (checked)" checked disabled />
        «sigta:checkbox label="Locked off" disabled />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bare (own label elsewhere)">
    «div class="flex items-center gap-3">
        «sigta:checkbox id="kit-checkbox-bare" checked />
        «sigta:label for="kit-checkbox-bare">Wired via for/id«/sigta:label>
    «/div>
    </x-kit-example>

    <x-kit-example label="Group in a field">
    «div class="max-w-sm">
        «sigta:field label="Overlays" description="Applied to every exported frame.">
            «div class="flex flex-col gap-2">
                «sigta:checkbox label="Paper grain" checked />
                «sigta:checkbox label="Dot grid" checked />
                «sigta:checkbox label="Margin notes" />
            «/div>
        «/sigta:field>
    «/div>
    </x-kit-example>
</x-kit-shell>
