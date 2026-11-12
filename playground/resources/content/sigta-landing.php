<?php

// Sigta's own landing copy. It lives in the playground, not the package: the
// section components ship without content, so every site passes its own.
// kit/landing.blade.php reads this file; the Sigta site can copy it as is.

$book = 'https://calendly.com/sf-sigta/30min';

return [
    'hero' => [
        'eyebrow' => 'Tech-partner · Hjælp, rådgivning og udvikling',
        'lede' => 'Sigta er den tech-partner, du kan få: hjælp, rådgivning og udvikling til virksomheder, der ikke har deres egen tekniske ledelse. Vi taler begge sprog, forretning og teknik.',
        'primaryLabel' => 'Book en intro-samtale',
        'primaryHref' => $book,
        'secondaryLabel' => 'Se hvordan vi arbejder',
        'secondaryHref' => '#hjaelp',
        'stats' => [
            ['value' => '14 år', 'label' => 'bag tastaturet'],
            ['value' => '1', 'label' => 'fast lav timepris'],
            ['value' => '2', 'label' => 'måder at samarbejde på'],
        ],
    ],

    'recognition' => [
        'heading' => 'Kan du genkende det?',
        'lede' => 'Fire situationer, hvor det hjælper at have en teknisk stemme på sin egen side af bordet.',
        'items' => [
            [
                'num' => '01 · Tilbuddet i indbakken',
                'scene' => '"Vi har fået et tilbud på 750.000 kr. fra et bureau. Er det rimeligt?"',
                'answer' => 'Svært at vide, når man ikke taler sproget. Vi læser det med dig, linje for linje.',
            ],
            [
                'num' => '02 · Projektet skrider',
                'scene' => '"Udviklingen er bagud, budgettet er brugt, og ingen kan rigtig forklare hvorfor."',
                'answer' => 'Vi finder ud af, hvor det knirker, og hvad der skal til for at komme i mål.',
            ],
            [
                'num' => '03 · Klar til at bygge',
                'scene' => '"Idéen er på plads. Men arkitektur, teknologivalg, og hvem skal bygge det?"',
                'answer' => 'Vi lægger planen sammen med dig, før du bruger den første krone.',
            ],
            [
                'num' => '04 · Vokset fra gaffatapen',
                'scene' => '"Virksomheden er vokset. Systemerne er fulgt med, men kun på gaffatape."',
                'answer' => 'Vi rydder op og sætter en teknisk retning, der holder til næste vækstspring.',
            ],
        ],
        'closingText' => 'Hvis bare ét af dem rammer, så lad os tage en kop kaffe.',
        'ctaLabel' => 'Book en intro-samtale',
        'ctaHref' => $book,
    ],

    'features' => [
        'heading' => 'Sådan hjælper vi.',
        'lede' => 'Fire måder at bruge en tech-partner på. De fleste samarbejder starter med én og vokser derfra.',
        'items' => [
            [
                'num' => '01',
                'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><circle cx="17" cy="17" r="13" stroke="currentColor" stroke-width="2"/><path d="M17 17 L23 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="17" cy="17" r="2.4" fill="currentColor"/><path d="M17 4 V7 M17 27 V30 M4 17 H7 M27 17 H30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                'title' => 'Teknisk ledelse',
                'text' => 'Strategi, roadmap og arkitektur på deltid. En ekstern CTO, uden en CTO på lønningslisten.',
            ],
            [
                'num' => '02',
                'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M5 8 H21 V19 H12 L8 23 V19 H5 Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M24 13 H29 V24 H26 V28 L22 24 H15" stroke="currentColor" stroke-width="2" stroke-linejoin="round" style="fill: var(--sigta-accent)"/></svg>',
                'title' => 'Din mand i rummet',
                'text' => 'Med til leverandørmøder og gennemgang af tilbud og kontrakter. Rådgivningen er uvildig: Sigta tjener ikke på, hvilken løsning du vælger.',
            ],
            [
                'num' => '03',
                'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><circle cx="14" cy="14" r="9" stroke="currentColor" stroke-width="2"/><path d="M21 21 L29 29" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M10 14 L13 17 L18.5 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                'title' => 'Teknisk vurdering',
                'text' => 'Sundhedstjek af kodebase, projekt eller leverandør. Et ærligt svar på spørgsmålet: er det her fornuftigt?',
            ],
            [
                'num' => '04',
                'icon' => '<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M12 9 6 17 12 25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 9 28 17 22 25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 7 15 27" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
                'title' => 'Hands-on udvikling',
                'text' => "MVP'er, prototyper og kritiske systemer. Vi kan også selv skrive koden. Omhyggeligt.",
            ],
        ],
    ],

    'pricing' => [
        'heading' => 'To måder at samarbejde på.',
        'lede' => 'Én filosofi bag begge: du skal altid vide, hvad du betaler for.',
        'tiers' => [
            [
                'label' => 'Spor 1 · Timepris',
                'title' => 'Klassisk: én lav, fast timepris',
                'figure' => '800 kr.',
                'figureSuffix' => '/ time, ekskl. moms',
                'mark' => true,
                'body' => [
                    'Én timepris. Ingen tillæg, ingen seniorrater, ingen overraskelser. Prisen er bevidst lavere end hos de store konsulenthuse, fordi et partnerskab fungerer bedst, når fokus er på at bygge og ikke på at diskutere, hvem der betaler for hvad.',
                    'Nogle gange går tingene godt, andre gange skal noget laves om. Den lave timepris gør, at vi kan begge dele uden dårlig stemning.',
                ],
            ],
            [
                'label' => 'Spor 2 · Fast månedspris',
                'title' => 'Skræddersyet software som abonnement',
                'figure' => 'Fast pris',
                'figureSuffix' => '/ måned, aftalt på forhånd',
                'mark' => false,
                'annot' => 'som SaaS, bare jeres eget',
                'body' => [
                    'Den nye måde at få software på: Vi bygger et værktøj eller en platform tilpasset jer, og I betaler en fast månedlig pris, som var det et SaaS-abonnement.',
                    'Startinvesteringen er en brøkdel af et klassisk udviklingsprojekt, og drift og vedligehold er regnet med. Softwaren bliver ved med at virke, og I ved præcis, hvad den koster.',
                ],
            ],
        ],
    ],

    'about' => [
        'eyebrow' => 'Om Sigta · Manden bag',
        'heading' => 'Jeg hedder Søren, og jeg taler begge sprog.',
        'paragraphs' => [
            'Jeg har bygget software i 14 år og har siddet på begge sider af bordet: som udvikleren, der byggede løsningen, og som rådgiveren, der skulle vurdere den.',
            'Min filosofi for softwareudvikling er enkel: find det gode fundament, hold fast i den simple løsning, og optimér, når vi ved, at det virker.',
            'Jeg har været med til at udvikle store projekter, der bruges i mange lande, bygget integrationer i store virksomheder, og truffet de gode teknologivalg som CTO hos Donor Network.',
            'Min hverdag ligger hos Donor Network. Sigta er min mulighed for at hjælpe flere på samme måde.',
        ],
    ],

    'proof' => [
        'yearsValue' => '14 år',
        'yearsLabel' => 'erfaring med software',
        'logos' => ['Park Styling', 'Jelling Musikfestival'],
    ],

    'quote' => [
        'quote' => 'Du behøver ikke forstå koden for at træffe gode beslutninger. Du skal bare kende én, der gør.',
        'cite' => 'Sådan arbejder Sigta',
    ],

    'faq' => [
        'heading' => 'Spørgsmål, vi ofte får.',
        'lede' => 'Og de ærlige svar. Mangler dit, så spørg løs på intro-samtalen.',
        'items' => [
            ['q' => 'Hvad koster det?', 'a' => 'Én fast timepris: 800 kr./time ekskl. moms. Ingen minimumskøb, og intro-samtalen er gratis. For skræddersyede værktøjer aftaler vi en fast månedspris, før vi går i gang.'],
            ['q' => 'Hvor meget tid skal vi bruge?', 'a' => 'Det afhænger af opgaven. Nogle kunder bruger et par timer om måneden til sparring, andre mere i perioder. Vi skalerer op og ned efter behov.'],
            ['q' => 'Hvad er en tech-partner?', 'a' => 'En fast partner, der dækker hele spektret fra rådgivning og sparring til hands-on udvikling. For mange virksomheder fungerer det som en teknisk direktør på deltid, internationalt kaldet en fractional CTO, men samarbejdet former sig efter jeres behov.'],
            ['q' => 'Har vi overhovedet brug for en tech-partner?', 'a' => 'Måske ikke. Det er også et svar, du kan få hos os. Intro-samtalen er netop til at finde ud af, om det giver mening for jer.'],
            ['q' => 'Vi har allerede udviklere. Giver det så mening?', 'a' => 'Ja. Udviklere og teknisk ledelse er to forskellige jobs. Dygtige udviklere fortjener en, der sætter retning, prioriterer og holder arkitekturen sund.'],
            ['q' => 'Hvad med fortrolighed?', 'a' => 'NDA er standard. Alt, hvad vi ser og hører, bliver mellem os.'],
            ['q' => 'Er vi bundet af noget?', 'a' => 'Nej. Timepris-samarbejdet kan stoppes når som helst. For værktøjer til fast månedspris aftaler vi vilkårene på forhånd, så der ikke er overraskelser.'],
        ],
    ],

    'cta' => [
        'heading' => 'Skal vi tage en snak, før du skriver under på noget?',
        'lede' => 'En intro-samtale koster ingenting og forpligter ikke. Medbring gerne det tilbud, du er i tvivl om.',
        'primaryLabel' => 'Book en intro-samtale',
        'primaryHref' => $book,
        'secondaryLabel' => 'Skriv en mail',
        'secondaryHref' => 'mailto:sf@sigta.dk',
        'annotation' => 'lad os tegne det op ↘',
    ],

    'footer' => [
        'tagline' => 'Sigta er tech-partneren for virksomheder uden egen teknisk ledelse: hjælp, rådgivning og udvikling.',
        'signoff' => '© 2026 Sigta · CVR 45530760',
        'columns' => [
            [
                'heading' => 'Navigation',
                'links' => [
                    ['label' => 'Kan du genkende det?', 'href' => '#genkend'],
                    ['label' => 'Sådan hjælper vi', 'href' => '#hjaelp'],
                    ['label' => 'Priser', 'href' => '#priser'],
                    ['label' => 'Om Sigta', 'href' => '#om'],
                ],
            ],
            [
                'heading' => 'Kontakt',
                'links' => [
                    ['label' => 'Book en intro-samtale', 'href' => $book],
                    ['label' => 'sf@sigta.dk', 'href' => 'mailto:sf@sigta.dk'],
                    ['label' => 'LinkedIn', 'href' => 'https://www.linkedin.com/in/soeren-frisk/'],
                ],
            ],
        ],
    ],
];
