{{-- Centered proof strip between hairlines: a years stat + client logos. The
     real logo files live in the site, not here — logos render as mono
     placeholder wordmarks. --}}
@props([
    'yearsValue' => '14 år',
    'yearsLabel' => 'erfaring med software',
    'logos' => ['Park Styling', 'Jelling Musikfestival'],
])

<section data-sigta="section.proof" {{ $attributes->merge(['class' => 'py-[clamp(40px,6vw,72px)]']) }} aria-label="Erfaring og kunder">
    <div class="mx-auto max-w-(--sigta-maxw) px-(--sigta-gutter)">
        <div class="flex flex-wrap items-center justify-center gap-[clamp(20px,4vw,56px)] border-y border-line py-[clamp(24px,3vw,40px)]">
            <x-sigta::stat variant="inline" :value="$yearsValue" :label="$yearsLabel" class="text-center" />
            @foreach ($logos as $logo)
                <span class="font-mono text-[0.9rem] tracking-[0.16em] uppercase text-graphite opacity-80">{{ $logo }}</span>
            @endforeach
        </div>
    </div>
</section>
