<x-kit-shell title="Shell">
    <x-kit-example label="Embedded app frame — sidebar, bar, main (narrow the viewport: the burger slides the nav in as a drawer)">
    «sigta:shell>
        «x-slot:sidebar>
            «sigta:shell.sidebar>
                «x-slot:brand>
                    «sigta:brand size="sm" glyph-only />
                    Plumb
                «/x-slot:brand>
                «sigta:shell.link href="#" icon="layout-grid" current>Overview«/sigta:shell.link>
                «sigta:shell.link href="#" icon="trending-up">Trends«/sigta:shell.link>
                «sigta:shell.link href="#" icon="clock">Reports«/sigta:shell.link>
                «sigta:shell.link href="#" icon="settings">Settings«/sigta:shell.link>
            «/sigta:shell.sidebar>
        «/x-slot:sidebar>

        «sigta:shell.main>
            «sigta:shell.bar heading="Overview" sub="Q2 2026 · updated 6 min ago">
                «x-slot:actions>
                    «sigta:segmented name="shell-range" label="Range" :options="['7d' => '7d', '30d' => '30d', 'qtd' => 'QTD']" checked="30d" />
                    «sigta:pill variant="success">On track«/sigta:pill>
                «/x-slot:actions>
            «/sigta:shell.bar>

            «div class="grid gap-3 sm:grid-cols-3">
                «sigta:stat label="Active accounts" value="2,418" delta="6.2% vs last 30d" trend="up" />
                «sigta:stat label="Net retention" value="112%" delta="1.4 pts" trend="up" />
                «sigta:stat label="Time to value" value="3.1d" delta="0.4d slower" trend="down" />
            «/div>

            «div class="bg-surface border border-line rounded-panel p-4">
                «sigta:chart.line :values="[22, 26, 25, 30, 33, 31, 38, 36, 42, 45]" :height="140" />
            «/div>
        «/sigta:shell.main>
    «/sigta:shell>
    </x-kit-example>

    <x-kit-example label="Sidebar actions (stay reachable while the nav is collapsed)">
    «sigta:shell>
        «x-slot:sidebar>
            «sigta:shell.sidebar>
                «x-slot:brand>
                    «sigta:brand size="sm" glyph-only />
                    Plumb
                «/x-slot:brand>
                «x-slot:actions>
                    «sigta:button variant="ghost" size="sm" aria-label="Quick actions">«sigta:icon name="zap" size="sm" /»«/sigta:button>
                «/x-slot:actions>
                «sigta:shell.link href="#" icon="layout-grid" current>Overview«/sigta:shell.link>
                «sigta:shell.link href="#" icon="clock">Reports«/sigta:shell.link>
            «/sigta:shell.sidebar>
        «/x-slot:sidebar>

        «sigta:shell.main>
            «sigta:text variant="muted">Narrow the viewport: the nav slides in as a drawer over this content, and the action stays beside the burger rather than going with it.«/sigta:text>
        «/sigta:shell.main>
    «/sigta:shell>
    </x-kit-example>

    <x-kit-example label="Bar — heading only, actions only">
    «div class="flex flex-col gap-5">
        «sigta:shell.bar heading="Reports" />
        «sigta:shell.bar sub="Filtered · 12 rows">
            «x-slot:actions>
                «sigta:button size="sm">Export«/sigta:button>
            «/x-slot:actions>
        «/sigta:shell.bar>
    «/div>
    </x-kit-example>
</x-kit-shell>
