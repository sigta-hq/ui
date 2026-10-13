<x-kit-shell title="Pricing">
    <x-kit-example label="Zero-config (brand defaults)">
    «sigta:section.pricing />
    </x-kit-example>

    <x-kit-example label="A long figure suffix drops below the figure as one block">
    «sigta:section.pricing
        heading="Én fast pris."
        :tiers="[
            ['label' => 'Hele forløbet', 'title' => 'Teknisk vurderingsplan', 'figure' => '60.000 kr.', 'figureSuffix' => 'ekskl. moms', 'mark' => true, 'body' => ['Halvdelen ved opstart, resten ved gennemgang.']],
            ['label' => 'Hvis vi ikke er de rette', 'title' => 'Anbefaling og overdragelse', 'figure' => '40.000 kr.', 'figureSuffix' => '+ 20.000 kr. til Leverandør-sparring, ekskl. moms', 'body' => ['Resten bliver til sparring, eller du får dem tilbage.']],
        ]" />
    </x-kit-example>

    <x-kit-example label="One tier with the aside slot beside it">
    «sigta:section.pricing
        heading="Én fast pris."
        :tiers="[
            ['title' => 'Teknisk vurderingsplan', 'figure' => '60.000 kr.', 'figureSuffix' => 'ekskl. moms', 'mark' => true, 'body' => ['Halvdelen ved opstart, resten ved gennemgang.']],
        ]">
        «x-slot:aside>
            «p class="text-[1.1rem]">I tvivl om, det passer til dig? Det finder vi ud af på 30 minutter.«/p>
            «sigta:button variant="cta" href="https://calendly.com/sf-sigta/30min" target="_blank" class="self-start">Book en gratis intro-samtale«/sigta:button>
        «/x-slot:aside>
    «/sigta:section.pricing>
    </x-kit-example>
</x-kit-shell>
