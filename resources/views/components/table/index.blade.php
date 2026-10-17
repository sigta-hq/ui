{{-- Brand data table: mono uppercase caption + headers, hairline rows, tabular numerals. --}}
@props([
    'caption' => null,
])

<div data-sigta="table">
    <table data-scope="table" data-part="table" {{ $attributes }}>
        @if ($caption)
            <caption data-scope="table" data-part="caption">{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
