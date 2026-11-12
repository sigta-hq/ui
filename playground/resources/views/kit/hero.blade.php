<x-kit-shell title="Hero">
    <x-kit-example label="Copy, two CTAs and a stat row (heading HTML in the default slot)">
    «sigta:section.hero
        eyebrow="Product studio · Design and development"
        lede="We help teams without in-house engineering leadership plan, build and run software that lasts."
        primary-label="Book a call"
        primary-href="#"
        secondary-label="See our work"
        secondary-href="#work"
        :stats="[
            ['value' => '12', 'label' => 'years shipping'],
            ['value' => '40+', 'label' => 'products launched'],
            ['value' => '1', 'label' => 'flat hourly rate'],
        ]">
        Software, «em>built«/em> to «sigta:mark>last«/sigta:mark>.
    «/sigta:section.hero>
    </x-kit-example>

    <x-kit-example label="With a visual beside the copy (two columns from lg)">
    «sigta:section.hero
        heading="Ship with confidence."
        lede="A short line on what you do and who it is for."
        primary-label="Get started"
        primary-href="#">
        «x-slot:visual>
            «div class="aspect-[4/3] grid place-items-center rounded-frame border border-line bg-surface-raised shadow-paper font-mono text-[0.8rem] tracking-[0.14em] uppercase text-graphite">Your illustration«/div>
        «/x-slot:visual>
    «/sigta:section.hero>
    </x-kit-example>
</x-kit-shell>
