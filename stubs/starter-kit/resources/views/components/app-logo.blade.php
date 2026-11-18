@props([
    'sidebar' => false,
])

@php
    $tag = $attributes->has('href') ? 'a' : 'span';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'inline-flex min-w-0 items-center text-ink no-underline']) }}>
    <sigta:brand :name="config('app.name', 'Laravel')" size="sm">
        <x-slot:logo>
            <x-app-logo-icon />
        </x-slot:logo>
    </sigta:brand>
</{{ $tag }}>
