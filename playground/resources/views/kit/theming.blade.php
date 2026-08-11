<x-kit-shell title="Theming">
    <x-sigta::text variant="muted" class="max-w-[60ch] -mt-4">
        Components read semantic <code class="font-mono text-ink">var(--sigta-*)</code> tokens, never raw colour.
        Swap the token values and the same markup carries a different brand — at build time, or at runtime by
        flipping <code class="font-mono text-ink">data-sigta-theme</code> on any subtree. See the full guide at
        <a href="/docs#theming" class="text-ink underline underline-offset-[3px]">/docs</a>.
    </x-sigta::text>

    {{-- ===================== INTERACTIVE ===================== --}}
    <x-kit-demo label="Runtime theme switch">
        <div x-data="{ theme: 'sigta' }">
            <div class="mb-6 flex flex-wrap items-center gap-4">
                <x-sigta::segmented
                    name="theme"
                    label="Brand theme"
                    :options="['sigta' => 'Sigta', 'studio' => 'Studio']"
                    checked="sigta"
                    x-model="theme" />
                <span class="font-mono text-[0.72rem] tracking-[0.08em] uppercase text-graphite">
                    live: <b class="text-ink" x-text="theme === 'sigta' ? 'Sigta (default)' : 'Studio'"></b>
                </span>
            </div>

            {{-- Empty string on 'sigta' → falls through to the :root Sigta brand;
                 'studio' → the [data-sigta-theme="studio"] scope kicks in. --}}
            <div :data-sigta-theme="theme === 'sigta' ? '' : 'studio'"
                 class="rounded-panel border border-line bg-surface-raised p-6 transition-colors duration-200 ease-out">
                @include('partials.sigta-cluster')
            </div>

            <x-sigta::text variant="muted" class="mt-4 text-[0.9rem]">
                Toggle the control above — nothing inside the panel re-renders. Only the token values change,
                so radii, fonts, accent, and surfaces all shift together while the component tree stays put.
            </x-sigta::text>
        </div>
    </x-kit-demo>

    {{-- ===================== STATIC SIDE BY SIDE ===================== --}}
    <x-kit-demo label="Same markup, two brands (static)">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-panel border border-line bg-surface-raised p-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="font-mono text-[0.7rem] tracking-[0.1em] uppercase text-graphite">Sigta</span>
                    <x-sigta::badge>:root default</x-sigta::badge>
                </div>
                @include('partials.sigta-cluster')
            </div>

            <div data-sigta-theme="studio" class="rounded-panel border border-line bg-surface-raised p-5">
                <div class="mb-4 flex items-center justify-between">
                    <span class="font-mono text-[0.7rem] tracking-[0.1em] uppercase text-graphite">Studio (example rebrand)</span>
                    <x-sigta::badge>data-sigta-theme="studio"</x-sigta::badge>
                </div>
                @include('partials.sigta-cluster')
            </div>
        </div>
    </x-kit-demo>
</x-kit-shell>
