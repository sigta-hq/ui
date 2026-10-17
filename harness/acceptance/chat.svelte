<!-- The Sigta Chat page in Svelte: the twin of chat.blade.php, its mock in Svelte state. -->
<script>
    import { tick } from 'svelte';
    import { Avatar, Badge, Brand, Button, Composer, Dropdown, Icon, Shell } from '@sigta-hq/svelte';

    let model = $state('Sigta Opus');
    const models = ['Sigta Opus', 'Sigta Sonnet', 'Sigta Haiku'];
    const greetName = 'Søren';
    let pending = $state(false);
    let messages = $state([]);
    const conversations = [
        { id: 1, title: 'Rebrand the pricing section', group: 'Today' },
        { id: 2, title: 'Explain the token layer', group: 'Today' },
        { id: 3, title: 'Draft the Q3 changelog', group: 'Yesterday' },
        { id: 4, title: 'Kanban drag-and-drop approach', group: 'Yesterday' },
        { id: 5, title: 'Whimsical error copy ideas', group: 'Previous 7 days' },
    ];
    let activeId = $state(null);
    let root = $state();
    let thread = $state();

    const grouped = conversations.reduce((acc, c) => {
        (acc[c.group] ??= []).push(c);
        return acc;
    }, {});

    async function newChat() {
        messages = [];
        activeId = null;
        await tick();
        root?.querySelector('[data-sigta=composer] textarea')?.focus();
    }

    function send(text) {
        const value = (text || '').trim();
        if (!value || pending) return;
        messages.push({ role: 'user', content: value });
        pending = true;
        scrollDown();
        setTimeout(() => {
            messages.push({ role: 'assistant', content: reply(value) });
            pending = false;
            scrollDown();
        }, 900);
    }

    function reply(prompt) {
        return 'Here\'s a first pass at “' + prompt + '”.\n\n'
            + 'This is a Sigta UI demo surface, so the response is canned — but the '
            + 'chrome is real: the thread, the model picker, the composer, and this '
            + 'prose block all come from the component library. Swap the mock for a '
            + 'Livewire action and it is a working chat.';
    }

    async function scrollDown() {
        await tick();
        thread?.scrollTo({ top: thread.scrollHeight, behavior: 'smooth' });
    }

    const suggestions = [
        'Draft a launch note for Plumb 2.4',
        'Explain our token theming to a new dev',
        'Rewrite this error message to be kinder',
        'Outline a pricing page for a SaaS',
    ];
</script>

