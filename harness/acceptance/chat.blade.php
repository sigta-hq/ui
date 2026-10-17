{{-- The Sigta Chat page (playground/resources/views/kit/chat.blade.php), body
     only: an app built from the components with a client-side mock. chat.svelte
     is its twin, with the mock in Svelte state. --}}
<div
    class="h-screen"
    x-data="{
        model: 'Sigta Opus',
        models: ['Sigta Opus', 'Sigta Sonnet', 'Sigta Haiku'],
        greetName: 'Søren',
        pending: false,
        messages: [],
        conversations: [
            { id: 1, title: 'Rebrand the pricing section', group: 'Today' },
            { id: 2, title: 'Explain the token layer', group: 'Today' },
            { id: 3, title: 'Draft the Q3 changelog', group: 'Yesterday' },
            { id: 4, title: 'Kanban drag-and-drop approach', group: 'Yesterday' },
            { id: 5, title: 'Whimsical error copy ideas', group: 'Previous 7 days' },
        ],
        activeId: null,

        get grouped() {
            return this.conversations.reduce((acc, c) => {
                (acc[c.group] ??= []).push(c);
                return acc;
            }, {});
        },

        newChat() {
            this.messages = [];
            this.activeId = null;
            this.$nextTick(() => this.$root.querySelector('[data-sigta=composer] textarea')?.focus());
        },

        send(text) {
            const value = (text || '').trim();
            if (! value || this.pending) return;
            this.messages.push({ role: 'user', content: value });
            this.pending = true;
            this.scrollDown();
            setTimeout(() => {
                this.messages.push({ role: 'assistant', content: this.reply(value) });
                this.pending = false;
                this.scrollDown();
            }, 900);
        },

        reply(prompt) {
            return 'Here\'s a first pass at “' + prompt + '”.\n\n'
                + 'This is a Sigta UI demo surface, so the response is canned — but the '
                + 'chrome is real: the thread, the model picker, the composer, and this '
                + 'prose block all come from the component library. Swap the mock for a '
                + 'Livewire action and it is a working chat.';
        },

        scrollDown() {
            this.$nextTick(() => {
                const el = this.$refs.thread;
                if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
            });
        },
    }"
