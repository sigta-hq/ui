<x-kit-shell title="Section · Footer">
    <x-kit-example label="Site footer — zero-config (brand, nav columns, signoff)">
    «sigta:section.footer />
    </x-kit-example>

    <x-kit-example label="Custom tagline + columns">
    «sigta:section.footer
        tagline="Et lille studie, der bygger holdbar software."
        signoff="© 2026 Dit Firma · CVR 00000000"
        :columns="[
            ['heading' => 'Produkt', 'links' => [
                ['label' => 'Funktioner', 'href' => '#funktioner'],
                ['label' => 'Priser', 'href' => '#priser'],
            ]],
            ['heading' => 'Kontakt', 'links' => [
                ['label' => 'Skriv til os', 'href' => 'mailto:hej@eksempel.dk'],
            ]],
        ]" />
    </x-kit-example>

    <x-kit-example label="Three columns (one row from lg)">
    «sigta:section.footer
        :columns="[
            ['heading' => 'Navigation', 'links' => [
                ['label' => 'Sådan hjælper vi', 'href' => '#hjaelp'],
                ['label' => 'Priser', 'href' => '#priser'],
            ]],
            ['heading' => 'Ydelser', 'links' => [
                ['label' => 'Teknisk vurdering', 'href' => '#teknisk-vurdering'],
                ['label' => 'Ekstra udvikler', 'href' => '#ekstra-udvikler'],
            ]],
            ['heading' => 'Kontakt', 'links' => [
                ['label' => 'sf@sigta.dk', 'href' => 'mailto:sf@sigta.dk'],
            ]],
        ]" />
    </x-kit-example>
</x-kit-shell>
