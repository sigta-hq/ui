<x-layouts::app.sidebar :title="$title ?? null">
    <sigta:shell.main>
        {{ $slot }}
    </sigta:shell.main>
</x-layouts::app.sidebar>
