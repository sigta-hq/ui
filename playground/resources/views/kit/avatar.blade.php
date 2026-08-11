<x-kit-shell title="Avatar & Profile">
    <x-kit-example label="Sizes — initials fallback">
    «div class="flex flex-wrap items-center gap-4">
        «sigta:avatar name="Søren Frisk" size="xs" />
        «sigta:avatar name="Søren Frisk" size="sm" />
        «sigta:avatar name="Søren Frisk" />
        «sigta:avatar name="Søren Frisk" size="lg" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Shape & tone">
    «div class="flex flex-wrap items-center gap-4">
        «sigta:avatar name="Marginalia Team" />
        «sigta:avatar name="Marginalia Team" tone="line" />
        «sigta:avatar name="Marginalia Team" shape="square" />
        «sigta:avatar name="Marginalia Team" shape="square" tone="line" />
        «sigta:avatar name="Plumb" />
    «/div>
    </x-kit-example>

    <x-kit-example label="With image">
    «div class="flex flex-wrap items-center gap-4">
        «sigta:avatar
            src="data:image/svg+xml;utf8,«svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'>«rect width='80' height='80' fill='gainsboro'/>«circle cx='40' cy='31' r='14' fill='dimgray'/>«path d='M12 76c4-17 16-25 28-25s24 8 28 25z' fill='dimgray'/>«/svg>"
            name="Søren Frisk" />
        «sigta:avatar
            src="data:image/svg+xml;utf8,«svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'>«rect width='80' height='80' fill='gainsboro'/>«circle cx='40' cy='31' r='14' fill='dimgray'/>«path d='M12 76c4-17 16-25 28-25s24 8 28 25z' fill='dimgray'/>«/svg>"
            name="Søren Frisk" shape="square" size="lg" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Profile — name + meta">
    «div class="flex flex-col gap-5">
        «sigta:profile name="Søren Frisk" meta="Studio lead · Copenhagen" />
        «sigta:profile
            name="Ada Marsh"
            meta="Engineering · Halyard OS"
            src="data:image/svg+xml;utf8,«svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'>«rect width='80' height='80' fill='gainsboro'/>«circle cx='40' cy='31' r='14' fill='dimgray'/>«path d='M12 76c4-17 16-25 28-25s24 8 28 25z' fill='dimgray'/>«/svg>" />
        «div class="flex justify-end">
            «sigta:profile name="Nina Vale" meta="Design · Marginalia" reverse />
        «/div>
    «/div>
    </x-kit-example>
</x-kit-shell>
