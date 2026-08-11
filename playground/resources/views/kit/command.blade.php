<x-kit-shell title="Command">
    <x-kit-example label="Palette — press ⌘K / Ctrl-K, or:">
    «sigta:modal.trigger name="command">
        «sigta:button>Open command palette «kbd class="font-mono text-[0.62rem] opacity-60">⌘K«/kbd>«/sigta:button>
    «/sigta:modal.trigger>

    «sigta:command>
        «sigta:command.item icon="file-plus" kbd="⌘N">New project«/sigta:command.item>
        «sigta:command.item icon="pencil" kbd="⌘E">Edit current draft«/sigta:command.item>
        «sigta:command.item icon="chart-line">Open Plumb dashboard«/sigta:command.item>
        «sigta:command.item href="#top" icon="settings">Settings«/sigta:command.item>
        «sigta:command.item icon="archive">Archive workspace«/sigta:command.item>
    «/sigta:command>
    </x-kit-example>
</x-kit-shell>
