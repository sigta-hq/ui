{{-- Breadcrumb trail: nav > ol, "/" separators drawn by the items. --}}
<nav data-sigta="breadcrumbs" {{ $attributes->merge(['aria-label' => 'Breadcrumb']) }}>
    <ol class="flex flex-wrap items-center gap-x-[0.6em] gap-y-1 list-none m-0 p-0 font-mono text-[0.74rem] tracking-[0.05em]">
        {{ $slot }}
    </ol>
</nav>
