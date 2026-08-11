<x-kit-shell title="Popover">
    <x-kit-example label="Free-form panel">
    «sigta:popover>
        «x-slot:trigger>
            «sigta:button>Share «sigta:icon name="chevron-down" size="sm" />«/sigta:button>
        «/x-slot:trigger>
        «sigta:heading level="3" size="sm">Share this draft«/sigta:heading>
        «sigta:text variant="muted" class="text-[0.9rem] mt-1.5">Anyone with the link can read, nobody can edit.«/sigta:text>
        «div class="mt-3 flex gap-2">
            «sigta:button variant="primary" size="sm">Copy link«/sigta:button>
            «sigta:button variant="ghost" size="sm">Settings«/sigta:button>
        «/div>
    «/sigta:popover>
    </x-kit-example>
</x-kit-shell>
