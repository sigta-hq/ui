{{-- håndtegnet søkort — inline SVG, tokens/currentColor only --}}
<svg viewBox="0 0 360 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Et håndtegnet søkort: en stiplet rute fra 'I er her' uden om forhindringerne 'dyrt' og 'teknisk gæld' til et kryds markeret 'sikker havn'.">
    <defs>
        <pattern id="soekort-dots" width="20" height="20" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1" style="fill: var(--sigta-line)"/>
        </pattern>
    </defs>
    {{-- kompasrose --}}
    <circle cx="60" cy="64" r="22" fill="none" stroke="currentColor" stroke-width="2"/>
    <path d="M60 46 L65 64 L60 82 L55 64 Z" fill="currentColor"/>
    <text x="60" y="38" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="11" fill="currentColor">N</text>
    {{-- forhindring: dyrt --}}
    <path d="M120 96 c10 -14 34 -12 40 2 s-4 26 -22 24 -28 -12 -18 -26z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2"/>
    <text x="140" y="112" text-anchor="middle" font-family="Caveat, cursive" font-size="16" style="fill: var(--sigta-fg-muted)">dyrt</text>
    {{-- forhindring: teknisk gæld --}}
    <path d="M166 212 c2 -18 28 -28 64 -28 s66 10 64 28 -28 30 -64 30 -66 -12 -64 -30z" style="fill: var(--sigta-surface-raised)" stroke="currentColor" stroke-width="2"/>
    <text x="230" y="219" text-anchor="middle" font-family="Caveat, cursive" font-size="17" style="fill: var(--sigta-fg-muted)">teknisk gæld</text>
    {{-- ruten --}}
    <circle cx="46" cy="252" r="7" fill="currentColor"/>
    <text x="60" y="274" font-family="Caveat, cursive" font-size="19" fill="currentColor">I er her</text>
    <path d="M46 252 C70 220 88 150 122 140 S170 168 196 158 232 108 262 92 300 74 306 62"
          fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="3 7"/>
    {{-- målet --}}
    <rect x="286" y="38" width="46" height="34" rx="3" style="fill: var(--sigta-accent)" opacity=".9" transform="rotate(-5 309 55)"/>
    <path d="M300 48 L318 66 M318 48 L300 66" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
    <ellipse cx="309" cy="57" rx="26" ry="20" fill="none" style="stroke: var(--sigta-danger)" stroke-width="1.8" transform="rotate(-8 309 57)"/>
    <text x="298" y="102" text-anchor="middle" font-family="Caveat, cursive" font-size="20" fill="currentColor" transform="rotate(-4 298 102)">sikker havn</text>
</svg>
