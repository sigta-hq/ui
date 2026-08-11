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
</x-kit-shell>
