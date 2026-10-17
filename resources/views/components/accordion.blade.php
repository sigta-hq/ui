{{-- Brand .faq group. `name` forwards to every item's <details> for native exclusive-open. --}}
@props([
    'name' => null,
])

<div data-sigta="accordion" {{ $attributes }}>
    {{ $slot }}
</div>
