<x-kit-shell title="Breadcrumbs">
    <x-kit-example label="Trail — last item is the current page">
    «sigta:breadcrumbs>
        «sigta:breadcrumbs.item href="#home">Home«/sigta:breadcrumbs.item>
        «sigta:breadcrumbs.item href="#projects">Projects«/sigta:breadcrumbs.item>
        «sigta:breadcrumbs.item>Marginalia v3«/sigta:breadcrumbs.item>
    «/sigta:breadcrumbs>
    </x-kit-example>

    <x-kit-example label="Two levels">
    «sigta:breadcrumbs>
        «sigta:breadcrumbs.item href="#studio">Studio«/sigta:breadcrumbs.item>
        «sigta:breadcrumbs.item>Practices«/sigta:breadcrumbs.item>
    «/sigta:breadcrumbs>
    </x-kit-example>

    <x-kit-example label="Explicit current prop (href kept for context, rendered as text)">
    «sigta:breadcrumbs>
        «sigta:breadcrumbs.item href="#home">Home«/sigta:breadcrumbs.item>
        «sigta:breadcrumbs.item href="#settings" current>Settings«/sigta:breadcrumbs.item>
    «/sigta:breadcrumbs>
    </x-kit-example>
</x-kit-shell>
