<x-kit-shell title="Progress">
    <x-kit-example label="With label">
    «div class="max-w-md flex flex-col gap-6">
        «sigta:progress :value="24" label="Uploading" />
        «sigta:progress :value="68" label="Migration" />
        «sigta:progress :value="100" label="Complete" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bare">
    «div class="max-w-md">«sigta:progress :value="45" />«/div>
    </x-kit-example>

    <x-kit-example label="Indeterminate">
    «div class="max-w-md">«sigta:progress indeterminate label="Working" />«/div>
    </x-kit-example>
</x-kit-shell>
