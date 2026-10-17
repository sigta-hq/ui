{{-- Shell nav link: mono, quiet graphite; `current` inverts to ink. --}}
@props([
    'icon' => null,
    'current' => false,
])

<a
    data-sigta="shell.link"
    @if ($current) aria-current="page" @endif
    {{ $attributes }}
>
    @if ($icon)<x-sigta::icon :name="$icon" size="sm" data-scope="shell.link" data-part="icon" />@endif
    {{ $slot }}
</a>
