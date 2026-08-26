<x-kit-shell title="Money">
    <x-kit-example label="Display — minor units, formatted per currency">
    «div class="flex flex-col gap-2 font-head text-2xl">
        «div>«sigta:money :minor="123456" currency="DKK" />«/div>
        «div>«sigta:money :minor="123456" currency="EUR" />«/div>
        «div>«sigta:money :minor="123456" currency="USD" />«/div>
        «div>«sigta:money :minor="123456" currency="GBP" />«/div>
    «/div>
    «p class="mt-4 max-w-[60ch] font-mono text-[0.7rem] text-graphite">
        Input is minor units (øre / cents). Known currencies carry symbol, position and separators;
        override any piece or pass an unknown code (rendered as a trailing symbol).
    «/p>
    </x-kit-example>

    <x-kit-example label="Whole-major & per-unit suffix">
    «div class="flex flex-wrap items-baseline gap-10 font-head text-2xl">
        «sigta:money :minor="145000" currency="DKK" :decimals="0" suffix="/ hour" />
        «sigta:money :minor="99900" currency="USD" suffix="/ seat" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Money input — amount field + currency toggle">
    «div class="max-w-md">
        «sigta:money-input label="Hourly rate" :value="145000" currency="DKK" />
    «/div>
    «p class="mt-4 max-w-[60ch] font-mono text-[0.7rem] text-graphite">
        Type a major amount; a hidden input carries the value in minor units on the forwarded
        «code class="text-[0.85em]">wire:model«/code>. Bind the toggle with «code class="text-[0.85em]">currency-model«/code>.
    «/p>
    </x-kit-example>
</x-kit-shell>
