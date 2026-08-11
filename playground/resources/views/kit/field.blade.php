<x-kit-shell title="Field">
    <x-kit-example label="Field wrapping a custom control">
    «div class="max-w-sm">
        «sigta:field label="Density" description="How compact the tables render.">
            «div class="flex flex-col gap-2">
                «sigta:radio name="kit-field-density" label="Comfortable density" checked />
                «sigta:radio name="kit-field-density" label="Compact density" />
            «/div>
        «/sigta:field>
    «/div>
    </x-kit-example>

    <x-kit-example label="Explicit for/id wiring">
    «div class="max-w-sm">
        «sigta:field label="Project name" description="Shown on invoices and reports." for="kit-field-custom">
            «sigta:input id="kit-field-custom" value="Marginalia v3" />
        «/sigta:field>
    «/div>
    </x-kit-example>

    <x-kit-example label="Error state">
    «div class="max-w-sm">
        «sigta:field label="Workspace" error="Pick a workspace before continuing." for="kit-field-error">
            «sigta:select id="kit-field-error">
                «option value="">—«/option>
                «option>Marginalia«/option>
                «option>Plumb«/option>
            «/sigta:select>
        «/sigta:field>
    «/div>
    </x-kit-example>

    <x-kit-example label="Standalone label">
    «sigta:label for="kit-label-target">Engagement type«/sigta:label>
    </x-kit-example>
</x-kit-shell>
