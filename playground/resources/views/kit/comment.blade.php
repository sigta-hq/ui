<x-kit-shell title="Comment">
    <x-kit-example label="Comment feed with owner actions">
    «div class="max-w-xl">
        «sigta:comment author="Ada Lovelace" meta="2h ago" avatar="Ada Lovelace">
            Shipping the analytical-engine notes today — the append-only ledger idea holds up well.
        «/sigta:comment>
        «sigta:comment author="Alan Turing" meta="1h ago" avatar="Alan Turing">
            Agreed. Let's add a decidability test before we merge.
            «x-slot:actions>
                «sigta:button variant="ghost" size="sm" aria-label="Edit note">«sigta:icon name="pencil" size="sm" />«/sigta:button>
                «sigta:button variant="ghost" size="sm" aria-label="Delete note">«sigta:icon name="trash-2" size="sm" />«/sigta:button>
            «/x-slot:actions>
        «/sigta:comment>
    «/div>
    «p class="mt-4 max-w-[62ch] font-mono text-[0.7rem] text-graphite">
        Presentational only — persistence, validation and who-may-edit auth stay in your Livewire component.
    «/p>
    </x-kit-example>

    <x-kit-example label="Editing state (inline editor slot)">
    «div class="max-w-xl">
        «sigta:comment :editing="true" author="Grace Hopper" meta="just now" avatar="Grace Hopper">
            The original body text.
            «x-slot:edit>
                «sigta:textarea rows="3">The original body text.«/sigta:textarea>
                «div class="mt-2 flex gap-2">
                    «sigta:button variant="primary" size="sm">Save«/sigta:button>
                    «sigta:button variant="ghost" size="sm">Cancel«/sigta:button>
                «/div>
            «/x-slot:edit>
        «/sigta:comment>
    «/div>
    </x-kit-example>

    <x-kit-example label="Compose a new note">
    «div class="max-w-xl">
        «sigta:composer placeholder="Add a note…" />
    «/div>
    </x-kit-example>
</x-kit-shell>
