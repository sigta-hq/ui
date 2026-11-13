<x-kit-shell title="Features">
    <x-kit-example label="Header + tiles (icon as a Lucide name or an inline svg string)">
    «sigta:section.features
        heading="How we help."
        lede="Three ways to work with us. Most teams start with one."
        :cols="3"
        :items="[
            ['num' => '01', 'icon' => 'compass', 'title' => 'Technical leadership', 'text' => 'Strategy, roadmap and architecture, part-time.'],
            ['num' => '02', 'icon' => 'search-check', 'title' => 'Technical review', 'text' => 'A health check of a codebase, a project or a vendor.'],
            ['num' => '03', 'icon' => 'code', 'title' => 'Hands-on development', 'text' => 'MVPs, prototypes and critical systems, built carefully.'],
        ]" />
    </x-kit-example>
</x-kit-shell>
