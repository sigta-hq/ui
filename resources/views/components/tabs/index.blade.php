{{-- Tab group: wraps <x-sigta::tabs.list> + <x-sigta::tabs.panel>s. --}}
@props([
    'default' => null,
])

<div
    x-data="sigtaTabs(@js($default))"
    {{ $attributes }}
    data-sigta="tabs"
>
    {{ $slot }}
</div>
