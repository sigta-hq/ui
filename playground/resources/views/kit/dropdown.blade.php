<x-kit-shell title="Dropdown">
    <x-kit-example label="Menu">
    «div class="flex gap-6">
        «sigta:dropdown>
            «x-slot:trigger>
                «sigta:button>Options «sigta:icon name="chevron-down" size="sm" />«/sigta:button>
            «/x-slot:trigger>
            «sigta:dropdown.item icon="pencil" kbd="⌘E">Edit«/sigta:dropdown.item>
            «sigta:dropdown.item icon="copy">Duplicate«/sigta:dropdown.item>
            «sigta:dropdown.separator />
            «sigta:dropdown.item icon="trash-2" variant="danger">Delete«/sigta:dropdown.item>
        «/sigta:dropdown>

        «sigta:dropdown position="bottom-end">
            «x-slot:trigger>
                «sigta:button variant="ghost">«sigta:icon name="ellipsis" />«/sigta:button>
            «/x-slot:trigger>
            «sigta:dropdown.item href="#top" icon="external-link">Open link«/sigta:dropdown.item>
            «sigta:dropdown.item icon="archive">Archive«/sigta:dropdown.item>
        «/sigta:dropdown>
    «/div>
    </x-kit-example>
</x-kit-shell>
