{{-- Brand data table: mono uppercase caption + headers, hairline rows, tabular numerals. --}}
@props([
    'caption' => null,
])

<div data-sigta="table" class="w-full overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full border-collapse text-[0.9rem]']) }}>
        @if ($caption)
            <caption class="text-left font-mono text-[0.66rem] tracking-[0.1em] uppercase text-graphite font-normal mb-[0.7rem]">{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
