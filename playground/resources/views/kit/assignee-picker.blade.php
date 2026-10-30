<x-kit-shell title="Assignee picker">
    <x-kit-example label="Unassigned & assigned">
    «div class="flex items-start gap-10">
        «div>
            «sigta:label class="mb-2 block">Unassigned«/sigta:label>
            «sigta:assignee-picker :members="[['id'=>1,'name'=>'Ada Lovelace'],['id'=>2,'name'=>'Alan Turing'],['id'=>3,'name'=>'Grace Hopper']]" />
        «/div>
        «div>
            «sigta:label class="mb-2 block">Assigned, with name«/sigta:label>
            «sigta:assignee-picker :user="['id'=>1,'name'=>'Ada Lovelace']" :members="[['id'=>1,'name'=>'Ada Lovelace'],['id'=>2,'name'=>'Alan Turing'],['id'=>3,'name'=>'Grace Hopper']]" show-name size="sm" />
        «/div>
    «/div>
    «p class="mt-4 max-w-[62ch] font-mono text-[0.7rem] text-graphite">
        Click to open a client-side searchable list. On select it calls
        «code class="text-[0.85em]">method(...args, userId)«/code> on the nearest Livewire component
        (omitted here). Each consumer should re-check membership server-side.
    «/p>
    </x-kit-example>

    <x-kit-example label="Inline positioning (for a native dialog's top layer)">
    «div class="max-w-sm">
        «sigta:assignee-picker inline show-name :members="[['id'=>1,'name'=>'Ada Lovelace'],['id'=>2,'name'=>'Alan Turing']]" />
    «/div>
    «p class="mt-4 max-w-[62ch] font-mono text-[0.7rem] text-graphite">
        Default mode teleports the panel to «code class="text-[0.85em]">body«/code> (escapes card / overflow clipping);
        «code class="text-[0.85em]">:inline«/code> keeps it in place as a top-layer popover, so inside a «code class="text-[0.85em]">&lt;dialog&gt;«/code> it floats over the modal instead of being clipped by its scroll.
    «/p>
    </x-kit-example>
</x-kit-shell>
