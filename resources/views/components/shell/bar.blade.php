{{-- Shell top bar (brand .app-bar): heading + mono sub on the left,
     `actions` slot (segmented, pills, buttons) on the right. --}}
@props([
    'heading' => null,
    'sub' => null,
])

<header data-sigta="shell.bar" {{ $attributes->merge(['class' => 'flex justify-between items-center gap-4 flex-wrap']) }}>
    <div class="flex flex-col">
        @if ($heading)
            <h3 class="font-head text-[1.3rem] font-medium leading-[1.2]">{{ $heading }}</h3>
        @endif
        @if ($sub)
            <span class="font-mono text-[0.72rem] text-graphite">{{ $sub }}</span>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2 flex-wrap">{{ $actions }}</div>
    @endisset
</header>
