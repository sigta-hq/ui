@props([
    'icon' => null,
    'variant' => 'default',
    'kbd' => null,
])

@php
// A button item closes its menu when clicked. A consumer's own x-on:click runs first:
// an element can carry an attribute only once, so the two handlers are joined into one.
$onClick = rtrim(trim((string) $attributes->get('x-on:click', '')), ';');
$onClick = ($onClick !== '' ? $onClick.'; ' : '').'close(true)';
@endphp

@if ($attributes->has('href'))
    <a role="menuitem" tabindex="-1" data-sigta="dropdown.item" data-variant="{{ $variant }}" {{ $attributes }}>
@else
    <button type="button" role="menuitem" tabindex="-1" data-sigta="dropdown.item" data-variant="{{ $variant }}" x-on:click="{{ $onClick }}" {{ $attributes->except('x-on:click') }}>
@endif
        @if ($icon)<x-sigta::icon :name="$icon" size="sm" data-scope="dropdown.item" data-part="icon" />@endif
        <span data-scope="dropdown.item" data-part="label">{{ $slot }}</span>
        @if ($kbd)<kbd data-scope="dropdown.item" data-part="kbd">{{ $kbd }}</kbd>@endif
@if ($attributes->has('href'))
    </a>
@else
    </button>
@endif
