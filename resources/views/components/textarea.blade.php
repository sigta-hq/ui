@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'rows' => 4,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$classes = 'w-full resize-y font-body text-[0.98rem] text-ink bg-surface placeholder:text-graphite/70 border-[1.5px] rounded-[5px] px-[0.8em] py-[0.62em] transition duration-150 ease-out focus:outline-none focus:border-ink focus:shadow-[0_0_0_3px_color-mix(in_srgb,var(--sigta-fg)_8%,transparent)] disabled:opacity-40 disabled:pointer-events-none';
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
    <textarea data-sigta="textarea" rows="{{ $rows }}" {{ $attributes->merge($controlAttributes) }}>{{ $slot }}</textarea>
</x-sigta::field>
