@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'rows' => 4,
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

{{-- The text is the slot, or a value attribute when the slot is empty (a
     <textarea> has no value attribute of its own). --}}
<x-sigta::field :label="$label" :description="$description" :error="$error" :for="$id">
    <textarea data-sigta="textarea" rows="{{ $rows }}" {{ $attributes->except('value')->merge($controlAttributes) }}>@if ($slot->isEmpty()){{ $attributes->get('value') }}@else{{ $slot }}@endif</textarea>
</x-sigta::field>
