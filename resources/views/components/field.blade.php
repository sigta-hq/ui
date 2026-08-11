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
    <div data-sigta="field" {{ $attributes->merge(['class' => 'flex flex-col gap-[0.4rem]']) }}>
        @if ($label)
            <x-sigta::label for="{{ $for }}">{{ $label }}</x-sigta::label>
        @endif

        {{ $slot }}

        @if ($description)
            <p id="{{ $for }}-description" class="font-mono text-[0.7rem] leading-relaxed text-graphite">{{ $description }}</p>
        @endif

        @if ($error)
            <p id="{{ $for }}-error" class="font-mono text-[0.7rem] leading-relaxed text-danger">{{ $error }}</p>
        @endif
    </div>
@else
    {{ $slot }}
@endif
