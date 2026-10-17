{{-- Count — a number that bumps when it changes: rising as it grows, dropping
     as it shrinks. A Livewire morph, an x-text or a plain write all move it,
     so it suits unread badges and "12 open" headings.

       <sigta:count :value="$open" />
       <sigta:count x-text="items.length">{{ count($items) }}</sigta:count> --}}
@props([
    'value' => null,
])

<span
    data-sigta="count"
    x-data="sigtaCount"
    {{ $attributes }}
>{{ $value ?? $slot }}</span>
