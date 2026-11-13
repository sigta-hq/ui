{{-- Centered proof strip between hairlines: a stat (e.g. years in business)
     + client names, set as mono wordmarks. Ships no copy: pass your own stat
     and :logos, and an aria-label to name the region. --}}
@props([
    'yearsValue' => null,
    'yearsLabel' => null,
    'logos' => [],
])

<section data-sigta="section.proof" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }}>
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="flex flex-wrap items-center justify-center gap-[clamp(20px,4vw,56px)] border-y border-line py-[clamp(24px,3vw,40px)]">
            @if ($yearsValue || $yearsLabel)
                <x-sigta::stat variant="inline" :value="$yearsValue" :label="$yearsLabel" class="text-center" />
            @endif
            @foreach ($logos as $logo)
                <span class="font-mono text-[0.9rem] tracking-[0.16em] uppercase text-graphite opacity-80">{{ $logo }}</span>
            @endforeach
        </div>
    </div>
</section>
