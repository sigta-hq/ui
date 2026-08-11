@props([
    'name',
])

<button
    type="button"
    role="tab"
    data-name="{{ $name }}"
    id="sigta-tab-{{ $name }}"
    aria-controls="sigta-panel-{{ $name }}"
    x-bind:aria-selected="isActive('{{ $name }}') ? 'true' : 'false'"
    x-bind:tabindex="isActive('{{ $name }}') ? 0 : -1"
    x-on:click="select('{{ $name }}')"
    x-bind:class="isActive('{{ $name }}')
        ? 'text-ink border-ink'
        : 'text-graphite border-transparent hover:text-ink'"
    {{ $attributes->merge(['class' => 'font-mono text-[0.78rem] tracking-[0.04em] px-3.5 py-2.5 -mb-px border-b-[1.5px] cursor-pointer transition-colors']) }}
    data-sigta="tabs.tab"
>{{ $slot }}</button>
