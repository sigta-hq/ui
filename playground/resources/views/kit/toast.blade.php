<x-kit-shell title="Toast">
    <x-kit-example label="Trigger toasts (Sigta API)">
    «div class="flex flex-wrap gap-3" x-data>
        «sigta:button x-on:click="$sigta.toast({ heading: 'Deploy complete.', text: 'Plumb 2.4 is live for all workspaces.', variant: 'success' })">Success«/sigta:button>
        «sigta:button x-on:click="$sigta.toast({ heading: 'Heads up.', text: 'Two seats are unassigned.', variant: 'warning' })">Warning«/sigta:button>
        «sigta:button x-on:click="$sigta.toast({ heading: 'Note.', text: 'Background sync is enabled.', variant: 'info' })">Info«/sigta:button>
        «sigta:button variant="danger" x-on:click="$sigta.toast({ text: 'Deploy failed — rolled back.', variant: 'danger', duration: 8000 })">Danger«/sigta:button>
        «sigta:button x-on:click="$sigta.toast({ text: 'Invoice created.', variant: 'success', link: { text: 'View invoice', href: '/kit/toast' } })">With link«/sigta:button>
    «/div>
    «sigta:text variant="caption" class="mt-4">From Livewire: Sigta::toast('Saved') or Sigta::toast(heading: '…', variant: 'success')«/sigta:text>
    </x-kit-example>

    <x-kit-example label="Toast outlet — include once per page">
    «sigta:text variant="muted" class="mb-3">With wire:navigate, wrap in @persist('toast') so toasts survive page transitions.«/sigta:text>
    «sigta:toast />
    </x-kit-example>
</x-kit-shell>
