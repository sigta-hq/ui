@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'icon' => null,
    'type' => 'text',
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
    @if ($icon)<span data-scope="input" data-part="wrap">
        <x-sigta::icon :name="$icon" size="sm" data-scope="input" data-part="icon" />
    @endif
    <input data-sigta="input" type="{{ $type }}" {{ $attributes->merge($controlAttributes) }} />
    @if ($icon)</span>@endif
</x-sigta::field>
