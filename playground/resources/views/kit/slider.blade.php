<x-kit-shell title="Slider">
    <x-kit-example label="With live value">
    «div class="max-w-md">«sigta:slider label="Ink density" min="0" max="100" value="60" show-value />«/div>
    </x-kit-example>

    <x-kit-example label="Plain + description">
    «div class="max-w-md">«sigta:slider label="Compression" description="Higher is smaller, slower." min="0" max="9" value="4" />«/div>
    </x-kit-example>

    <x-kit-example label="States">
    «div class="max-w-md flex flex-col gap-6">
        «sigta:slider label="Disabled" value="30" disabled />
        «sigta:slider label="Error" value="90" error="Out of safe range." />
    «/div>
    </x-kit-example>
</x-kit-shell>
