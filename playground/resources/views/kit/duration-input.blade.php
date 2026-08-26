<x-kit-shell title="Duration input">
    <x-kit-example label="Compact field — click to edit as HH:MM">
    «div class="flex flex-wrap items-start gap-8">
        «sigta:duration-input label="Estimate" :value="119" wire:key="est-119" />
        «sigta:duration-input label="Time logged" :value="90" wire:key="logged-90" />
        «sigta:duration-input label="No value yet" placeholder="Add time" wire:key="dur-empty" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Forgiving parse, emits whole minutes">
    «p class="max-w-[62ch] text-graphite">
        Click a field and type «code class="font-mono text-[0.85em]">1:30«/code>,
        a bare «code class="font-mono text-[0.85em]">90«/code>, or «code class="font-mono text-[0.85em]">02:05«/code>.
        On blur or Enter it parses to whole minutes and emits them on the forwarded
        «code class="font-mono text-[0.85em]">wire:model«/code> via a hidden input; Escape cancels.
        Pass a value-keyed «code class="font-mono text-[0.85em]">wire:key«/code> so a reset gives fresh state.
    «/p>
    «div class="mt-4 max-w-xs">
        «sigta:duration-input label="Estimate" :value="150" wire:key="parse-150" />
    «/div>
    </x-kit-example>
</x-kit-shell>
