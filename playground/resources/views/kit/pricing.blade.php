<x-kit-shell title="Pricing">
    <x-kit-example label="Two tiers: a marked figure and an overhanging hand-note">
    «sigta:section.pricing
        heading="Two ways to work together."
        lede="One philosophy behind both: you always know what you pay for."
        :tiers="[
            ['label' => 'Track 1 · Hourly', 'title' => 'One low, flat hourly rate', 'figure' => '$120', 'figureSuffix' => '/ hour, excl. VAT', 'mark' => true, 'body' => ['No surcharges, no senior rates, no surprises.']],
            ['label' => 'Track 2 · Monthly', 'title' => 'Custom software as a subscription', 'figure' => 'Fixed price', 'figureSuffix' => '/ month, agreed up front', 'annot' => 'like SaaS, but yours', 'body' => ['We build the tool; you pay a fixed monthly price that includes running it.']],
        ]" />
    </x-kit-example>

    <x-kit-example label="A long figure suffix drops below the figure as one block">
    «sigta:section.pricing
        heading="One fixed price."
        :tiers="[
            ['label' => 'The whole engagement', 'title' => 'Technical review plan', 'figure' => '$8,000', 'figureSuffix' => 'excl. VAT', 'mark' => true, 'body' => ['Half at kick-off, the rest at the review.']],
            ['label' => 'If we are not the right fit', 'title' => 'Recommendation and handover', 'figure' => '$5,000', 'figureSuffix' => '+ $3,000 towards vendor advisory, excl. VAT', 'body' => ['The rest goes to advisory, or you get it back.']],
        ]" />
    </x-kit-example>

    <x-kit-example label="One tier with the aside slot beside it">
    «sigta:section.pricing
        heading="One fixed price."
        :tiers="[
            ['title' => 'Technical review plan', 'figure' => '$8,000', 'figureSuffix' => 'excl. VAT', 'mark' => true, 'body' => ['Half at kick-off, the rest at the review.']],
        ]">
        «x-slot:aside>
            «p class="text-[1.1rem]">Not sure it fits? We will know in 30 minutes.«/p>
            «sigta:button variant="cta" href="#" class="self-start">Book a free intro call«/sigta:button>
        «/x-slot:aside>
    «/sigta:section.pricing>
    </x-kit-example>
</x-kit-shell>
