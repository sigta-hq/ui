<x-kit-shell title="Autocomplete">
    <x-kit-example label="Filtering combobox">
    «div class="max-w-xs">
        «sigta:autocomplete label="Engagement type" name="engagement" placeholder="Search types…">
            «sigta:autocomplete.option value="full-build">Full build«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="design-sprint">Design sprint«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="embedded-team">Embedded team«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="brand-system">Brand &amp; system«/sigta:autocomplete.option>
        «/sigta:autocomplete>
    «/div>
    </x-kit-example>

    <x-kit-example label="Error state">
    «div class="max-w-xs">
        «sigta:autocomplete label="Country" error="Pick a country from the list." placeholder="Search…">
            «sigta:autocomplete.option value="dk">Denmark«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="se">Sweden«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="no">Norway«/sigta:autocomplete.option>
        «/sigta:autocomplete>
    «/div>
    </x-kit-example>
</x-kit-shell>
