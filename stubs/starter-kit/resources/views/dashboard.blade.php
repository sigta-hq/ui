<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <sigta:shell.bar :heading="__('Dashboard')" :sub="__('Welcome back, :name', ['name' => auth()->user()->name])" />

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-panel border border-line bg-surface-raised">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-line" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-panel border border-line bg-surface-raised">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-line" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-panel border border-line bg-surface-raised">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-line" />
            </div>
        </div>

        <div class="relative min-h-80 flex-1 overflow-hidden rounded-panel border border-line bg-surface-raised">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-line" />
        </div>
    </div>
</x-layouts::app>
