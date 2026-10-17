@props([
    'label' => null,
    'description' => null,
    'error' => null,
])

@php
$id = $attributes->get('id') ?? 'sigta-'.\Illuminate\Support\Str::random(8);

$controlAttributes = ['id' => $id];

if ($error) {
    $controlAttributes['aria-invalid'] = 'true';
}

if ($describedBy = trim(($description ? $id.'-description ' : '').($error ? $id.'-error' : ''))) {
    $controlAttributes['aria-describedby'] = $describedBy;
}
@endphp

<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <span data-scope="select" data-part="wrap">
        <select data-sigta="select" {{ $attributes->merge($controlAttributes) }}>{{ $slot }}</select>
        <x-sigta::icon name="chevron-down" size="sm" data-scope="select" data-part="icon" />
    </span>
</x-sigta::field>
