<x-kit-shell title="Section · FAQ">
    <x-kit-example label="Spørgsmål — zero-config (7 default entries, single-open)">
    «sigta:section.faq />
    </x-kit-example>

    <x-kit-example label="Custom heading + your own entries">
    «sigta:section.faq
        heading="Ofte stillede spørgsmål"
        lede="Kan du ikke finde dit svar? Skriv til os."
        :items="[
            ['q' => 'Hvordan kommer vi i gang?', 'a' => 'Book en intro-samtale, så tager vi den derfra.'],
            ['q' => 'Hvad koster det?', 'a' => 'Én fast, lav timepris. Ingen overraskelser.'],
        ]" />
    </x-kit-example>
</x-kit-shell>
