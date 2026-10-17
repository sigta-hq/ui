<script lang="ts">
    /* Chat composer — an auto-growing textarea (up to six rows) between a
       leading snippet (attach, by default) and the send button. Enter sends,
       Shift+Enter breaks the line. Sending calls onsubmit(text) and, like the
       Livewire runtime, dispatches a bubbling sigta-composer-submit { text }
       from the textarea; then the field clears. bind:value for the draft;
       other attributes land on the textarea. */
    import type { Snippet } from 'svelte';
    import type { HTMLTextareaAttributes } from 'svelte/elements';
    import { onMount, tick } from 'svelte';
    import { autosize } from '@sigta-hq/core';
    import Button from './Button.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLTextareaAttributes, 'value' | 'onsubmit'> {
        placeholder?: string;
        name?: string | null;
        value?: string | null;
        onsubmit?: (text: string) => void;
        leading?: Snippet;
    }

    let { placeholder = 'Write a message…', name = null, value = $bindable(), onsubmit, leading, id, oninput, onkeydown, ...rest }: Props = $props();

    const MAX_ROWS = 6;
    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);

    let input: HTMLTextAreaElement | undefined = $state();
    // The Livewire render has no disabled Send until Alpine runs; neither does this one.
    let mounted = $state(false);

    const resize = () => { if (input) autosize(input, MAX_ROWS); };

    async function submit() {
        const text = (value ?? '').trim();
        if (!text || !input) return;
        input.dispatchEvent(new CustomEvent('sigta-composer-submit', { detail: { text }, bubbles: true, composed: true, cancelable: true }));
        onsubmit?.(text);
        value = '';
        await tick();
        resize();
    }

    const typed = (event: Event & { currentTarget: EventTarget & HTMLTextAreaElement }) => {
        resize();
        oninput?.(event as Parameters<NonNullable<typeof oninput>>[0]);
    };

    const keydown = (event: KeyboardEvent & { currentTarget: EventTarget & HTMLTextAreaElement }) => {
        onkeydown?.(event);
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            submit();
        }
    };

    onMount(() => {
        mounted = true;
        tick().then(resize);
    });
</script>

<div data-sigta="composer">
    <div data-scope="composer" data-part="leading">
        {#if leading}
            {@render leading()}
        {:else}
            <Button variant="ghost" aria-label="Attach file">
                <Icon name="paperclip" size="sm" />
            </Button>
        {/if}
    </div>

    <textarea
        bind:this={input}
        bind:value
        oninput={typed}
        onkeydown={keydown}
        rows="1"
        {placeholder}
        name={name ?? undefined}
        data-scope="composer"
        data-part="input"
        id={inputId}
        aria-label="Message"
        {...rest}
    ></textarea>

    <div data-scope="composer" data-part="send">
        <Button variant="primary" size="sm" aria-label="Send" onclick={submit} disabled={mounted && !(value ?? '').trim()}>
            <Icon name="send" size="sm" />
        </Button>
    </div>
</div>
