{{-- Brand .faq item: native <details>/<summary>, "+" rotates 45° when open. No JS. --}}
@aware([
    'name' => null,
])

@props([
    'heading',
    'name' => null,
])

<details
    data-sigta="accordion.item"
    @if ($name) name="{{ $name }}" @endif
    {{ $attributes->merge(['class' => 'group border-b border-line']) }}
>
    <summary class="list-none [&::-webkit-details-marker]:hidden cursor-pointer flex justify-between items-baseline gap-4 py-[1.1em] font-head font-medium text-[clamp(1.05rem,1.6vw,1.25rem)] after:content-['+'] after:font-mono after:font-normal after:text-[1.1rem] after:text-graphite after:shrink-0 after:transition-transform after:duration-[180ms] after:ease-out group-open:after:rotate-45">
        {{ $heading }}
    </summary>
    <div class="pb-[1.3em] text-graphite max-w-[60ch]">
        {{ $slot }}
    </div>
</details>
