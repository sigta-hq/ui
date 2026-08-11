<x-kit-shell title="Accordion">
    <x-kit-example label="FAQ — native details/summary, exclusive open via name">
    «sigta:accordion name="faq">
        «sigta:accordion.item heading="Hvad koster det?">
            «p>Én fast timepris: 800 kr./time ekskl. moms. Ingen minimumskøb, og intro-samtalen er gratis. For skræddersyede værktøjer aftaler vi en fast månedspris, før vi går i gang.«/p>
        «/sigta:accordion.item>
        «sigta:accordion.item heading="Hvor meget tid skal vi bruge?">
            «p>Det afhænger af opgaven. Nogle kunder bruger et par timer om måneden til sparring, andre mere i perioder. Vi skalerer op og ned efter behov.«/p>
        «/sigta:accordion.item>
        «sigta:accordion.item heading="Hvad er en tech-partner?">
            «p>En fast partner, der dækker hele spektret fra rådgivning og sparring til hands-on udvikling. For mange virksomheder fungerer det som en teknisk direktør på deltid, internationalt kaldet en fractional CTO, men samarbejdet former sig efter jeres behov.«/p>
        «/sigta:accordion.item>
    «/sigta:accordion>
    </x-kit-example>

    <x-kit-example label="Independent items (no name) — one open by default">
    «sigta:accordion>
        «sigta:accordion.item heading="Open by default" open>
            «p>This item was rendered with the native «code class="font-mono text-[0.8rem]">open«/code> attribute forwarded onto the details element.«/p>
        «/sigta:accordion.item>
        «sigta:accordion.item heading="Stays independent">
            «p>Without a group name, opening one item does not close the others.«/p>
        «/sigta:accordion.item>
    «/sigta:accordion>
    </x-kit-example>
</x-kit-shell>
