{{-- Site footer: your brand + tagline, nav columns, dashed signoff line. Uses
     a <footer> landmark as its root but carries the section contract's
     data-sigta hook. Pass the brand through the `brand` slot, typically a
     <sigta:brand> with your logo. Ships no copy or links. --}}
@props([
    'tagline' => null,
    'signoff' => null,
    'columns' => [],
])

@php
// One track per column beside the brand, or a third column wraps under the
// brand. Three columns wait for lg: four tracks squeeze their links at md.
$gridClasses = match (count($columns)) {
    1 => 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr]',
    3 => 'sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr]',
    default => 'sm:grid-cols-2 md:grid-cols-[1.5fr_1fr_1fr]',
};
@endphp

<footer data-sigta="section.footer" {{ $attributes->merge(['class' => 'mt-[clamp(50px,7vw,90px)] border-t border-line pt-[clamp(40px,5vw,64px)] pb-8']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="grid gap-8 {{ $gridClasses }}">
            <div>
                {{ $brand ?? '' }}
                @if ($tagline)
                    <p @class(['max-w-[32ch] text-[0.92rem] text-graphite', 'mt-4' => isset($brand)])>{{ $tagline }}</p>
                @endif
            </div>
            @foreach ($columns as $column)
                <div>
                    <h4 class="mb-4 font-mono text-[0.68rem] font-normal tracking-[0.12em] uppercase text-graphite">{{ $column['heading'] }}</h4>
                    <ul class="m-0 flex list-none flex-col gap-[0.6rem] p-0">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['href'] }}" class="text-[0.92rem] text-ink no-underline hover:underline hover:underline-offset-[3px]">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        @if ($signoff)
            <div class="mt-[clamp(36px,5vw,56px)] flex flex-wrap items-center justify-between gap-4 border-t border-dashed border-line pt-[1.6rem] font-mono text-[0.72rem] text-graphite">
                <span>{{ $signoff }}</span>
            </div>
        @endif
    </div>
</footer>
