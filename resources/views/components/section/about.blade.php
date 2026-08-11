{{-- Brand "Om Sigta": device-framed portrait + text column. Portrait defaults
     to an inline SVG placeholder (assets/soren.jpg lives in the real site, not
     here); pass the `portrait` slot to drop in a real framed image. --}}
@props([
    'eyebrow' => 'Om Sigta · Manden bag',
    'heading' => 'Jeg hedder Søren, og jeg taler begge sprog.',
    'paragraphs' => [
        'Jeg har bygget software i 14 år og har siddet på begge sider af bordet: som udvikleren, der byggede løsningen, og som rådgiveren, der skulle vurdere den.',
        'Min filosofi for softwareudvikling er enkel: find det gode fundament, hold fast i den simple løsning, og optimér, når vi ved, at det virker.',
        'Jeg har været med til at udvikle store projekter, der bruges i mange lande, bygget integrationer i store virksomheder, og truffet de gode teknologivalg som CTO hos Donor Network.',
        'Min hverdag ligger hos Donor Network. Sigta er min mulighed for at hjælpe flere på samme måde.',
    ],
])

<section data-sigta="section.about" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="grid items-center gap-[clamp(32px,5vw,72px)] md:grid-cols-[0.8fr_1.2fr]">
            <div class="relative">
                @isset($portrait)
                    {{ $portrait }}
                @else
                    {{-- device frame + inline SVG portrait placeholder --}}
                    <div class="overflow-hidden rounded-frame border border-line bg-surface-raised shadow-paper text-ink">
                        <svg viewBox="0 0 686 800" xmlns="http://www.w3.org/2000/svg" class="block h-auto w-full" role="img" aria-label="Pladsholder for sort-hvidt portræt af Søren Risbøll Frisk">
                            <rect width="686" height="800" style="fill: var(--sigta-surface-raised)"/>
                            <rect x="24" y="24" width="638" height="752" rx="8" fill="none" style="stroke: var(--sigta-line)" stroke-width="2" stroke-dasharray="10 12"/>
                            <path d="M343 176 c68 -4 122 52 120 126 -2 76 -52 134 -120 134 s-118 -58 -120 -134 c-2 -74 52 -122 120 -126z" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                            <path d="M150 800 c6 -156 88 -234 193 -236 s189 78 193 236" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                            <path d="M262 258 c8 26 32 42 60 40 M366 296 c22 4 42 -6 52 -24" fill="none" style="stroke: var(--sigta-line)" stroke-width="4" stroke-linecap="round"/>
                            <text x="343" y="716" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="22" letter-spacing="4" style="fill: var(--sigta-fg-muted)">PORTRÆT PÅ VEJ</text>
                        </svg>
                    </div>
                    <x-sigta::annot :rotate="-5" class="absolute -bottom-[38px] right-[6%]">det er mig ✎</x-sigta::annot>
                @endisset
            </div>
            <div>
                @if ($eyebrow)
                    <x-sigta::eyebrow class="mb-[1.2rem]">{{ $eyebrow }}</x-sigta::eyebrow>
                @endif
                <x-sigta::heading level="2" size="xl" class="mb-[1.2rem]">{{ $heading }}</x-sigta::heading>
                @foreach ($paragraphs as $paragraph)
                    <p class="mb-4 max-w-[52ch] text-graphite last:mb-0">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </div>
</section>
