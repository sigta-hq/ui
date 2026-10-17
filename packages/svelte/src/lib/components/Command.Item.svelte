<script lang="ts">
    /* One command of a Command palette: an icon, the text and a key hint;
       a link with href, else a button. Running it (a click, or Enter while
       it is active) closes the palette. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onDestroy } from 'svelte';
    import { useCombobox, type ComboboxOption } from '../runtime/combobox.svelte.js';
    import { usePalette } from '../runtime/command.js';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        icon?: string | null;
        kbd?: string | null;
        value?: string | null;
        href?: string | null;
        children?: Snippet;
        [key: string]: unknown;
    }

    let { icon = null, kbd = null, value = null, href = null, onclick, children, ...rest }: Props = $props();

    const box = useCombobox();
    const palette = usePalette();
    const uid = $props.id();
    const id = `sigta-command-${uid}`;
    let element: HTMLElement | undefined = $state();

    // Matched against the query by its whole text, as the Livewire runtime does.
    const text = () => element?.textContent ?? '';
    const option: ComboboxOption = {
        id,
        value: () => value ?? text().trim(),
        label: () => text().trim(),
        text,
        element: () => element,
    };
    onDestroy(box.register(option));

    const run = (event: MouseEvent & { currentTarget: EventTarget & HTMLElement }) => {
        (onclick as ((event: MouseEvent) => void) | null | undefined)?.(event);
        palette?.hide();
    };
</script>

<svelte:element
    this={href ? 'a' : 'button'}
    bind:this={element}
    type={href ? undefined : 'button'}
    href={href ?? undefined}
    {id}
    role="option"
    data-value={value ?? undefined}
    onmousemove={() => box.setActive(option)}
    onclick={run}
    data-active={box.activeId === id ? 'true' : undefined}
    hidden={!box.isVisible(option)}
    {...rest}
    data-sigta="command.item"
>
    {#if icon}<Icon name={icon} size="sm" data-scope="command.item" data-part="icon" />{/if}
    <span data-scope="command.item" data-part="label">{@render children?.()}</span>
    {#if kbd}<kbd data-scope="command.item" data-part="kbd">{kbd}</kbd>{/if}
</svelte:element>
