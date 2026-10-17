{{-- Breadcrumb trail: nav > ol, "/" separators drawn by the items. --}}
<nav data-sigta="breadcrumbs" {{ $attributes->merge(['aria-label' => 'Breadcrumb']) }}>
    <ol data-scope="breadcrumbs" data-part="list">
        {{ $slot }}
    </ol>
</nav>
