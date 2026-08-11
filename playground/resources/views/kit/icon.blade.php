<x-kit-shell title="Icon">
    <x-kit-example label="Sizes">
    «div class="flex items-end gap-4 text-ink">
        «sigta:icon name="pencil" size="xs" />
        «sigta:icon name="pencil" size="sm" />
        «sigta:icon name="pencil" />
        «sigta:icon name="pencil" size="lg" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Inherits currentColor">
    «div class="flex items-center gap-4">
        «span class="text-ink">«sigta:icon name="check" />«/span>
        «span class="text-graphite">«sigta:icon name="check" />«/span>
        «span class="text-success">«sigta:icon name="check" />«/span>
        «span class="text-danger">«sigta:icon name="triangle-alert" />«/span>
    «/div>
    </x-kit-example>

    <x-kit-example label="A sampling (any Lucide name works)">
    «div class="flex flex-wrap gap-4 text-ink">
        «sigta:icon name="arrow-right" />
        «sigta:icon name="search" />
        «sigta:icon name="settings" />
        «sigta:icon name="calendar" />
        «sigta:icon name="chart-line" />
        «sigta:icon name="upload-cloud" />
        «sigta:icon name="external-link" />
        «sigta:icon name="ellipsis" />
    «/div>
    </x-kit-example>
</x-kit-shell>
