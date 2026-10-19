<x-kit-shell title="Select">
    <x-kit-example label="Basic">
    «div class="max-w-sm">
        «sigta:select label="Engagement type">
            «option>Full build«/option>
            «option>Design sprint«/option>
            «option>Embedded team«/option>
            «option>Brand &amp; system«/option>
        «/sigta:select>
    «/div>
    </x-kit-example>

    <x-kit-example label="With description">
    «div class="max-w-sm">
        «sigta:select label="Billing cadence" description="Changeable until the first invoice.">
            «option>Monthly«/option>
            «option>Quarterly«/option>
            «option>Per milestone«/option>
        «/sigta:select>
    «/div>
    </x-kit-example>

    <x-kit-example label="Error">
    «div class="max-w-sm">
        «sigta:select label="Workspace" error="Pick a workspace before continuing.">
            «option value="">—«/option>
            «option>Marginalia«/option>
            «option>Plumb«/option>
            «option>Halyard OS«/option>
        «/sigta:select>
    «/div>
    </x-kit-example>

    <x-kit-example label="Disabled">
    «div class="max-w-sm">
        «sigta:select label="Region" disabled>
            «option>Europe (locked)«/option>
        «/sigta:select>
    «/div>
    </x-kit-example>

    <x-kit-example label="Bare (no field chrome)">
    «div class="max-w-sm">
        «sigta:select aria-label="Range">
            «option>7d«/option>
            «option>30d«/option>
            «option>QTD«/option>
        «/sigta:select>
    «/div>
    </x-kit-example>

    <x-kit-example label="Inline (a value in a detail view)">
    «div class="grid max-w-md grid-cols-[7rem_1fr] items-center gap-x-4 gap-y-1">
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Column«/span>
        «sigta:select variant="inline" aria-label="Column">«option>Todo«/option>«option>Doing«/option>«option>Done«/option>«/sigta:select>
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Owner«/span>
        «span class="font-body text-[0.98rem] text-ink">Line Bak«/span>
    «/div>
    </x-kit-example>
</x-kit-shell>
