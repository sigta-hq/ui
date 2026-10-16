@props([
    'variant' => 'secondary',
    'size' => 'base',
    'loading' => null,
])

@php
$linkAttributes = ['data-variant' => $variant, 'data-size' => $size];
if ($attributes->get('target') === '_blank' && ! $attributes->has('rel')) {
    $linkAttributes['rel'] = 'noopener';
}

// While Livewire runs a button's action it sets data-loading on the button (on
// the form for a submit). The label never gives way: once the action outlasts
// a beat the runtime marks the button data-sigta-busy and the compass stands
// in for its leading icon, or is written in before a label that has none, so
// a quick action shows nothing. The loader markup is added only where that
// can happen, unless `loading` says otherwise: true shows it now (a busy
// button you drive yourself), false never.
$busy = $loading ?? ($attributes->get('type') === 'submit' || collect(array_keys($attributes->getAttributes()))->contains(
    fn (string $name) => str_starts_with($name, 'wire:click') || str_starts_with($name, 'wire:submit') || in_array($name, ['data-loading', 'x-bind:data-loading', ':data-loading'], true)
));

$buttonAttributes = ['type' => 'button', 'data-variant' => $variant, 'data-size' => $size];
if ($busy && str_starts_with(ltrim((string) $slot), '<svg')) {
    $buttonAttributes['data-lead-icon'] = '';
}
if ($loading === true) {
    $buttonAttributes['data-loading'] = 'true';
    $buttonAttributes['data-sigta-busy'] = '';
    $buttonAttributes['aria-busy'] = 'true';
}
@endphp

@if ($attributes->has('href'))
    <a data-sigta="button" {{ $attributes->merge($linkAttributes) }}>{{ $slot }}</a>
@else
    <button data-sigta="button" {{ $attributes->merge($buttonAttributes) }}>
        @if ($busy)
            <span data-scope="button" data-part="busy" aria-hidden="true"><x-sigta::loader size="xs" decorative /></span>
            <span data-scope="button" data-part="label">{{ $slot }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
