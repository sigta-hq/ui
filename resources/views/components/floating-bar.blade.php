{{-- Floating bar: a dark pill pinned to the bottom centre of the viewport that
     slides up when `show` turns true and away when it turns false. For state that
     lasts while the member works (a running timer, a bulk selection), shown over
     the page instead of taking a band of it.
     While it is up it publishes its footprint as --sigta-floating-space on <html>,
     which shell.main and the toast region add to their bottom edge, so nothing
     ends up stuck under it. --}}
@props([
    'show' => false,
    'label' => null,
])

<div
    data-sigta="floating-bar"
    data-show="{{ $show ? 'true' : 'false' }}"
    x-data="sigtaFloatingBar"
    {{ $attributes }}
>
    {{-- Alpine owns this element's display from here on. `wire:ignore.self` keeps a
         Livewire morph from writing the server's display back over it, which would
         cut the leave transition short; the children still update. A first render
         that is already up shows without animating, since x-show only transitions
         on a change. The transition hooks are styled in the contract stylesheet. --}}
    <div
        x-ref="bar"
        x-show="visible"
        wire:ignore.self
        @unless ($show) style="display: none" @endunless
        @if ($label) role="region" aria-label="{{ $label }}" @endif
        x-transition:enter="sigta-floating-bar-enter"
        x-transition:enter-start="sigta-floating-bar-away"
        x-transition:leave="sigta-floating-bar-leave"
        x-transition:leave-end="sigta-floating-bar-away"
        data-scope="floating-bar"
        data-part="bar"
    >
        {{ $slot }}
    </div>
</div>
