{{-- One tab. Selected per the group's default on the server; Alpine keeps aria-selected
     and the roving tabindex in step after that. --}}
@aware([
    'default' => null,
])

@props([
    'name',
])

<button
    type="button"
    role="tab"
    data-name="{{ $name }}"
    id="sigta-tab-{{ $name }}"
    aria-controls="sigta-panel-{{ $name }}"
    aria-selected="{{ $name === $default ? 'true' : 'false' }}"
    tabindex="{{ $name === $default ? 0 : -1 }}"
    x-bind:aria-selected="isActive('{{ $name }}') ? 'true' : 'false'"
    x-bind:tabindex="isActive('{{ $name }}') ? 0 : -1"
    x-on:click="select('{{ $name }}')"
    {{ $attributes }}
    data-sigta="tabs.tab"
>{{ $slot }}</button>
