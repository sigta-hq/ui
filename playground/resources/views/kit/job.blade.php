<x-kit-shell title="Job">
    <x-sigta::text variant="muted" class="max-w-[60ch]">One piece of long-running work: an import, an export, a queue run. The compass draws its circle as far round as the work has come, the stage line says what is happening now, and the moment it turns done the circle rises into the mark's arc with a burst. Its state is plain markup, so a Livewire poll that re-renders it moves it along.</x-sigta::text>

    <x-kit-example label="States">
    «div class="flex max-w-xl flex-col gap-2.5">
        «sigta:job heading="Exporting the Q3 ledger" state="queued" stage="Waiting for a worker" />
        «sigta:job heading="Importing 1,204 contacts" :value="62" stage="Matching duplicates" description="312 duplicates merged so far." />
        «sigta:job heading="Rebuilding the search index" stage="Crawling pages" />
        «sigta:job heading="Sending the October newsletter" state="done" stage="4,210 delivered" />
        «sigta:job heading="Syncing Dinero invoices" state="failed" stage="Step 3 of 4 · API timed out">
            «x-slot:actions>«sigta:button size="sm">Retry«/sigta:button>«/x-slot:actions>
        «/sigta:job>
    «/div>
    </x-kit-example>

    <x-kit-demo label="Livewire — a live queue (wire:poll) in a [data-sigta-motion] list">
        <div class="max-w-xl">
            <livewire:kit.jobs />
        </div>
        <pre class="mt-6 overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.76rem] leading-relaxed text-inverse-fg"><code>&lt;div wire:poll.1s="tick"&gt;
    &lt;div data-sigta-motion class="space-y-2.5"&gt;
        &#64;foreach ($jobs as $job)
            &lt;sigta:job wire:key="job-&#123;&#123; $job->id &#125;&#125;" :heading="$job->name"
                       :state="$job->state" :value="$job->progress" :stage="$job->stage" /&gt;
        &#64;endforeach
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
    </x-kit-demo>
</x-kit-shell>
