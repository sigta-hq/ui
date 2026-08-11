{{-- Shell main area (brand .app-main): padded column of dashboard rows. --}}
<div data-sigta="shell.main" {{ $attributes->merge(['class' => 'p-6 flex flex-col gap-5 min-w-0']) }}>
    {{ $slot }}
</div>
