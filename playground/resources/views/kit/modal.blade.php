<x-kit-shell title="Modal">
    <x-kit-example label="Named modal with trigger">
    «sigta:modal.trigger name="demo">
        «sigta:button variant="primary">Open modal«/sigta:button>
    «/sigta:modal.trigger>

    «sigta:modal name="demo" heading="Have something worth drawing twice?">
        «sigta:text variant="muted">Tell us what you're building. We take on a handful of projects each quarter.«/sigta:text>
        «div class="flex gap-3 mt-6">
            «sigta:button variant="primary">Confirm«/sigta:button>
            «sigta:modal.close name="demo">«sigta:button variant="ghost">Cancel«/sigta:button>«/sigta:modal.close>
        «/div>
    «/sigta:modal>
    </x-kit-example>

    <x-kit-example label="No heading (close button floats, no header band)">
    «sigta:modal.trigger name="headless">
        «sigta:button variant="secondary">Open headless«/sigta:button>
    «/sigta:modal.trigger>

    «sigta:modal name="headless">
        «sigta:heading level="3">Content starts at the top«/sigta:heading>
        «sigta:text variant="muted" class="mt-2">With no heading the close button floats in the corner instead of reserving an empty header row, so nothing is wasted above the content.«/sigta:text>
    «/sigta:modal>
    </x-kit-example>

    <x-kit-example label="Full screen on a phone (lg and xl by default)">
    «div class="flex gap-3">
        «sigta:modal.trigger name="workspace">«sigta:button size="sm">Workspace (lg)«/sigta:button>«/sigta:modal.trigger>
        «sigta:modal.trigger name="confirm-small">«sigta:button size="sm">Confirmation (base)«/sigta:button>«/sigta:modal.trigger>
        «sigta:modal.trigger name="forced">«sigta:button size="sm">Small, forced«/sigta:button>«/sigta:modal.trigger>
        «sigta:modal.trigger name="kept">«sigta:button size="sm">Large, kept centred«/sigta:button>«/sigta:modal.trigger>
    «/div>

    «sigta:modal name="workspace" size="lg" heading="Workspace">
        «sigta:text variant="muted">Narrow the window under 768px: this one goes edge to edge, because a modal carrying real work wants the screen a phone has.«/sigta:text>
    «/sigta:modal>

    «sigta:modal name="confirm-small" heading="Delete this?">
        «sigta:text variant="muted">A confirmation stays a centred card at every width. Taking the whole screen would make a light act feel heavy.«/sigta:text>
    «/sigta:modal>

    «sigta:modal name="forced" size="sm" fullscreen heading="Small, full screen">
        «sigta:text variant="muted">fullscreen overrides the size, for a small modal that still needs the room (a step by step flow).«/sigta:text>
    «/sigta:modal>

    «sigta:modal name="kept" size="lg" :fullscreen="false" heading="Large, centred">
        «sigta:text variant="muted">:fullscreen="false" keeps a large modal a sheet on a phone, for content that reads better framed.«/sigta:text>
    «/sigta:modal>
    </x-kit-example>

    <x-kit-example label="Full screen body that fills the screen">
    «sigta:modal.trigger name="filling">
        «sigta:button variant="secondary">Open filling modal«/sigta:button>
    «/sigta:modal.trigger>

    «sigta:modal name="filling" size="xl" heading="Fills the screen">
        «div class="flex-1 mt-4 rounded-panel border border-line grid place-items-center">
            «sigta:text variant="muted">A panel marked flex-1 fills a full screen modal exactly; anything taller just scrolls.«/sigta:text>
        «/div>
    «/sigta:modal>
    </x-kit-example>

    <x-kit-example label="Sizes">
    «div class="flex gap-3">
        «sigta:modal.trigger name="small">«sigta:button size="sm">Small«/sigta:button>«/sigta:modal.trigger>
        «sigta:modal.trigger name="large">«sigta:button size="sm">Large«/sigta:button>«/sigta:modal.trigger>
    «/div>
    «sigta:modal name="small" size="sm" heading="Small modal">«sigta:text>Compact confirmation.«/sigta:text>«/sigta:modal>
    «sigta:modal name="large" size="lg" heading="Large modal">«sigta:text>Room for a whole form.«/sigta:text>«/sigta:modal>
    </x-kit-example>
</x-kit-shell>
