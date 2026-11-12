<x-kit-shell title="Typography">
    <x-kit-example label="Heading scale">
    «div class="flex flex-col gap-6">
        «sigta:heading level="2" size="display">Written by hand«/sigta:heading>
        «sigta:heading level="2" size="xl">Software, shipped like a product«/sigta:heading>
        «sigta:heading level="3" size="lg">What we do, and keep doing«/sigta:heading>
        «sigta:heading level="4">Panel heading«/sigta:heading>
    «/div>
    </x-kit-example>

    <x-kit-example label="Text variants">
    «div class="flex flex-col gap-4 max-w-[56ch]">
        «sigta:text variant="lede">We design and build software the careful way — researched, drawn, typed, and tested.«/sigta:text>
        «sigta:text>Body text in Newsreader. Comfortable at long line lengths, with a paper-first reading rhythm and «em>true italics«/em> where emphasis calls for them.«/sigta:text>
        «sigta:text variant="muted">Muted body text for secondary explanations.«/sigta:text>
        «sigta:text variant="caption">SIGTA · est. 2017 · made on paper«/sigta:text>
    «/div>
    </x-kit-example>

    <x-kit-example label="Highlighter & annotations">
    «div class="flex flex-col gap-6">
        «sigta:heading level="2" size="xl">Technology, translated to «sigta:mark>business«/sigta:mark>.«/sigta:heading>
        «div class="flex gap-8 items-center">
            «sigta:annot>drawn, not generated ↓«/sigta:annot>
            «sigta:annot rotate="4" muted>a real chart, on real paper«/sigta:annot>
        «/div>
        «sigta:eyebrow>Design &amp; engineering studio · est. 2017«/sigta:eyebrow>
    «/div>
    </x-kit-example>

    <x-kit-example label="Rules & separators">
    «div class="flex flex-col gap-6">
        «sigta:rule variant="1" />
        «sigta:rule variant="2" />
        «sigta:separator />
        «sigta:separator variant="dashed" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Brand lockup">
    «div class="flex items-end gap-8">
        «sigta:brand name="Acme" size="sm">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>
        «sigta:brand name="Acme">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>
        «sigta:brand name="Acme" size="lg">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>
        «sigta:brand name="Acme" glyph-only>«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>
        «span class="bg-inverse text-inverse-fg p-3 rounded-base inline-flex">«sigta:brand name="Acme" class="text-inverse-fg">«x-slot:logo>«sigta:icon name="hexagon" />«/x-slot:logo>«/sigta:brand>«/span>
    «/div>
    </x-kit-example>
</x-kit-shell>
