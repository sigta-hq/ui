{{-- Mono kicker with leading dash — sits above section headings. --}}
<p {{ $attributes->merge(['class' => "font-mono text-[0.72rem] tracking-[0.22em] uppercase text-graphite flex items-center gap-[0.7em] before:content-[''] before:inline-block before:w-[1.6rem] before:h-px before:bg-ink"]) }} data-sigta="eyebrow">{{ $slot }}</p>