<div class="h-screen" bind:this={root}>
    <Shell full>
        {#snippet sidebar()}
            <aside class="flex h-screen flex-col bg-surface border-r border-line px-3 py-4" aria-label="Conversations">
                <div class="flex items-center justify-between px-1">
                    <Brand name="Sigta" size="sm" />
                    <Button variant="ghost" size="sm" aria-label="New chat" onclick={newChat}>
                        <Icon name="square-pen" size="sm" />
                    </Button>
                </div>

                <div class="mt-4">
                    <Button variant="primary" class="w-full justify-center" onclick={newChat}>
                        <Icon name="plus" size="sm" /> New chat
                    </Button>
                </div>

                <nav class="mt-6 flex-1 overflow-y-auto -mx-1 px-1" aria-label="History">
                    {#each Object.entries(grouped) as [group, items] (group)}
                        <div class="mb-4">
                            <p class="px-2 mb-1.5 font-mono text-[0.6rem] tracking-[0.14em] uppercase text-graphite">{group}</p>
                            {#each items as c (c.id)}
                                <button
                                    type="button"
                                    onclick={() => (activeId = c.id)}
                                    class="{activeId === c.id ? 'bg-ink text-surface' : 'text-graphite hover:text-ink hover:bg-ink/5'} w-full text-left truncate rounded-base px-2 py-1.5 font-mono text-[0.76rem] cursor-pointer"
                                >{c.title}</button>
                            {/each}
                        </div>
                    {/each}
                </nav>

                <div class="mt-2 border-t border-line pt-3">
                    <Dropdown position="top-start" class="w-full">
                        {#snippet trigger()}
                            <button type="button" class="flex w-full items-center gap-2.5 rounded-base px-2 py-1.5 text-left hover:bg-ink/5 cursor-pointer">
                                <Avatar name="Søren Frisk" size="sm" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-[0.85rem] text-ink">Søren Frisk</span>
                                    <span class="block truncate font-mono text-[0.62rem] text-graphite">Studio · Pro</span>
                                </span>
                                <Icon name="chevrons-up-down" size="xs" class="text-graphite shrink-0" />
                            </button>
                        {/snippet}
                        <Dropdown.Item icon="settings">Settings</Dropdown.Item>
                        <Dropdown.Item icon="credit-card">Billing</Dropdown.Item>
                        <Dropdown.Separator />
                        <Dropdown.Item icon="log-out" variant="danger">Sign out</Dropdown.Item>
                    </Dropdown>
                </div>
            </aside>
        {/snippet}

        <div class="flex h-screen flex-col">
            <header class="flex items-center justify-between gap-4 border-b border-line px-5 md:px-8 py-3">
                <Dropdown>
                    {#snippet trigger()}
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-base px-2 py-1 font-mono text-[0.8rem] text-ink hover:bg-ink/5 cursor-pointer">
                            <span>{model}</span>
                            <Icon name="chevron-down" size="sm" class="text-graphite" />
                        </button>
                    {/snippet}
                    {#each models as m (m)}
                        <Dropdown.Item onclick={() => (model = m)}>
                            <span>{m}</span>
                            <span hidden={model !== m} class="ml-2 inline-flex text-graphite"><Icon name="check" size="xs" /></span>
                        </Dropdown.Item>
                    {/each}
                </Dropdown>

                <div class="flex items-center gap-2">
                    <Badge>Demo</Badge>
                    <Button variant="ghost" size="sm" aria-label="Share">
                        <Icon name="share" size="sm" />
                    </Button>
                </div>
            </header>

            <div bind:this={thread} class="flex-1 overflow-y-auto">
                <div hidden={messages.length !== 0} class="flex h-full flex-col items-center justify-center px-6 text-center">
                    <Brand glyphOnly size="lg" class="mb-5 opacity-90" />
                    <h1 class="font-head text-[clamp(1.8rem,4vw,2.6rem)] font-normal tracking-[-0.012em]">
                        How can I help, <span>{greetName}</span>?
                    </h1>
                    <p class="mt-2 text-graphite">Ask anything, or start from one of these.</p>

                    <div class="mt-7 grid w-full max-w-2xl gap-2.5 sm:grid-cols-2">
                        {#each suggestions as s (s)}
                            <button
                                type="button"
                                onclick={() => send(s)}
                                class="rounded-panel border border-line bg-surface-raised px-4 py-3 text-left text-[0.9rem] text-ink hover:border-ink hover:-translate-y-[1px] transition cursor-pointer"
                            >{s}</button>
                        {/each}
                    </div>
                </div>

                <div hidden={messages.length === 0} class="mx-auto w-full max-w-3xl px-5 md:px-8 py-8 flex flex-col gap-8">
                    {#each messages as m, i (i)}
                        <div>
                            <div hidden={m.role !== 'assistant'} class="flex gap-3.5">
                                <span class="mt-0.5 shrink-0 text-ink"><Brand glyphOnly size="sm" /></span>
                                <div class="sigta-prose min-w-0 flex-1 whitespace-pre-wrap">{m.content}</div>
                            </div>
                            <div hidden={m.role !== 'user'} class="flex justify-end">
                                <div class="max-w-[80%] whitespace-pre-wrap rounded-panel bg-surface-raised border border-line px-4 py-2.5 text-[0.96rem] text-ink">{m.content}</div>
                            </div>
                        </div>
                    {/each}

                    <div hidden={!pending} class="flex gap-3.5">
                        <span class="mt-0.5 shrink-0 text-ink"><Brand glyphOnly size="sm" /></span>
                        <div class="flex items-center gap-1 py-2" aria-label="Thinking">
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce [animation-delay:-0.3s]"></span>
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce [animation-delay:-0.15s]"></span>
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-line px-5 md:px-8 py-4">
                <div class="mx-auto w-full max-w-3xl">
                    <Composer placeholder="Message Sigta…" onsubmit={send} />
                    <p class="mt-2 text-center font-mono text-[0.62rem] text-graphite">Sigta can make mistakes. Responses here are a canned demo.</p>
                </div>
            </div>
        </div>
    </Shell>
</div>
