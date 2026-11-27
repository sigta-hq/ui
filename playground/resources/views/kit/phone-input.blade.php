<x-kit-shell title="Phone input">
    <x-kit-example label="Calling code picker inside the field">
    «div class="max-w-md">
        «sigta:phone-input label="Mobile number" value="+45 25 04 88 63" country="DK" :preferred="['DK', 'SE', 'NO', 'DE']" />
    «/div>
    «p class="mt-4 max-w-[60ch] font-mono text-[0.7rem] text-graphite">
        The button opens a searchable list of countries, the preferred ones first. Type or paste
        «code class="text-[0.85em]">+46 70 412 38 95«/code> or «code class="text-[0.85em]">0046 70 412 38 95«/code>
        and the country changes by itself. A hidden input carries «code class="text-[0.85em]">+46 70 412 38 95«/code>
        on the forwarded «code class="text-[0.85em]">wire:model«/code>.
    «/p>
    </x-kit-example>

    <x-kit-example label="A number from abroad, and an empty field">
    «div class="flex max-w-md flex-col gap-5">
        «sigta:phone-input label="Mobile number" value="+46 70 412 38 95" country="DK" :preferred="['DK', 'SE', 'NO', 'DE']" />
        «sigta:phone-input label="Mobile number (optional)" country="DK" :preferred="['DK', 'SE', 'NO', 'DE']" placeholder="25 04 88 63" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With an error">
    «div class="max-w-md">
        «sigta:phone-input label="Mobile number" value="+45 2504" country="DK" error="This is not a valid number in Denmark." />
    «/div>
    </x-kit-example>

    <x-kit-example label="Inline (a value in a detail view)">
    «div class="grid max-w-md grid-cols-[7rem_1fr] items-center gap-x-4 gap-y-1">
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Mobile«/span>
        «sigta:phone-input variant="inline" value="+45 25 04 88 63" country="DK" :preferred="['DK', 'SE', 'NO', 'DE']" aria-label="Mobile number" />
        «span class="font-mono text-[0.68rem] uppercase tracking-[0.08em] text-graphite">Owner«/span>
        «span class="font-body text-[0.98rem] text-ink">Line Bak«/span>
    «/div>
    </x-kit-example>
</x-kit-shell>
