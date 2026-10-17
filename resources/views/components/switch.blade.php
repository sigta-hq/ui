@props([
    'label' => null,
])

{{-- A real checkbox (role="switch") hidden behind a drawn track. Pressed, the
     knob squashes wider toward the way it will travel, like a finger on a real
     toggle; it springs to the other side when let go (the contract's switch
     stylesheet). --}}
<label data-sigta="switch" {{ $attributes->only('class') }}>
    <input type="checkbox" role="switch" data-scope="switch" data-part="input" {{ $attributes->except('class') }} />
    <span aria-hidden="true" data-scope="switch" data-part="track"></span>
    @if ($label)
        <span>{{ $label }}</span>
    @endif
</label>
