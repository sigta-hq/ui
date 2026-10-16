<script lang="ts">
    /* One option of an Autocomplete; value defaults to its text, label (shown
       back in the query box) to its text as well. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { onDestroy } from 'svelte';
    import { useCombobox, type ComboboxOption } from '../runtime/combobox.svelte.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        value?: string | null;
        label?: string | null;
        children?: Snippet;
    }

    let { value = null, label = null, children, ...rest }: Props = $props();

    const box = useCombobox();
    const uid = $props.id();
    const id = `sigta-option-${uid}`;
    let element: HTMLDivElement | undefined = $state();

    const text = () => element?.textContent ?? String(label ?? value ?? '');
    const option: ComboboxOption = {
        id,
        value: () => value ?? text().trim(),
        label: () => (label ?? text()).trim(),
        text,
        element: () => element,
    };
    onDestroy(box.register(option));
</script>

<!-- svelte-ignore a11y_click_events_have_key_events -->
<div
    bind:this={element}
    {id}
    role="option"
    data-value={value ?? undefined}
    data-label={label ?? undefined}
    onmousedown={(event) => event.preventDefault()}
    onclick={() => box.choose(option)}
    onmousemove={() => box.setActive(option)}
    aria-selected={box.isSelected(option.value()) ? 'true' : 'false'}
    data-active={box.activeId === id ? 'true' : undefined}
    hidden={!box.isVisible(option)}
    {...rest}
    data-sigta="autocomplete.option"
>{@render children?.()}</div>
