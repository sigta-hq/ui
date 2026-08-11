<x-kit-shell title="Input">
    <x-kit-example label="Basic">
    «div class="max-w-sm">
        «sigta:input label="Project name" value="Marginalia v3" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With description">
    «div class="max-w-sm">
        «sigta:input label="Email" type="email" placeholder="you@studio.co" description="We only use this for the invoice." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Error">
    «div class="max-w-sm">
        «sigta:input label="Project name" value="" error="A project needs a name." />
    «/div>
    </x-kit-example>

    <x-kit-example label="With leading icon">
    «div class="flex max-w-sm flex-col gap-4">
        «sigta:input label="Search" icon="search" placeholder="Search engagements…" />
        «sigta:input label="Email" icon="mail" type="email" placeholder="you@studio.co" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Types">
    «div class="flex max-w-sm flex-col gap-4">
        «sigta:input label="Password" type="password" value="hunter2sigta" />
        «sigta:input label="Kickoff date" type="date" />
        «sigta:input label="Seats" type="number" value="4" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="max-w-sm">
        «sigta:input label="Engagement id" value="SGT-2026-014" disabled />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bare (no field chrome)">
    «div class="max-w-sm">
        «sigta:input placeholder="Quick note…" aria-label="Quick note" />
    «/div>
    </x-kit-example>
</x-kit-shell>