>
    <x-sigta::shell full>
        {{-- ---------------- sidebar ---------------- --}}
        <x-slot:sidebar>
            <aside class="flex h-screen flex-col bg-surface border-r border-line px-3 py-4" aria-label="Conversations">
                <div class="flex items-center justify-between px-1">
                    <x-sigta::brand name="Sigta" size="sm" />
                    <x-sigta::button variant="ghost" size="sm" aria-label="New chat" x-on:click="newChat()">
                        <x-sigta::icon name="square-pen" size="sm" />
                    </x-sigta::button>
                </div>

                <div class="mt-4">
                    <x-sigta::button variant="primary" class="w-full justify-center" x-on:click="newChat()">
                        <x-sigta::icon name="plus" size="sm" /> New chat
                    </x-sigta::button>
                </div>

                <nav class="mt-6 flex-1 overflow-y-auto -mx-1 px-1" aria-label="History">
                    <template x-for="(items, group) in grouped" :key="group">
                        <div class="mb-4">
                            <p class="px-2 mb-1.5 font-mono text-[0.6rem] tracking-[0.14em] uppercase text-graphite" x-text="group"></p>
                            <template x-for="c in items" :key="c.id">
                                <button
                                    type="button"
                                    x-on:click="activeId = c.id"
                                    x-bind:class="activeId === c.id ? 'bg-ink text-surface' : 'text-graphite hover:text-ink hover:bg-ink/5'"
                                    class="w-full text-left truncate rounded-base px-2 py-1.5 font-mono text-[0.76rem] cursor-pointer"
                                    x-text="c.title"
                                ></button>
                            </template>
                        </div>
                    </template>
                </nav>

                {{-- account --}}
                <div class="mt-2 border-t border-line pt-3">
                    <x-sigta::dropdown position="top-start" class="w-full">
                        <x-slot:trigger>
                            <button type="button" class="flex w-full items-center gap-2.5 rounded-base px-2 py-1.5 text-left hover:bg-ink/5 cursor-pointer">
                                <x-sigta::avatar name="Søren Frisk" size="sm" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-[0.85rem] text-ink">Søren Frisk</span>
                                    <span class="block truncate font-mono text-[0.62rem] text-graphite">Studio · Pro</span>
                                </span>
                                <x-sigta::icon name="chevrons-up-down" size="xs" class="text-graphite shrink-0" />
                            </button>
                        </x-slot:trigger>
                        <x-sigta::dropdown.item icon="settings">Settings</x-sigta::dropdown.item>
                        <x-sigta::dropdown.item icon="credit-card">Billing</x-sigta::dropdown.item>
                        <x-sigta::dropdown.separator />
                        <x-sigta::dropdown.item icon="log-out" variant="danger">Sign out</x-sigta::dropdown.item>
                    </x-sigta::dropdown>
                </div>
            </aside>
        </x-slot:sidebar>

        {{-- ---------------- main ---------------- --}}
        <div class="flex h-screen flex-col">
            {{-- top bar: model picker --}}
            <header class="flex items-center justify-between gap-4 border-b border-line px-5 md:px-8 py-3">
                <x-sigta::dropdown>
                    <x-slot:trigger>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-base px-2 py-1 font-mono text-[0.8rem] text-ink hover:bg-ink/5 cursor-pointer">
                            <span x-text="model"></span>
                            <x-sigta::icon name="chevron-down" size="sm" class="text-graphite" />
                        </button>
                    </x-slot:trigger>
                    <template x-for="m in models" :key="m">
                        <x-sigta::dropdown.item x-on:click="model = m">
                            <span x-text="m"></span>
                            <span x-show="model === m" x-cloak class="ml-2 inline-flex text-graphite"><x-sigta::icon name="check" size="xs" /></span>
                        </x-sigta::dropdown.item>
                    </template>
                </x-sigta::dropdown>

                <div class="flex items-center gap-2">
                    <x-sigta::badge>Demo</x-sigta::badge>
                    <x-sigta::button variant="ghost" size="sm" aria-label="Share">
                        <x-sigta::icon name="share" size="sm" />
                    </x-sigta::button>
                </div>
            </header>

            {{-- thread / empty state --}}
            <div x-ref="thread" class="flex-1 overflow-y-auto">
                {{-- empty state --}}
                <div x-show="messages.length === 0" class="flex h-full flex-col items-center justify-center px-6 text-center">
                    <x-sigta::brand glyph-only size="lg" class="mb-5 opacity-90" />
                    <h1 class="font-head text-[clamp(1.8rem,4vw,2.6rem)] font-normal tracking-[-0.012em]">
                        How can I help, <span x-text="greetName"></span>?
                    </h1>
                    <p class="mt-2 text-graphite">Ask anything, or start from one of these.</p>

                    <div class="mt-7 grid w-full max-w-2xl gap-2.5 sm:grid-cols-2">
                        <template x-for="s in [
                            'Draft a launch note for Plumb 2.4',
                            'Explain our token theming to a new dev',
                            'Rewrite this error message to be kinder',
                            'Outline a pricing page for a SaaS',
                        ]" :key="s">
                            <button
                                type="button"
                                x-on:click="send(s)"
                                class="rounded-panel border border-line bg-surface-raised px-4 py-3 text-left text-[0.9rem] text-ink hover:border-ink hover:-translate-y-[1px] transition cursor-pointer"
                                x-text="s"
                            ></button>
                        </template>
                    </div>
                </div>

                {{-- messages --}}
                <div x-show="messages.length > 0" class="mx-auto w-full max-w-3xl px-5 md:px-8 py-8 flex flex-col gap-8">
                    <template x-for="(m, i) in messages" :key="i">
                        <div>
                            {{-- assistant --}}
                            <div x-show="m.role === 'assistant'" class="flex gap-3.5">
                                <span class="mt-0.5 shrink-0 text-ink"><x-sigta::brand glyph-only size="sm" /></span>
                                <div class="sigta-prose min-w-0 flex-1 whitespace-pre-wrap" x-text="m.content"></div>
                            </div>
                            {{-- user --}}
                            <div x-show="m.role === 'user'" class="flex justify-end">
                                <div class="max-w-[80%] whitespace-pre-wrap rounded-panel bg-surface-raised border border-line px-4 py-2.5 text-[0.96rem] text-ink" x-text="m.content"></div>
                            </div>
                        </div>
                    </template>

                    {{-- thinking indicator --}}
                    <div x-show="pending" class="flex gap-3.5" x-cloak>
                        <span class="mt-0.5 shrink-0 text-ink"><x-sigta::brand glyph-only size="sm" /></span>
                        <div class="flex items-center gap-1 py-2" aria-label="Thinking">
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce [animation-delay:-0.3s]"></span>
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce [animation-delay:-0.15s]"></span>
                            <span class="size-1.5 rounded-full bg-graphite animate-bounce"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- composer --}}
            <div class="border-t border-line px-5 md:px-8 py-4">
                <div class="mx-auto w-full max-w-3xl">
                    <x-sigta::composer
                        placeholder="Message Sigta…"
                        x-on:sigta-composer-submit="send($event.detail.text)"
                    />
                    <p class="mt-2 text-center font-mono text-[0.62rem] text-graphite">Sigta can make mistakes. Responses here are a canned demo.</p>
                </div>
            </div>
        </div>
    </x-sigta::shell>
</div>
