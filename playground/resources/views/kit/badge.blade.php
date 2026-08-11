<x-kit-shell title="Badge & Pill">
    <x-kit-example label="Badges — outline, solid & accent">
    «div class="flex flex-wrap items-center gap-3">
        «sigta:badge>Outline«/sigta:badge>
        «sigta:badge variant="solid">Solid«/sigta:badge>
        «sigta:badge variant="accent">Highlight«/sigta:badge>
    «/div>
    </x-kit-example>

    <x-kit-example label="Status pills">
    «div class="flex flex-wrap items-center gap-3">
        «sigta:pill variant="success">Operational«/sigta:pill>
        «sigta:pill variant="danger">Degraded«/sigta:pill>
        «sigta:pill>Paused«/sigta:pill>
    «/div>
    </x-kit-example>

    <x-kit-example label="In context — table stage column">
    «div class="flex flex-wrap items-center gap-3">
        «sigta:pill variant="success">Shipping«/sigta:pill>
        «sigta:pill variant="success">Active«/sigta:pill>
        «sigta:pill variant="danger">At risk«/sigta:pill>
        «sigta:badge variant="accent">New«/sigta:badge>
    «/div>
    </x-kit-example>
</x-kit-shell>
