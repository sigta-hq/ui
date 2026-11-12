{{-- An app's own loader, as config('sigta.loader') would name it. It is drawn
     inside <sigta:loader>'s box, which owns size, colour, data-state and the
     accessible name, so it keys its motion off the box: it turns while the
     work runs and shows a tick once it is done. --}}
@props(['state' => 'working', 'value' => null, 'size' => 'base', 'label' => null])

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="group-data-[state=working]/loader:animate-spin">
    <circle cx="12" cy="12" r="9" class="opacity-20" />
    <path d="M21 12a9 9 0 0 0-9-9" class="group-data-[state=done]/loader:hidden group-data-[state=idle]/loader:hidden" />
    <path d="m8 12.5 2.7 2.7L16 9.5" class="hidden group-data-[state=done]/loader:inline" />
</svg>
