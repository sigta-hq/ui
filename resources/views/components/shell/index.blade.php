{{-- App shell (brand .app): sidebar + main grid. Embeddable frame by default
     (rounded, hairline border); pass `full` for a real full-viewport app.
     Usage:
       <x-sigta::shell>
           <x-slot:header><x-sigta::shell.header>…</x-sigta::shell.header></x-slot:header>
           <x-slot:sidebar><x-sigta::shell.sidebar>…</x-sigta::shell.sidebar></x-slot:sidebar>
           <x-sigta::shell.main>…</x-sigta::shell.main>
       </x-sigta::shell>
     `header` is optional. With it, the header row spans both columns and takes
     over the sidebar's own brand row and burger. --}}
@props([
    'full' => false,
])

@php
$hasHeader = isset($header);

$classes = 'group/shell grid md:grid-cols-[220px_1fr] bg-surface-raised text-ink ';
// In header mode the phone's sidebar row is empty (its drawer is fixed), so the
// rows are pinned: were they auto, a short page would hand a share of its
// leftover height to the bars instead of the main row.
$classes .= $hasHeader ? 'grid-rows-[auto_auto_1fr] md:grid-rows-[auto_1fr] ' : '';
$classes .= $full
    ? 'min-h-screen rounded-none border-0'
    : 'min-h-[440px] border border-line rounded-frame overflow-hidden';
@endphp

<div data-sigta="shell" @if ($hasHeader) data-header @endif {{ $attributes->merge(['class' => $classes]) }}>
    @if ($hasHeader){{ $header }}@endif
    @isset($sidebar){{ $sidebar }}@endisset
    {{ $slot }}
</div>
