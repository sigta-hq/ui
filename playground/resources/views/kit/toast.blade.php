<x-kit-shell title="Toast">
    <x-kit-example label="Trigger toasts (window events)">
    «div class="flex flex-wrap gap-3" x-data>
        «sigta:button x-on:click="$dispatch('sigta-toast', { heading: 'Deploy complete.', text: 'Plumb 2.4 is live for all workspaces.', variant: 'success' })">Success«/sigta:button>
        «sigta:button x-on:click="$dispatch('sigta-toast', { heading: 'Heads up.', text: 'Two seats are unassigned.', variant: 'info' })">Info«/sigta:button>
        «sigta:button variant="danger" x-on:click="$dispatch('sigta-toast', { text: 'Deploy failed — rolled back.', variant: 'danger', duration: 8000 })">Danger«/sigta:button>
    «/div>
    «sigta:text variant="caption" class="mt-4">From Livewire: $this->dispatch('sigta-toast', heading: '…', variant: 'success')«/sigta:text>
    </x-kit-example>

    <x-kit-example label="Toast outlet — include once per page">
    «sigta:toast />
    </x-kit-example>
</x-kit-shell>
