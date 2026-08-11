<x-kit-shell title="Listbox">
    <x-kit-example label="Rich single select">
    «div class="max-w-xs">
        «sigta:listbox label="Country" name="country" placeholder="Pick a country…">
            «sigta:autocomplete.option value="dk">Denmark«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="se">Sweden«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="no">Norway«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="fi">Finland«/sigta:autocomplete.option>
        «/sigta:listbox>
    «/div>
    </x-kit-example>

    <x-kit-example label="Preselected value + description">
    «div class="max-w-xs">
        «sigta:listbox label="Plan" name="plan" value="studio" description="Billed yearly. Change anytime.">
            «sigta:autocomplete.option value="solo">Solo«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="studio">Studio«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="org">Organisation«/sigta:autocomplete.option>
        «/sigta:listbox>
    «/div>
    </x-kit-example>

    <x-kit-example label="Error state">
    «div class="max-w-xs">
        «sigta:listbox label="Assignee" name="assignee" error="Pick someone before saving.">
            «sigta:autocomplete.option value="sf">Søren Frisk«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="mk">Mette Kaas«/sigta:autocomplete.option>
        «/sigta:listbox>
    «/div>
    </x-kit-example>
</x-kit-shell>
