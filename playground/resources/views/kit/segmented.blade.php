<x-kit-shell title="Segmented">
    <x-kit-example label="Range picker (brand .seg)">
    «div class="flex items-center gap-6">
        «sigta:segmented name="range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" checked="30d" />
        «sigta:segmented name="density" label="Density" :options="['comfortable' => 'Comfortable', 'compact' => 'Compact']" checked="comfortable" />
    «/div>
    </x-kit-example>
</x-kit-shell>
