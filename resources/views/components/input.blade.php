@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'icon' => null,
    'type' => 'text',
    'variant' => 'default',
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$inline = $variant === 'inline';

// The inline form is a value in a detail view, not a form field: no box until it
// is hovered or focused, and its frame sits in the margin so the text lines up
// with the plain values around it, the way the inline editor does.
$classes = match (true) {
    $inline => 'w-full font-body text-[0.98rem] text-ink bg-transparent placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.3em] transition duration-150 ease-out hover:border-line focus:outline-none focus:border-ink disabled:opacity-40 disabled:pointer-events-none',
    default => 'w-full font-body text-[0.98rem] text-ink bg-surface placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.62em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none',
};
$classes .= $error ? ' border-danger' : ($inline ? ' border-transparent' : ' border-line');
$classes .= $icon ? ' pl-[2.4em]' : '';

$controlAttributes = ['id' => $id, 'class' => $classes];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    @if ($icon || $inline)<span class="relative block {{ $inline ? '-mx-[0.8em]' : '' }}">@endif
    @if ($icon)
        <x-sigta::icon :name="$icon" class="pointer-events-none absolute left-[0.8em] top-1/2 -translate-y-1/2 text-graphite" size="sm" />
    @endif
    <input data-sigta="input" data-variant="{{ $variant }}" type="{{ $type }}" {{ $attributes->merge($controlAttributes) }} />
    @if ($icon || $inline)</span>@endif
</x-sigta::field>
