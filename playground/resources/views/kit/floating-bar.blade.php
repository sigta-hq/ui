<x-kit-shell title="Floating bar">
    <x-kit-example label="Shown and hidden by state (the pill slides up from the bottom of the window)">
    «div x-data="{ running: false }" class="flex flex-wrap gap-3">
        «sigta:button x-on:click="running = ! running">Toggle bar«/sigta:button>
        «sigta:floating-bar x-bind:data-show="running ? 'true' : 'false'" label="Running timer">
            «span class="size-2 shrink-0 animate-pulse rounded-full bg-danger" aria-hidden="true">«/span>
            «span class="truncate font-head text-[0.95rem]">Redesign the pricing page«/span>
            «sigta:elapsed-clock :since="now()->subMinutes(12)" class="text-inverse-fg" />
            «sigta:button size="sm" variant="secondary">Stop«/sigta:button>
        «/sigta:floating-bar>
    «/div>
    «sigta:text variant="caption" class="mt-4">From Livewire, bind show to state: the morph updates data-show and the bar animates. Wrap it in &#64;persist in a wire:navigate layout.«/sigta:text>
    </x-kit-example>
</x-kit-shell>
