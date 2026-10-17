<script lang="ts">
    /* One kit page: each component's API in the current runtime's names, and
       its examples — live demo above, source below. */
    import { Heading, Text } from '@sigta-hq/svelte';

    interface Prop { name: string; type: string; default: string; description: string }
    interface Example { id: string; label: string; html: string; source: string; lang: string; frame?: string }
    interface Component {
        name: string;
        description: string;
        props: Prop[];
        slots: { name: string; description: string }[];
        events: { name: string; description: string }[];
        notes: string[];
        examples: Example[];
    }

    let { components }: { components: Component[] } = $props();
</script>

{#each components as component (component.name)}
    <section class="mb-14" id={component.name}>
        <Heading level={2} size="lg"><code class="font-mono text-[0.8em]">{component.name}</code></Heading>
        <Text variant="lede" class="mt-2 max-w-[62ch]">{component.description}</Text>

        {#each component.examples as example (example.id)}
            <div class="mt-6 overflow-hidden rounded-panel border border-line bg-surface-raised">
                <p class="border-b border-line px-4 py-2 font-mono text-[0.66rem] uppercase tracking-[0.14em] text-graphite">{example.label}</p>
                <div class="p-6 {example.frame === 'inverse' ? '' : ''}" data-example={example.id}>{@html example.html}</div>
                <pre class="m-0 overflow-x-auto border-t border-line bg-inverse p-4 font-mono text-[0.76rem] leading-relaxed text-inverse-fg"><code>{example.source}</code></pre>
            </div>
        {/each}

        {#if component.props.length}
            <div class="mt-6 overflow-x-auto">
                <table class="w-full border-collapse text-left text-[0.9rem]">
                    <thead>
                        <tr class="border-b border-line font-mono text-[0.66rem] uppercase tracking-[0.12em] text-graphite">
                            <th class="py-2 pr-4 font-normal">Prop</th><th class="py-2 pr-4 font-normal">Type</th><th class="py-2 pr-4 font-normal">Default</th><th class="py-2 font-normal">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each component.props as prop (prop.name)}
                            <tr class="border-b border-line align-top">
                                <td class="py-2 pr-4 font-mono text-[0.78rem] text-ink">{prop.name}</td>
                                <td class="py-2 pr-4 font-mono text-[0.72rem] text-graphite">{prop.type}</td>
                                <td class="py-2 pr-4 font-mono text-[0.72rem] text-graphite">{prop.default}</td>
                                <td class="py-2 text-graphite">{prop.description}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {/if}

        {#if component.slots.length || component.events.length || component.notes.length}
            <ul class="mt-4 flex list-disc flex-col gap-1.5 pl-5 text-[0.92rem] text-graphite">
                {#each component.slots as slot (slot.name)}<li><code class="font-mono text-[0.8rem] text-ink">{slot.name}</code> — {slot.description}</li>{/each}
                {#each component.events as event (event.name)}<li><code class="font-mono text-[0.8rem] text-ink">{event.name}</code> — {event.description}</li>{/each}
                {#each component.notes as note, i (i)}<li>{note}</li>{/each}
            </ul>
        {/if}
    </section>
{/each}
