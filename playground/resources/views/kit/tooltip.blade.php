<x-kit-shell title="Tooltip">
    <x-kit-example label="Hover or focus the trigger — 150ms delay, Escape dismisses">
    «div class="flex flex-wrap items-center gap-6 py-8">
        «sigta:tooltip text="Saves a draft, publishes nothing">
            «sigta:button>Save draft«/sigta:button>
        «/sigta:tooltip>
        «sigta:tooltip text="Copied to clipboard">
            «sigta:button variant="ghost">«sigta:icon name="copy" size="sm" /> Copy«/sigta:button>
        «/sigta:tooltip>
    «/div>
    </x-kit-example>

    <x-kit-example label="Positions">
    «div class="flex flex-wrap items-center gap-6 py-10">
        «sigta:tooltip text="Above (default)">
            «sigta:button size="sm">Top«/sigta:button>
        «/sigta:tooltip>
        «sigta:tooltip text="Below" position="bottom">
            «sigta:button size="sm">Bottom«/sigta:button>
        «/sigta:tooltip>
        «sigta:tooltip text="Leading side" position="left">
            «sigta:button size="sm">Left«/sigta:button>
        «/sigta:tooltip>
        «sigta:tooltip text="Trailing side" position="right">
            «sigta:button size="sm">Right«/sigta:button>
        «/sigta:tooltip>
    «/div>
    </x-kit-example>
</x-kit-shell>
