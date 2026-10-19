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

    <x-kit-example label="Inline (a value in a detail view)">
    «div class="grid max-w-md grid-cols-[7rem_1fr] items-center gap-x-4 gap-y-1">
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Title«/span>
        «sigta:input variant="inline" aria-label="Title" placeholder="Untitled" value="Launch the site" />
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Owner«/span>
        «span class="font-body text-[0.98rem] text-ink">Line Bak«/span>
    «/div>
    </x-kit-example>
</x-kit-shell>
