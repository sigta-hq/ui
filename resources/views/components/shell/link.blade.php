{{-- Shell nav link: mono, quiet graphite; `current` inverts to ink. --}}
@props([
    'icon' => null,
    'current' => false,
])

@php
$classes = 'flex items-center gap-[0.55em] font-mono text-[0.76rem] no-underline rounded-[4px] px-[0.65em] py-[0.5em] transition-colors duration-150 ease-out ';
$classes .= $current
    ? 'bg-ink text-surface'
    : 'text-graphite hover:bg-ink/5 hover:text-ink';
@endphp

<a
    data-sigta="shell.link"
    @if ($current) aria-current="page" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if ($icon)<x-sigta::icon :name="$icon" size="sm" class="shrink-0" />@endif
    {{ $slot }}
</a>
