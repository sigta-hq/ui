{{-- Shell main area (brand .app-main): padded column of dashboard rows. The
     bottom padding grows by a visible floating bar's footprint, so the last row
     can still scroll clear of it. --}}
<div data-sigta="shell.main" {{ $attributes->merge(['class' => 'p-6 pb-[calc(1.5rem+var(--sigta-floating-space,0px))] flex flex-col gap-5 min-w-0']) }}>
    {{ $slot }}
</div>
