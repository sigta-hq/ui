@props([
    'label' => null,
])

{{-- A round box that fills with ink and keeps a paper dot. With a label it
     is a mono label row and every other attribute lands on the <input>. --}}
@if ($label)
    <label data-sigta="radio" {{ $attributes->only('class') }}>
        <input type="radio" data-scope="radio" data-part="control" {{ $attributes->except('class') }} />
        <span>{{ $label }}</span>
    </label>
@else
    <input data-sigta="radio" type="radio" {{ $attributes }} />
@endif
