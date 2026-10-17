{{-- Brand .faq item: native <details>/<summary>, "+" rotates 45° when open. No JS. --}}
@aware([
    'name' => null,
])

@props([
    'heading',
    'name' => null,
])

<details
    data-sigta="accordion.item"
    @if ($name) name="{{ $name }}" @endif
    {{ $attributes }}
>
    <summary data-scope="accordion.item" data-part="summary">
        {{ $heading }}
    </summary>
    <div data-scope="accordion.item" data-part="content">
        {{ $slot }}
    </div>
</details>
