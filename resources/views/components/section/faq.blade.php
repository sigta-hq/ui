{{-- Brand FAQ: section-header + accordion. The accordion `name` makes it a
     single-open group (native <details name>); items come from `:items`
     (array of ['q' => …, 'a' => …]). --}}
@props([
    'heading' => 'Spørgsmål, vi ofte får.',
    'lede' => 'Og de ærlige svar. Mangler dit, så spørg løs på intro-samtalen.',
    'name' => 'faq',
    'items' => [
        ['q' => 'Hvad koster det?', 'a' => 'Én fast timepris: 800 kr./time ekskl. moms. Ingen minimumskøb, og intro-samtalen er gratis. For skræddersyede værktøjer aftaler vi en fast månedspris, før vi går i gang.'],
        ['q' => 'Hvor meget tid skal vi bruge?', 'a' => 'Det afhænger af opgaven. Nogle kunder bruger et par timer om måneden til sparring, andre mere i perioder. Vi skalerer op og ned efter behov.'],
        ['q' => 'Hvad er en tech-partner?', 'a' => 'En fast partner, der dækker hele spektret fra rådgivning og sparring til hands-on udvikling. For mange virksomheder fungerer det som en teknisk direktør på deltid, internationalt kaldet en fractional CTO, men samarbejdet former sig efter jeres behov.'],
        ['q' => 'Har vi overhovedet brug for en tech-partner?', 'a' => 'Måske ikke. Det er også et svar, du kan få hos os. Intro-samtalen er netop til at finde ud af, om det giver mening for jer.'],
        ['q' => 'Vi har allerede udviklere. Giver det så mening?', 'a' => 'Ja. Udviklere og teknisk ledelse er to forskellige jobs. Dygtige udviklere fortjener en, der sætter retning, prioriterer og holder arkitekturen sund.'],
        ['q' => 'Hvad med fortrolighed?', 'a' => 'NDA er standard. Alt, hvad vi ser og hører, bliver mellem os.'],
        ['q' => 'Er vi bundet af noget?', 'a' => 'Nej. Timepris-samarbejdet kan stoppes når som helst. For værktøjer til fast månedspris aftaler vi vilkårene på forhånd, så der ikke er overraskelser.'],
    ],
])

<section data-sigta="section.faq" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <x-sigta::section-header
            class="mb-[clamp(28px,4vw,52px)]"
            :heading="$heading"
            :lede="$lede" />
        <x-sigta::accordion :name="$name" class="mx-auto max-w-[760px]">
            @foreach ($items as $item)
                <x-sigta::accordion.item :heading="$item['q']">
                    <p>{{ $item['a'] }}</p>
                </x-sigta::accordion.item>
            @endforeach
        </x-sigta::accordion>
    </div>
</section>
