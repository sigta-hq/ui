@props([
    'label' => null,
    'description' => null,
    'error' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$classes = 'peer w-full appearance-none font-body text-[0.98rem] text-ink bg-surface border-[1.5px] rounded-[5px] py-[0.62em] pl-[0.8em] pr-[2.2em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none';
$classes .= $error ? ' border-danger' : ' border-line';

$controlAttributes = ['id' => $id, 'class' => $classes];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <span class="relative block">
        <select data-sigta="select" {{ $attributes->merge($controlAttributes) }}>{{ $slot }}</select>
        <x-sigta::icon name="chevron-down" class="pointer-events-none absolute right-[0.7em] top-1/2 -translate-y-1/2 text-graphite peer-disabled:opacity-40" size="sm" />
    </span>
</x-sigta::field>
