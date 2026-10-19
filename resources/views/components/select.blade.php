@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'variant' => 'default',
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$inline = $variant === 'inline';

// Inline reads as a value until it is hovered or focused, so its chevron waits too.
$classes = match (true) {
    $inline => 'peer w-full appearance-none font-body text-[0.98rem] text-ink bg-transparent border-[1.5px] rounded-[5px] py-[0.3em] pl-[0.8em] pr-[2.2em] transition duration-150 ease-out hover:border-line focus:outline-none focus:border-ink disabled:opacity-40 disabled:pointer-events-none',
    default => 'peer w-full appearance-none font-body text-[0.98rem] text-ink bg-surface border-[1.5px] rounded-[5px] py-[0.62em] pl-[0.8em] pr-[2.2em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none',
};
$classes .= $error ? ' border-danger' : ($inline ? ' border-transparent' : ' border-line');
$wrapClasses = $inline ? 'group/select relative block -mx-[0.8em]' : 'relative block';
$chevronClasses = 'pointer-events-none absolute right-[0.7em] top-1/2 -translate-y-1/2 text-graphite peer-disabled:opacity-40'
    .($inline ? ' opacity-0 transition-opacity group-hover/select:opacity-100 peer-focus:opacity-100' : '');

$controlAttributes = ['id' => $id, 'class' => $classes];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <span class="{{ $wrapClasses }}">
        <select data-sigta="select" data-variant="{{ $variant }}" {{ $attributes->merge($controlAttributes) }}>{{ $slot }}</select>
        <x-sigta::icon name="chevron-down" :class="$chevronClasses" size="sm" />
    </span>
</x-sigta::field>
