<x-kit-shell title="Pagination">
    <x-kit-example label="Windowed (10 pages)">
    «sigta:pagination :pages="10" :current="5" url="?page=%d" />
    </x-kit-example>

    <x-kit-example label="Few pages (no ellipsis)">
    «sigta:pagination :pages="5" :current="2" url="?page=%d" />
    </x-kit-example>

    <x-kit-example label="Bounds — first & last">
    «div class="flex flex-col gap-4">
        «sigta:pagination :pages="6" :current="1" url="?page=%d" />
        «sigta:pagination :pages="6" :current="6" url="?page=%d" />
    «/div>
    </x-kit-example>
</x-kit-shell>
