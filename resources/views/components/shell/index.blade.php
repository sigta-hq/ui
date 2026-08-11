{{-- App shell (brand .app): sidebar + main grid. Embeddable frame by default
     (rounded, hairline border); pass `full` for a real full-viewport app.
     Usage:
       <x-sigta::shell>
           <x-slot:sidebar><x-sigta::shell.sidebar>…</x-sigta::shell.sidebar></x-slot:sidebar>
           <x-sigta::shell.main>…</x-sigta::shell.main>
       </x-sigta::shell> --}}
@props([
    'full' => false,
])

@php
$classes = 'grid md:grid-cols-[220px_1fr] bg-surface-raised text-ink ';
$classes .= $full
    ? 'min-h-screen rounded-none border-0'
    : 'min-h-[440px] border border-line rounded-frame overflow-hidden';
@endphp

<div data-sigta="shell" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($sidebar){{ $sidebar }}@endisset
    {{ $slot }}
</div>
