<x-kit-shell title="Chart">
    <x-kit-example label="Sparkline — inherits currentColor">
    «div class="grid gap-3 sm:grid-cols-3">
        «sigta:stat label="Monthly recurring" value="$48.2k" delta="9%" trend="up">
            «sigta:chart.sparkline :values="[12, 14, 13, 17, 16, 20, 19, 24, 23, 28]" />
        «/sigta:stat>
        «sigta:stat label="Churned seats" value="41" delta="12 fewer" trend="up">
            «sigta:chart.sparkline :values="[80, 74, 78, 66, 60, 62, 52, 48, 44, 41]" class="text-graphite" />
        «/sigta:stat>
        «sigta:stat label="Support load" value="3.4h" delta="steady">
            «sigta:chart.sparkline :values="[3.2, 3.6, 3.1, 3.5, 3.3, 3.6, 3.2, 3.4]" label="Sparkline of support load hovering around 3.4 hours" />
        «/sigta:stat>
    «/div>
    </x-kit-example>

    <x-kit-example label="Line — area fill, gridlines, highlighted endpoint">
    «div class="bg-surface border border-line rounded-panel p-4">
        «div class="flex justify-between items-center mb-2 font-mono text-[0.7rem] tracking-[0.06em] text-graphite">
            «span>Weekly active workspaces«/span>
            «span>12 weeks«/span>
        «/div>
        «sigta:chart.line
            :values="[112, 118, 116, 124, 130, 128, 141, 139, 152, 149, 163, 170]"
            :labels="['W1', 'W4', 'W8', 'W12']"
            label="Area chart of weekly active workspaces rising from 112 to 170 over 12 weeks"
        />
    «/div>
    </x-kit-example>

    <x-kit-example label="Line — short height, no labels">
    «div class="max-w-md bg-surface border border-line rounded-panel p-4">
        «sigta:chart.line :values="[9, 7, 8, 6, 7, 5, 6, 4]" :height="120" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bars — highlight swipe + dashed trend">
    «div class="bg-surface border border-line rounded-panel p-4">
        «sigta:chart.bars
            :values="[54, 80, 72, 110, 96, 132]"
            :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
            :highlight="5"
            trend
            label="Bar chart of monthly builds rising from 54 to a highlighted 132"
        />
    «/div>
    </x-kit-example>

    <x-kit-example label="Bars — plain">
    «div class="max-w-md bg-surface border border-line rounded-panel p-4">
        «sigta:chart.bars :values="[18, 26, 22, 31]" :height="120" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Degenerate data — single point & all-equal">
    «div class="grid gap-3 sm:grid-cols-3">
        «div class="bg-surface border border-line rounded-panel p-4">
            «sigta:chart.sparkline :values="[42]" />
        «/div>
        «div class="bg-surface border border-line rounded-panel p-4">
            «sigta:chart.sparkline :values="[5, 5, 5, 5, 5]" />
        «/div>
        «div class="bg-surface border border-line rounded-panel p-4">
            «sigta:chart.line :values="[7]" :height="120" />
        «/div>
    «/div>
    </x-kit-example>
</x-kit-shell>
