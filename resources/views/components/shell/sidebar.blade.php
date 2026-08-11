{{-- Shell sidebar (brand .app-side): nav column on paper; collapses to a
     wrapping top bar on mobile. `brand` slot renders the lockup up top. --}}
<aside
    data-sigta="shell.sidebar"
    {{ $attributes->merge([
        'class' => 'bg-surface border-line px-3.5 py-[18px] gap-1 flex flex-row flex-wrap border-b md:flex-col md:flex-nowrap md:border-b-0 md:border-r',
        'aria-label' => 'App navigation',
    ]) }}
>
    @isset($brand)
        <span class="flex items-center gap-2 w-full mb-4 font-head font-semibold text-[1.05rem] text-ink">{{ $brand }}</span>
    @endisset
    {{ $slot }}
</aside>
