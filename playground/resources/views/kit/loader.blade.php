<x-kit-shell title="Loader">
    <x-sigta::text variant="muted" class="max-w-[60ch]">The Sigta compass, in use. While work runs the handle turns, the legs swing round each other and the pencil draws a circle on the paper, then lifts it. When the work lands the compass finishes its turn, the circle closes and its front rises into the mark's arc, then it stamps. Use it wherever a page would otherwise say "Loading…" or "Saving…".</x-sigta::text>

    <x-kit-example label="Sizes — xs · sm · base · lg · xl">
    «div class="flex flex-wrap items-end gap-7">
        «sigta:loader size="xs" />
        «sigta:loader size="sm" />
        «sigta:loader />
        «sigta:loader size="lg" />
        «sigta:loader size="xl" />
    «/div>
    </x-kit-example>

    <x-kit-example label="States">
    «div class="flex flex-wrap items-end gap-9">
        «div class="flex flex-col items-center gap-2">«sigta:loader size="lg" state="queued" />«sigta:text variant="caption">queued«/sigta:text>«/div>
        «div class="flex flex-col items-center gap-2">«sigta:loader size="lg" />«sigta:text variant="caption">working«/sigta:text>«/div>
        «div class="flex flex-col items-center gap-2">«sigta:loader size="lg" state="done" />«sigta:text variant="caption">done«/sigta:text>«/div>
        «div class="flex flex-col items-center gap-2">«sigta:loader size="lg" state="failed" />«sigta:text variant="caption">failed«/sigta:text>«/div>
        «div class="flex flex-col items-center gap-2">«sigta:loader size="lg" state="idle" />«sigta:text variant="caption">idle«/sigta:text>«/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="The landing — replay it">
    «div class="flex items-center gap-6" x-data="{ state: 'working' }">
        «sigta:loader size="xl" x-bind:data-state="state" />
        «div class="flex gap-2">
            «sigta:button size="sm" x-on:click="state = 'working'">Work«/sigta:button>
            «sigta:button size="sm" variant="primary" x-on:click="state = 'done'">Land it«/sigta:button>
            «sigta:button size="sm" variant="ghost" x-on:click="state = 'failed'">Fail«/sigta:button>
        «/div>
    «/div>
    </x-kit-example>

    <x-kit-example label="Progress — the circle is drawn as far round as the value">
    «div class="flex flex-wrap items-center gap-8" x-data="{ p: 8 }" x-init="setInterval(() => p = p >= 100 ? 8 : Math.min(100, p + 13), 1100)">
        «sigta:loader size="xl" :value="8" x-bind:style="'--sigta-progress: ' + p" x-bind:data-state="p >= 100 ? 'done' : 'working'" />
        «sigta:loader size="lg" :value="35" />
        «sigta:loader size="lg" :value="70" />
        «sigta:loader size="lg" :value="100" state="done" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Sketch — the mark draws itself, for longer waits">
    «div class="flex items-end gap-8">
        «sigta:loader variant="sketch" size="lg" />
        «sigta:loader variant="sketch" size="xl" label="Preparing your workspace" />
    «/div>
    </x-kit-example>

    {{-- Your own loader. config('sigta.loader') is app-wide, so the live side
         sets it for this block only and puts it back; the code side shows the
         config line and the real view file. --}}
    @php
        $codeLoaderConfig = <<<'TXT'
        // config/sigta.php   (php artisan vendor:publish --tag=sigta-config)
        'loader' => 'components.spinner',
        TXT;
        $codeSpinner = file_get_contents(resource_path('views/components/spinner.blade.php'));
    @endphp
    <x-kit-demo label="Your own loader — every loader, busy button, save state and job draws it">
        <x-sigta::text variant="muted" class="max-w-[64ch] mb-5">Set <code class="font-mono text-ink">loader</code> in <code class="font-mono text-ink">config/sigta.php</code> to a Blade view and it is drawn inside the loader's box instead of the compass. The box keeps the size, colour, <code class="font-mono text-ink">data-state</code>, progress and accessible name, and hands the view <code class="font-mono text-ink">$state</code>, <code class="font-mono text-ink">$value</code>, <code class="font-mono text-ink">$size</code> and <code class="font-mono text-ink">$label</code>. State can change in the browser without a re-render, so key motion off the box: <code class="font-mono text-ink">group-data-[state=working]/loader:animate-spin</code>. <code class="font-mono text-ink">variant="compass"</code> or <code class="font-mono text-ink">"sketch"</code> still draws the built-in one.</x-sigta::text>
        <x-doc-example label="Live, with 'loader' => 'components.spinner'">
            @php config(['sigta.loader' => 'components.spinner']); @endphp
            <div class="flex flex-wrap items-center gap-6" x-data="{ state: 'working' }">
                <x-sigta::loader size="xl" x-bind:data-state="state" />
                <div class="flex gap-2">
                    <x-sigta::button size="sm" x-on:click="state = 'working'">Work</x-sigta::button>
                    <x-sigta::button size="sm" variant="primary" x-on:click="state = 'done'">Done</x-sigta::button>
                    <x-sigta::button size="sm" variant="ghost" x-on:click="state = 'failed'">Fail</x-sigta::button>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <x-sigta::button variant="primary" loading>Save changes</x-sigta::button>
                <x-sigta::save-state state="saving" />
            </div>
            @php config(['sigta.loader' => null]); @endphp
            <x-slot:code>{{ $codeLoaderConfig }}

{{-- resources/views/components/spinner.blade.php --}}
{{ $codeSpinner }}</x-slot:code>
        </x-doc-example>
    </x-kit-demo>

    <x-kit-example label="In a busy button, once its action outlasts a beat (the label stays)">
    «div class="flex flex-wrap items-center gap-3">
        «sigta:button variant="primary" loading>Save changes«/sigta:button>
        «sigta:button loading>Export CSV«/sigta:button>
    «/div>
    </x-kit-example>

    <x-kit-example label="Beside your own words — the compass holds still while they change">
    «div class="flex max-w-lg items-center justify-between gap-4" x-data="{ state: 'idle', sync() { this.state = 'working'; setTimeout(() => this.state = 'done', 2600) } }">
        «sigta:button size="sm" x-on:click="sync()" x-bind:disabled="state === 'working'">Sync now«/sigta:button>
        «span class="inline-flex items-center gap-2 font-mono text-[0.72rem] text-graphite">
            «span role="status" class="grid justify-items-end text-right">
                «span class="[grid-area:1/1]" x-bind:class="{ invisible: state === 'working' }">Up to date«/span>
                «span class="[grid-area:1/1] invisible" x-bind:class="{ invisible: state !== 'working' }">Syncing with the bank«/span>
            «/span>
            «sigta:loader size="sm" decorative state="idle" x-bind:data-state="state" />
        «/span>
    «/div>
    </x-kit-example>

    <x-kit-example label="On an ink band">
    «div class="flex items-center gap-6 rounded-frame bg-inverse p-6 text-inverse-fg">
        «sigta:loader size="lg" />
        «sigta:loader size="lg" variant="sketch" />
        «sigta:text class="text-inverse-fg-muted">currentColor throughout — it takes the band's ink.«/sigta:text>
    «/div>
    </x-kit-example>
</x-kit-shell>
