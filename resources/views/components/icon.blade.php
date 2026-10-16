@props([
    'name',
    'size' => 'base',
])

{{-- Drawn from the pinned Lucide set in packages/contract/icons, the same
     markup the Svelte runtime renders. Decorative by default; pass an
     aria-label (and aria-hidden="false") for a meaningful icon. --}}
<svg data-sigta="icon" data-size="{{ $size }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['stroke-width' => '1.75', 'aria-hidden' => 'true']) }}>{!! \Sigta\Ui\Icons::markup($name) !!}</svg>
