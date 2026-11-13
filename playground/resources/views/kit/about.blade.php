<x-kit-shell title="Section · About">
    <x-kit-example label="Text only (one column)">
    «sigta:section.about
        eyebrow="About us · The team"
        heading="A small studio with a long memory."
        :paragraphs="[
            'We have built software for fourteen years, on both sides of the table.',
            'Our approach is simple: find the solid foundation, keep the simple solution, and optimise once we know it works.',
        ]" />
    </x-kit-example>

    <x-kit-example label="With your own framed portrait (two columns)">
    «sigta:section.about
        eyebrow="About us · The team"
        heading="A small studio with a long memory."
        :paragraphs="['We have built software for fourteen years, on both sides of the table.']">
        «x-slot:portrait>
            «div class="overflow-hidden rounded-frame border border-line bg-surface-raised shadow-paper aspect-[686/800] grid place-items-center text-graphite font-mono text-[0.8rem] tracking-[0.14em] uppercase">Your portrait«/div>
            «sigta:annot :rotate="-5" class="absolute -bottom-[38px] right-[6%]">that’s us ✎«/sigta:annot>
        «/x-slot:portrait>
    «/sigta:section.about>
    </x-kit-example>
</x-kit-shell>
