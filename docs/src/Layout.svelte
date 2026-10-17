<script lang="ts">
    /* The docs chrome, rendered on the server only: a sidebar of pages, the
       runtime switch, and the page. No component state lives here. */
    import type { Snippet } from 'svelte';
    import { Heading, Text, Icon } from '@sigta-hq/svelte';

    interface NavGroup { label: string; items: { href: string; title: string; current: boolean }[] }
    interface Props {
        title: string;
        runtime: 'livewire' | 'svelte';
        base: string;
        other: string;
        nav: NavGroup[];
        children: Snippet;
    }

    let { title, runtime, base, other, nav, children }: Props = $props();
</script>

<div class="min-h-dvh md:grid md:grid-cols-[15rem_1fr]">
    <aside class="border-b border-line bg-surface-raised px-5 py-6 md:sticky md:top-0 md:h-dvh md:overflow-y-auto md:border-b-0 md:border-r">
        <a href="{base}{runtime}/" class="font-head text-[1.35rem] font-medium text-ink no-underline">Sigta UI</a>
        <div class="mt-4 inline-flex rounded-base border border-line p-0.5 font-mono text-[0.7rem] uppercase tracking-[0.1em]" role="group" aria-label="Runtime">
            <a href={runtime === 'livewire' ? '#' : other} data-runtime="livewire" aria-current={runtime === 'livewire' ? 'page' : undefined}
               class="rounded-[2px] px-2.5 py-1 no-underline {runtime === 'livewire' ? 'bg-ink text-surface' : 'text-graphite hover:text-ink'}">Livewire</a>
            <a href={runtime === 'svelte' ? '#' : other} data-runtime="svelte" aria-current={runtime === 'svelte' ? 'page' : undefined}
               class="rounded-[2px] px-2.5 py-1 no-underline {runtime === 'svelte' ? 'bg-ink text-surface' : 'text-graphite hover:text-ink'}">Svelte</a>
        </div>
        <nav class="mt-6 flex flex-col gap-5" aria-label="Pages">
            {#each nav as group (group.label)}
                <div>
                    <p class="mb-1.5 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-graphite">{group.label}</p>
                    <ul class="flex flex-col">
                        {#each group.items as item (item.href)}
                            <li>
                                <a href={item.href} aria-current={item.current ? 'page' : undefined}
                                   class="block rounded-base px-2 py-1 font-mono text-[0.76rem] no-underline {item.current ? 'bg-ink/5 text-ink' : 'text-graphite hover:text-ink'}">{item.title}</a>
                            </li>
                        {/each}
                    </ul>
                </div>
            {/each}
        </nav>
    </aside>
    <main class="min-w-0 px-[clamp(20px,5vw,64px)] py-10">
        <div class="mx-auto max-w-4xl">
            <p class="mb-2 font-mono text-[0.68rem] uppercase tracking-[0.16em] text-graphite">Sigta UI · {runtime === 'livewire' ? 'Livewire' : 'Svelte'}</p>
            <Heading level={1} size="xl">{title}</Heading>
            <div class="mt-8">{@render children()}</div>
        </div>
    </main>
</div>
