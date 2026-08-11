{{-- Sigta landing page — composed entirely from the Sections tier.
     Every <sigta:section.*> renders its brand-default Danish content; this file
     is just the page-level assembly (shell, nav, ordering, anchors, rules).
     This is the Tier-4 "Page" proof: a whole site page is a stack of Sections. --}}
<!doctype html>
<html lang="da" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sigta · Din tech-partner · Hjælp, rådgivning og udvikling</title>
    <meta name="description" content="Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling. Vi taler både forretning og teknik. Én lav, fast timepris.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>

<a href="#main" class="absolute top-0 -left-[9999px] focus:left-0 z-[100] bg-ink text-surface font-mono text-[0.8rem] no-underline px-[1em] py-[0.6em] rounded-br-base">Til indholdet</a>

<x-sigta::paper />

<div class="relative z-10">

    <x-sigta::navbar>
        <x-slot:brand>
            <a href="#" aria-label="Sigta forside" class="no-underline"><x-sigta::brand /></a>
        </x-slot:brand>

        <x-sigta::navlink href="#hjaelp">Sådan hjælper vi</x-sigta::navlink>
        <x-sigta::navlink href="#priser">Priser</x-sigta::navlink>
        <x-sigta::navlink href="#om">Om Sigta</x-sigta::navlink>
        <x-sigta::navlink href="#kontakt">Kontakt</x-sigta::navlink>

        <x-slot:actions>
            <x-sigta::button variant="cta" href="https://calendly.com/sf-sigta/30min" target="_blank" rel="noopener" class="group">
                Book en intro-samtale <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span>
            </x-sigta::button>
        </x-slot:actions>
    </x-sigta::navbar>

    <main id="main">
        <x-sigta::section.hero />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="1" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.recognition id="genkend" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.features id="hjaelp" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="3" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.pricing id="priser" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.about id="om" />

        <x-sigta::section.proof />

        <x-sigta::section.quote />

        <x-sigta::section.faq id="faq" />

        <x-sigta::section.cta id="kontakt" />
    </main>

    <x-sigta::section.footer />

</div>

@sigtaScripts
@livewireScripts
</body>
</html>
