{{-- Centered proof strip between hairlines: a years stat + client logos. The
     real logo files live in the site, not here — logos render as mono
     placeholder wordmarks. --}}
@props([
    'yearsValue' => '14 år',
    'yearsLabel' => 'erfaring med software',
    'logos' => ['Park Styling', 'Jelling Musikfestival'],
])

<section data-sigta="section.proof" {{ $attributes->merge(['aria-label' => 'Erfaring og kunder']) }}>
    <div data-scope="section.proof" data-part="wrap">
        <div data-scope="section.proof" data-part="strip">
            <x-sigta::stat variant="inline" :value="$yearsValue" :label="$yearsLabel" data-scope="section.proof" data-part="stat" />
            @foreach ($logos as $logo)
                <span data-scope="section.proof" data-part="logo">{{ $logo }}</span>
            @endforeach
        </div>
    </div>
</section>
