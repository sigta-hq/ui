@props([
    'variant' => 'info',
    'icon' => null,
    'heading' => null,
])

@php
$classes = 'flex items-start gap-[0.8rem] bg-surface border-[1.5px] border-l-4 rounded-[5px] px-[1em] py-[0.9em] text-[0.92rem]';

[$variantClasses, $iconClasses, $defaultIcon, $role] = match ($variant) {
    'success' => ['border-success', 'text-success', 'circle-check', 'status'],
    'danger' => ['border-danger', 'text-danger', 'circle-alert', 'alert'],
    default => ['border-ink', 'text-ink', 'triangle-alert', 'status'],
};
@endphp

<div data-sigta="callout" {{ $attributes->merge(['role' => $role, 'class' => $classes.' '.$variantClasses]) }}>
    <x-sigta::icon :name="$icon ?? $defaultIcon" class="shrink-0 mt-[2px] {{ $iconClasses }}" />
    <div class="min-w-0">
        @if ($heading)<b class="font-head font-semibold">{{ $heading }}</b> @endif{{ $slot }}
    </div>
</div>
