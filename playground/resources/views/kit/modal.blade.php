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

    <x-kit-example label="Sizes">
    «div class="flex gap-3">
        «sigta:modal.trigger name="small">«sigta:button size="sm">Small«/sigta:button>«/sigta:modal.trigger>
        «sigta:modal.trigger name="large">«sigta:button size="sm">Large«/sigta:button>«/sigta:modal.trigger>
    «/div>
    «sigta:modal name="small" size="sm" heading="Small modal">«sigta:text>Compact confirmation.«/sigta:text>«/sigta:modal>
    «sigta:modal name="large" size="lg" heading="Large modal">«sigta:text>Room for a whole form.«/sigta:text>«/sigta:modal>
    </x-kit-example>
</x-kit-shell>
