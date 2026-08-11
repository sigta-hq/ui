@props([
    'name',
])

<div
    role="tabpanel"
    id="sigta-panel-{{ $name }}"
    aria-labelledby="sigta-tab-{{ $name }}"
    tabindex="0"
    x-cloak
    x-show="isActive('{{ $name }}')"
    {{ $attributes->merge(['class' => 'pt-5']) }}
    data-sigta="tabs.panel"
>{{ $slot }}</div>
