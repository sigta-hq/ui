{{-- A tab's panel. The group's default panel renders shown; the others stay cloaked
     until Alpine shows the active one. --}}
@aware([
    'default' => null,
])

@props([
    'name',
])

<div
    role="tabpanel"
    id="sigta-panel-{{ $name }}"
    aria-labelledby="sigta-tab-{{ $name }}"
    tabindex="0"
    @if ($name !== $default) x-cloak @endif
    x-show="isActive('{{ $name }}')"
    {{ $attributes }}
    data-sigta="tabs.panel"
>{{ $slot }}</div>
