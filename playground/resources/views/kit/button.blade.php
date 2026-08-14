<x-kit-shell title="Button">
    <x-kit-example label="Variants">
    «sigta:button variant="primary">Save draft«/sigta:button>
    «sigta:button>Preview«/sigta:button>
    «sigta:button variant="ghost">Cancel«/sigta:button>
    «sigta:button variant="cta">Book a project «sigta:icon name="arrow-right" size="sm" />«/sigta:button>
    «sigta:button variant="danger">Delete«/sigta:button>
    </x-kit-example>

    <x-kit-example label="States & sizes">
    «sigta:button variant="primary" disabled>Disabled«/sigta:button>
    «sigta:button href="#top" variant="secondary">As link«/sigta:button>
    «sigta:button href="https://example.com" target="_blank" variant="cta">Opens in a new tab«/sigta:button>
    «sigta:button variant="primary" size="sm">Small«/sigta:button>
    </x-kit-example>

    <x-kit-example label="Inverse variants (for dark / ink bands)">
    «div class="rounded-frame bg-inverse p-8 flex flex-wrap items-end gap-4">
        «sigta:button variant="inverse">Book en intro-samtale«/sigta:button>
        «sigta:button variant="ghost-inverse">Skriv en mail«/sigta:button>
    «/div>
    </x-kit-example>
</x-kit-shell>
