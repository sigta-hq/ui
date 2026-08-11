<x-kit-shell title="Dashboard">
    <x-sigta::text variant="muted" class="max-w-[52ch]">Tier 2 acceptance: the Plumb product spotlight from the brand board, rebuilt entirely from library components — app shell, stats, hand-drawn chart, segmented control, all on an ink band.</x-sigta::text>

    {{-- Ink spotlight band --}}
    <div class="bg-inverse text-inverse-fg rounded-frame p-5 md:p-10 shadow-paper">
        <div class="flex justify-between items-end gap-6 flex-wrap mb-7">
            <div>
                <x-sigta::eyebrow class="text-inverse-fg-muted before:bg-inverse-fg">Product spotlight</x-sigta::eyebrow>
                <x-sigta::heading level="2" size="xl" class="text-inverse-fg mt-3">Plumb — the dashboard that knows when to shut up.</x-sigta::heading>
                <x-sigta::text class="text-inverse-fg-muted max-w-[38ch] mt-2">Same ink, same paper, on a real product surface. Grayscale by default; one highlight where it matters.</x-sigta::text>
            </div>
            <x-sigta::button class="bg-surface! text-ink! border-surface! hover:-translate-y-[2px]">Try Plumb <x-sigta::icon name="arrow-right" size="sm" /></x-sigta::button>
        </div>

        {{-- App frame --}}
        <x-sigta::shell>
            <x-slot:sidebar>
                <x-sigta::shell.sidebar>
                    <x-slot:brand>
                        <x-sigta::brand name="Plumb" size="sm" />
                    </x-slot:brand>
                    <x-sigta::shell.link icon="layout-grid" current>Overview</x-sigta::shell.link>
                    <x-sigta::shell.link icon="trending-up">Trends</x-sigta::shell.link>
                    <x-sigta::shell.link icon="file-text">Reports</x-sigta::shell.link>
                    <x-sigta::shell.link icon="settings">Settings</x-sigta::shell.link>
                </x-sigta::shell.sidebar>
            </x-slot:sidebar>

            <x-sigta::shell.main>
                <x-sigta::shell.bar heading="Overview" sub="Q2 2026 · updated 6 min ago">
                    <x-slot:actions>
                        <x-sigta::segmented name="range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" checked="30d" />
                        <x-sigta::pill variant="success">On track</x-sigta::pill>
                    </x-slot:actions>
                </x-sigta::shell.bar>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <x-sigta::stat label="Active accounts" value="2,418" trend="up" delta="6.2% vs last 30d" />
                    <x-sigta::stat label="Net retention" value="112%" trend="up" delta="1.4 pts" />
                    <x-sigta::stat label="Time to value" value="3.1d" trend="down" delta="0.4d slower" />
                </div>

                <div class="bg-surface border border-line rounded-panel p-4">
                    <div class="flex justify-between items-center mb-2 font-mono text-[0.7rem] tracking-[0.06em] text-graphite">
                        <span>Weekly active workspaces</span>
                        <span>26 weeks</span>
                    </div>
                    <x-sigta::chart.line
                        label="Weekly active workspaces trending upward over 26 weeks"
                        :values="[42,45,44,48,47,52,55,53,58,57,60,63,61,66,68,67,72,74,73,79,82,80,86,89,92,98]"
                    />
                </div>
            </x-sigta::shell.main>
        </x-sigta::shell>
    </div>
</x-kit-shell>
