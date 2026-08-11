<x-kit-shell title="Context Menu">
    <x-kit-example label="Right-click the surface">
    «sigta:context-menu>
        «div class="grid place-items-center h-40 bg-surface border-[1.5px] border-dashed border-line rounded-panel font-mono text-[0.76rem] text-graphite select-none">
            Right-click anywhere in here
        «/div>
        «x-slot:menu>
            «sigta:dropdown.item icon="pencil" kbd="⌘R">Rename«/sigta:dropdown.item>
            «sigta:dropdown.item icon="copy">Duplicate«/sigta:dropdown.item>
            «sigta:dropdown.item icon="external-link" href="#top">Open in new tab«/sigta:dropdown.item>
            «sigta:dropdown.separator />
            «sigta:dropdown.item icon="trash-2" variant="danger">Delete«/sigta:dropdown.item>
        «/x-slot:menu>
    «/sigta:context-menu>
    </x-kit-example>

    <x-kit-example label="On a table row">
    «sigta:context-menu>
        «div class="flex items-center justify-between bg-surface border border-line rounded-panel px-4 py-3">
            «span class="font-body text-[0.95rem]">Q2 revenue report.pdf«/span>
            «span class="font-mono text-[0.68rem] text-graphite">2.4 MB«/span>
        «/div>
        «x-slot:menu>
            «sigta:dropdown.item icon="download">Download«/sigta:dropdown.item>
            «sigta:dropdown.item icon="archive">Archive«/sigta:dropdown.item>
        «/x-slot:menu>
    «/sigta:context-menu>
    </x-kit-example>
</x-kit-shell>
