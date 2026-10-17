<!-- The Plumb dashboard spotlight in Svelte: the twin of dashboard.blade.php. -->
<script>
    import { Brand, Button, Chart, Eyebrow, Heading, Icon, Pill, Segmented, Shell, Stat, Text } from '@sigta-hq/svelte';
</script>

<div class="bg-inverse text-inverse-fg rounded-frame p-5 md:p-10 shadow-paper">
    <div class="flex justify-between items-end gap-6 flex-wrap mb-7">
        <div>
            <Eyebrow class="text-inverse-fg-muted before:bg-inverse-fg">Product spotlight</Eyebrow>
            <Heading level={2} size="xl" class="text-inverse-fg mt-3">Plumb — the dashboard that knows when to shut up.</Heading>
            <Text class="text-inverse-fg-muted max-w-[38ch] mt-2">Same ink, same paper, on a real product surface. Grayscale by default; one highlight where it matters.</Text>
        </div>
        <Button class="bg-surface! text-ink! border-surface! hover:-translate-y-[2px]">Try Plumb <Icon name="arrow-right" size="sm" /></Button>
    </div>

    <Shell>
        {#snippet sidebar()}
            <Shell.Sidebar>
                {#snippet brand()}
                    <Brand name="Plumb" size="sm" />
                {/snippet}
                <Shell.Link icon="layout-grid" current>Overview</Shell.Link>
                <Shell.Link icon="trending-up">Trends</Shell.Link>
                <Shell.Link icon="file-text">Reports</Shell.Link>
                <Shell.Link icon="settings">Settings</Shell.Link>
            </Shell.Sidebar>
        {/snippet}

        <Shell.Main>
            <Shell.Bar heading="Overview" sub="Q2 2026 · updated 6 min ago">
                {#snippet actions()}
                    <Segmented name="range" label="Range" options={{ '7d': '7d', '30d': '30d', qtd: 'QTD' }} checked="30d" />
                    <Pill variant="success">On track</Pill>
                {/snippet}
            </Shell.Bar>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <Stat label="Active accounts" value="2,418" trend="up" delta="6.2% vs last 30d" />
                <Stat label="Net retention" value="112%" trend="up" delta="1.4 pts" />
                <Stat label="Time to value" value="3.1d" trend="down" delta="0.4d slower" />
            </div>

            <div class="bg-surface border border-line rounded-panel p-4">
                <div class="flex justify-between items-center mb-2 font-mono text-[0.7rem] tracking-[0.06em] text-graphite">
                    <span>Weekly active workspaces</span>
                    <span>26 weeks</span>
                </div>
                <Chart.Line
                    label="Weekly active workspaces trending upward over 26 weeks"
                    values={[42, 45, 44, 48, 47, 52, 55, 53, 58, 57, 60, 63, 61, 66, 68, 67, 72, 74, 73, 79, 82, 80, 86, 89, 92, 98]}
                />
            </div>
        </Shell.Main>
    </Shell>
</div>
