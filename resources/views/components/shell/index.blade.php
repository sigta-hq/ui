{{-- App shell (brand .app): sidebar + main grid. Embeddable frame by default
     (rounded, hairline border); pass `full` for a real full-viewport app.
     Usage:
       <x-sigta::shell>
           <x-slot:header><x-sigta::shell.header>…</x-sigta::shell.header></x-slot:header>
           <x-slot:sidebar><x-sigta::shell.sidebar>…</x-sigta::shell.sidebar></x-slot:sidebar>
           <x-sigta::shell.main>…</x-sigta::shell.main>
       </x-sigta::shell>
     `header` is optional. With it, the header row spans both columns and takes
     over the sidebar's own brand row and burger. The look is the contract's
     (packages/contract/css/components/shell.css). --}}
@props([
    'full' => false,
])

<div data-sigta="shell" @isset($header) data-header @endisset @if ($full) data-full @endif {{ $attributes }}>
    @isset($header){{ $header }}@endisset
    @isset($sidebar){{ $sidebar }}@endisset
    {{ $slot }}
</div>
