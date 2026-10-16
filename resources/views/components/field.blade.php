@props([
    'label' => null,
    'description' => null,
    'error' => null,
    'for' => null,
])

@php
$for ??= 'sigta-'.\Illuminate\Support\Str::random(8);
@endphp

@if ($label || $description || $error || $attributes->isNotEmpty())
    <div data-sigta="field" {{ $attributes }}>
        @if ($label)
            <x-sigta::label for="{{ $for }}">{{ $label }}</x-sigta::label>
        @endif

        {{ $slot }}

        @if ($description)
            <p id="{{ $for }}-description" data-scope="field" data-part="description">{{ $description }}</p>
        @endif

        @if ($error)
            <p id="{{ $for }}-error" data-scope="field" data-part="error">{{ $error }}</p>
        @endif
    </div>
@else
    {{ $slot }}
@endif
