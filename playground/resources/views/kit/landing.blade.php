{{-- Sigta landing page — composed entirely from the Sections tier.
     The sections ship without copy, so this page passes Sigta's own from
     resources/content/sigta-landing.php, plus the hero map and the portrait
     placeholder from components/landing/. This is the Tier-4 "Page" proof: a
     whole site page is a stack of Sections. --}}
@php
    $c = require resource_path('content/sigta-landing.php');
@endphp
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
            <a href="#" aria-label="Sigta forside" class="no-underline"><x-sigta::brand name="SIGTA"><x-slot:logo><x-logo /></x-slot:logo></x-sigta::brand></a>
        </x-slot:brand>

        <x-sigta::navlink href="#hjaelp">Sådan hjælper vi</x-sigta::navlink>
        <x-sigta::navlink href="#priser">Priser</x-sigta::navlink>
        <x-sigta::navlink href="#om">Om Sigta</x-sigta::navlink>
        <x-sigta::navlink href="#kontakt">Kontakt</x-sigta::navlink>

        <x-slot:actions>
            <x-sigta::button variant="cta" href="https://calendly.com/sf-sigta/30min" target="_blank" class="group">
                Book en intro-samtale <span class="transition-transform duration-[180ms] ease-out group-hover:translate-x-[3px]" aria-hidden="true">→</span>
            </x-sigta::button>
        </x-slot:actions>
    </x-sigta::navbar>

    <main id="main">
        <x-sigta::section.hero
            :eyebrow="$c['hero']['eyebrow']"
            :lede="$c['hero']['lede']"
            :primary-label="$c['hero']['primaryLabel']"
            :primary-href="$c['hero']['primaryHref']"
            target="_blank"
            :secondary-label="$c['hero']['secondaryLabel']"
            :secondary-href="$c['hero']['secondaryHref']"
            :stats="$c['hero']['stats']">
            Teknologi, <em>oversat</em> til <x-sigta::mark>forretning</x-sigta::mark>. Og&nbsp;tilbage&nbsp;igen.
            <x-slot:visual><x-landing.soekort /></x-slot:visual>
        </x-sigta::section.hero>

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="1" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.recognition id="genkend"
            :heading="$c['recognition']['heading']"
            :lede="$c['recognition']['lede']"
            :items="$c['recognition']['items']"
            :closing-text="$c['recognition']['closingText']"
            :cta-label="$c['recognition']['ctaLabel']"
            :cta-href="$c['recognition']['ctaHref']"
            target="_blank" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.features id="hjaelp"
            :heading="$c['features']['heading']"
            :lede="$c['features']['lede']"
            :items="$c['features']['items']" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="3" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.pricing id="priser"
            :heading="$c['pricing']['heading']"
            :lede="$c['pricing']['lede']"
            :tiers="$c['pricing']['tiers']" />

        <div class="max-w-(--sigta-maxw) mx-auto px-(--sigta-gutter)"><x-sigta::rule variant="2" class="my-[clamp(48px,7vw,96px)]" /></div>

        <x-sigta::section.about id="om"
            :eyebrow="$c['about']['eyebrow']"
            :heading="$c['about']['heading']"
            :paragraphs="$c['about']['paragraphs']">
            <x-slot:portrait><x-landing.portrait /></x-slot:portrait>
        </x-sigta::section.about>

        <x-sigta::section.proof aria-label="Erfaring og kunder"
            :years-value="$c['proof']['yearsValue']"
            :years-label="$c['proof']['yearsLabel']"
            :logos="$c['proof']['logos']" />

        <x-sigta::section.quote :quote="$c['quote']['quote']" :cite="$c['quote']['cite']" />

        <x-sigta::section.faq id="faq"
            :heading="$c['faq']['heading']"
            :lede="$c['faq']['lede']"
            :items="$c['faq']['items']" />

        <x-sigta::section.cta id="kontakt" target="_blank"
            :heading="$c['cta']['heading']"
            :lede="$c['cta']['lede']"
            :primary-label="$c['cta']['primaryLabel']"
            :primary-href="$c['cta']['primaryHref']"
            :secondary-label="$c['cta']['secondaryLabel']"
            :secondary-href="$c['cta']['secondaryHref']"
            :annotation="$c['cta']['annotation']" />
    </main>

    <x-sigta::section.footer
        :tagline="$c['footer']['tagline']"
        :signoff="$c['footer']['signoff']"
        :columns="$c['footer']['columns']">
        <x-slot:brand><x-sigta::brand name="SIGTA"><x-slot:logo><x-logo /></x-slot:logo></x-sigta::brand></x-slot:brand>
    </x-sigta::section.footer>

</div>

@sigtaScripts
@livewireScripts
</body>
</html>
