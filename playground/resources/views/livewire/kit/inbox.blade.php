<div class="overflow-hidden rounded-frame border border-line bg-surface-raised">
    <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-4">
        <div class="flex items-baseline gap-3" data-inbox-heading>
            <x-sigta::heading level="3" size="base">Inbox</x-sigta::heading>
            <span class="font-mono text-[0.68rem] uppercase tracking-[0.14em] text-graphite"><x-sigta::count :value="count($items)" /> open</span>
        </div>
        <x-sigta::button size="sm" variant="ghost" wire:click="arrive">
            <x-sigta::icon name="plus" size="sm" /> Something arrives
        </x-sigta::button>
    </div>

    {{-- The list stays rendered when empty, so its last row can fold away. --}}
    <ul data-sigta-motion id="kit-inbox-list" class="divide-y divide-line">
        @foreach ($items as $item)
            <li wire:key="inbox-{{ $item['id'] }}" class="flex items-center gap-3.5 px-5 py-3.5">
                <x-sigta::checkbox
                    aria-label="Mark “{{ $item['title'] }}” done"
                    x-on:change="$sigta.complete($el, () => $wire.complete({{ $item['id'] }}))"
                />
                <div class="min-w-0 flex-1">
                    <p class="truncate font-head text-[1rem] leading-snug text-ink"><span data-sigta-strike>{{ $item['title'] }}</span></p>
                    <p class="mt-0.5 font-mono text-[0.62rem] uppercase tracking-[0.12em] text-graphite">{{ $item['from'] }} · {{ $item['meta'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>

    @if ($items === [])
        <div class="flex flex-col items-center gap-3 px-5 py-10 text-center animate-sigta-settle">
            <x-sigta::loader size="lg" state="done" label="All done" />
            <p class="font-hand text-[1.6rem] font-semibold leading-none text-ink">All clear.</p>
            <x-sigta::text variant="muted">Nothing waiting on you. Go make something.</x-sigta::text>
            <x-sigta::button size="sm" wire:click="refill">Refill the demo</x-sigta::button>
        </div>
    @endif
</div>
