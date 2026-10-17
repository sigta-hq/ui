{{-- Shell main area (brand .app-main): padded column of dashboard rows. The
     bottom padding grows by a visible floating bar's footprint, so the last row
     can still scroll clear of it. --}}
<div data-sigta="shell.main" {{ $attributes }}>
    {{ $slot }}
</div>
