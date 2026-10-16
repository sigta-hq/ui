@props([
    'label' => null,
])

{{-- data-sigta-check draws the tick as two pen strokes (the contract's
     checkbox stylesheet); pressed, the box gives a little and springs back. --}}
@if ($label)
    <label data-sigta="checkbox" {{ $attributes->only('class') }}>
        <input type="checkbox" data-sigta-check {{ $attributes->except('class') }} />
        <span>{{ $label }}</span>
    </label>
@else
    <input data-sigta="checkbox" type="checkbox" data-sigta-check {{ $attributes }} />
@endif
