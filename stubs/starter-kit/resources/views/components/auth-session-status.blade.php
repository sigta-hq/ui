@props([
    'status',
])

@if ($status)
    <sigta:callout variant="success" {{ $attributes }}>
        {{ $status }}
    </sigta:callout>
@endif
