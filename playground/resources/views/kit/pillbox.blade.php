<x-kit-shell title="Pillbox">
    <x-kit-example label="Multi-select with chips">
    «div class="max-w-md">
        «sigta:pillbox label="Disciplines" name="disciplines" placeholder="Add a discipline…">
            «sigta:autocomplete.option value="design">Product design«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="saas">SaaS engineering«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="tools">Internal tools«/sigta:autocomplete.option>
            «sigta:autocomplete.option value="brand">Brand & systems«/sigta:autocomplete.option>
        «/sigta:pillbox>
    «/div>
    </x-kit-example>
</x-kit-shell>
