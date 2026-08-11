<x-kit-shell title="Card">
    <x-kit-example label="With thumb — hover lift">
    «div class="grid gap-6 sm:grid-cols-2">
        «sigta:card>
            «x-slot:thumb>
                «svg viewBox="0 0 400 225" preserveAspectRatio="xMidYMid slice" class="absolute inset-0 size-full text-ink" aria-hidden="true">
                    «g stroke="var(--sigta-line)" stroke-width="1">«path d="M0 56 H400"/>«path d="M0 112 H400"/>«path d="M0 168 H400"/>«/g>
                    «rect x="290" y="40" width="60" height="34" rx="3" fill="var(--sigta-accent)" opacity=".9"/>
                    «path d="M30 170 C90 160 110 120 170 110 S270 80 320 60 380 48 380 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    «circle cx="380" cy="48" r="5" fill="currentColor"/>
                «/svg>
            «/x-slot:thumb>
            «div class="font-mono text-[0.7rem] tracking-[0.12em] uppercase text-graphite flex justify-between">«span>Our product«/span>«span>SaaS · live«/span>«/div>
            «h3 class="font-head font-medium text-[1.5rem] mt-2 mb-1.5">Marginalia«/h3>
            «p class="text-graphite text-[0.96rem] m-0">A reading-and-notes app for teams — annotate any doc, search every margin.«/p>
        «/sigta:card>

        «sigta:card>
            «x-slot:thumb>
                «svg viewBox="0 0 400 225" preserveAspectRatio="xMidYMid slice" class="absolute inset-0 size-full text-ink" aria-hidden="true">
                    «g fill="currentColor" opacity=".85">«rect x="40" y="120" width="36" height="70" rx="3"/>«rect x="100" y="90" width="36" height="100" rx="3"/>«rect x="160" y="105" width="36" height="85" rx="3"/>«rect x="220" y="64" width="36" height="126" rx="3"/>«/g>
                    «rect x="280" y="80" width="36" height="110" rx="3" fill="var(--sigta-accent)"/>
                    «rect x="280" y="80" width="36" height="110" rx="3" fill="none" stroke="currentColor" stroke-width="2"/>
                    «path d="M58 110 118 80 178 96 238 56 298 70" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="2 6"/>
                «/svg>
            «/x-slot:thumb>
            «div class="font-mono text-[0.7rem] tracking-[0.12em] uppercase text-graphite flex justify-between">«span>Our product«/span>«span>SaaS · live«/span>«/div>
            «h3 class="font-head font-medium text-[1.5rem] mt-2 mb-1.5">Plumb«/h3>
            «p class="text-graphite text-[0.96rem] m-0">Metrics without the dashboard fatigue. One honest number per question.«/p>
        «/sigta:card>
    «/div>
    </x-kit-example>

    <x-kit-example label="Body only — static (no hover lift)">
    «div class="max-w-md">
        «sigta:card :hover="false">
            «h3 class="font-head font-medium text-[1.28rem] mb-1.5">Halyard OS«/h3>
            «p class="text-graphite text-[0.96rem] m-0">A dispatch and routing console for a freight client. 4,000 trucks, one calm screen.«/p>
        «/sigta:card>
    «/div>
    </x-kit-example>
</x-kit-shell>
