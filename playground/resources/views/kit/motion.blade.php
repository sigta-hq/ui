<x-kit-shell title="Motion">
    <x-sigta::text variant="lede" class="max-w-[52ch]">Ink on paper. Strokes draw, paper settles under a press, and a finished thing is crossed out and folds away.</x-sigta::text>
    <x-sigta::text variant="muted" class="max-w-[62ch]">Motion here always decorates state the page already carries: the text, the ARIA, the data attributes. With prefers-reduced-motion set, all of it stops and nothing is lost. Timings come from the --sigta-curve-* and --sigta-dur-* theme tokens.</x-sigta::text>

    <x-kit-demo label="Which moment gets which move">
        <div class="grid gap-x-8 gap-y-2.5 font-mono text-[0.74rem] sm:grid-cols-[auto_1fr]">
            <span class="text-graphite">Saving</span><span>&lt;sigta:save-state&gt; — the compass instead of "Saving…"</span>
            <span class="text-graphite">Background work</span><span>&lt;sigta:job&gt;, or &lt;sigta:loader :value&gt; on its own</span>
            <span class="text-graphite">A pressed control</span><span>built in: buttons settle, ticks draw, switches squash</span>
            <span class="text-graphite">Finishing an item</span><span>$sigta.complete(el, commit) in a [data-sigta-motion] list</span>
            <span class="text-graphite">Something arrives</span><span>[data-sigta-motion] unfolds new rows with a highlighter wash</span>
            <span class="text-graphite">A number changes</span><span>&lt;sigta:count&gt; bumps up or down</span>
            <span class="text-graphite">A milestone</span><span>$sigta.celebrate(el, 'big') — rarely, and it means it</span>
            <span class="text-graphite">Content loads in</span><span>animate-sigta-settle, staggered with animation-delay</span>
        </div>
    </x-kit-demo>

    <x-kit-example label="Press — lift on hover, settle on press, spring back">
    «div class="flex flex-wrap items-center gap-4">
        «sigta:button variant="primary">Press and hold«/sigta:button>
        «sigta:button>Secondary«/sigta:button>
        «sigta:button variant="ghost">Ghost«/sigta:button>
        «sigta:checkbox label="The tick is drawn" />
        «sigta:switch label="The knob squashes" />
    «/div>
    </x-kit-example>

    <x-kit-example label="Complete — tick, burst, cross out, fold away (Alpine; the Inbox page shows Livewire)">
    «div class="max-w-md" x-data="{ items: [{ id: 1, title: 'Send the Almanac invoice' }, { id: 2, title: 'Review the Plumb pricing page' }, { id: 3, title: 'Water the studio plants' }], next: 4 }">
        «div class="mb-3 flex items-baseline justify-between">
            «sigta:eyebrow>To do«/sigta:eyebrow>
            «span class="font-mono text-[0.68rem] uppercase tracking-[0.14em] text-graphite">«sigta:count x-text="items.length">3«/sigta:count> left«/span>
        «/div>
        «ul data-sigta-motion class="divide-y divide-line rounded-panel border border-line bg-surface">
            «template x-for="item in items" :key="item.id">
                «li class="flex items-center gap-3 px-4 py-3">
                    «sigta:checkbox x-bind:aria-label="'Done: ' + item.title" x-on:change="$sigta.complete($el, () => items = items.filter((i) => i.id !== item.id))" />
                    «span data-sigta-strike x-text="item.title">«/span>
                «/li>
            «/template>
        «/ul>
        «sigta:button size="sm" class="mt-3" x-on:click="items.push({ id: next, title: 'Another thing, number ' + next++ })">Add one«/sigta:button>
    «/div>
    </x-kit-example>

    <x-kit-example label="Complete with a stamp — { stamp: 'Done' }">
    «div class="max-w-md" x-data="{ items: [{ id: 1, title: 'Approve the Q3 invoice run' }, { id: 2, title: 'Ship the Marginalia brand refresh' }] }">
        «ul data-sigta-motion class="divide-y divide-line rounded-panel border border-line bg-surface">
            «template x-for="item in items" :key="item.id">
                «li class="flex items-center gap-3 px-4 py-3">
                    «sigta:button size="sm" x-on:click="$sigta.complete($el, () => items = items.filter((i) => i.id !== item.id), { stamp: 'Done' })">Approve«/sigta:button>
                    «span data-sigta-strike x-text="item.title">«/span>
                «/li>
            «/template>
        «/ul>
    «/div>
    </x-kit-example>

    <x-kit-example label="Count — rises as it grows, drops as it shrinks">
    «div class="flex items-center gap-4" x-data="{ n: 12 }">
        «sigta:button size="sm" x-on:click="n--" aria-label="One less">−«/sigta:button>
        «span class="font-head text-[2rem] leading-none">«sigta:count x-text="n">12«/sigta:count>«/span>
        «sigta:button size="sm" x-on:click="n++" aria-label="One more">+«/sigta:button>
    «/div>
    </x-kit-example>

    <x-kit-example label="Celebrate — small, medium, big">
    «div class="flex flex-wrap items-center gap-3">
        «sigta:button x-on:click="$sigta.celebrate($el, 'small')">Small: a burst«/sigta:button>
        «sigta:button x-on:click="$sigta.celebrate($el, 'medium', { text: 'nice!' })">Medium: ring and note«/sigta:button>
        «sigta:button variant="primary" x-on:click="$sigta.celebrate($el, 'big', { text: 'well done!' })">Big: paper confetti«/sigta:button>
    «/div>
    «sigta:text variant="caption" class="mt-4">From Livewire: Sigta::celebrate('big', text: 'Inbox zero!', target: '#inbox')«/sigta:text>
    </x-kit-example>

    <x-kit-example label="Settle — content lands like ink soaking into paper">
    «div x-data="{ shown: true }">
        «sigta:button size="sm" x-on:click="shown = false; $nextTick(() => shown = true)">Load again«/sigta:button>
        «template x-if="shown">
            «div class="mt-4 grid gap-2.5 sm:grid-cols-3">
                «sigta:stat class="animate-sigta-settle" label="Active accounts" value="2,418" />
                «sigta:stat class="animate-sigta-settle [animation-delay:70ms]" label="Net retention" value="112%" />
                «sigta:stat class="animate-sigta-settle [animation-delay:140ms]" label="Time to value" value="3.1d" />
            «/div>
        «/template>
    «/div>
    </x-kit-example>

    <x-sigta::toast />
</x-kit-shell>
