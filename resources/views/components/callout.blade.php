@props([
    'variant' => 'info',
    'icon' => null,
    'heading' => null,
])

@php
[$defaultIcon, $role] = match ($variant) {
    'success' => ['circle-check', 'status'],
    'danger' => ['circle-alert', 'alert'],
    default => ['triangle-alert', 'status'],
};
@endphp

<div data-sigta="callout" {{ $attributes->merge(['role' => $role, 'data-variant' => $variant]) }}>
    <x-sigta::icon :name="$icon ?? $defaultIcon" data-scope="callout" data-part="icon" />
    <div data-scope="callout" data-part="body">
        @if ($heading)<b data-scope="callout" data-part="heading">{{ $heading }}</b> @endif{{ $slot }}
    </div>
</div>
