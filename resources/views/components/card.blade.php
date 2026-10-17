@props([
    'hover' => true,
])

@php
// A consumer padding class on the card replaces the body's own, so the two do
// not stack into double the intended inset. The same test as ownsPadding() in
// @sigta-hq/core, which the Svelte card runs.
$contract = [];
if ($hover) {
    $contract['data-hover'] = '';
}
if (preg_match('/(^|\s)([^\s:]+:)*!?p[xytrblse]?-/', (string) $attributes->get('class'))) {
    $contract['data-padding'] = 'custom';
}
@endphp

<div data-sigta="card" {{ $attributes->merge($contract) }}>
    @isset($thumb)
        <div {{ $thumb->attributes->merge(['data-scope' => 'card', 'data-part' => 'thumb']) }}>{{ $thumb }}</div>
    @endisset
    <div data-scope="card" data-part="body">{{ $slot }}</div>
</div>
