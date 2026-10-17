<script lang="ts">
    /* Duration input — a compact "1h59m" button that becomes an HH:MM editor
       on click; blur or Enter parses the draft (forgiving: H:MM, HH:MM or bare
       minutes) into whole minutes on bind:value, Escape cancels. A hidden
       input carries the minutes for plain form posts. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { tick } from 'svelte';
    import { durationDraft, durationLabel, durationMinutes, parseDuration } from '@sigta-hq/core';
    import Field from './Field.svelte';

    interface Props extends Omit<HTMLAttributes<HTMLDivElement>, 'onchange'> {
        label?: string | null;
        value?: number | null;
        placeholder?: string | null;
        onchange?: (value: number | null) => void;
    }

    let { label = null, value = $bindable(null), placeholder = null, onchange, ...rest }: Props = $props();

    const uid = $props.id();
    const editorId = `sigta-${uid}`;

    const minutes = $derived(durationMinutes(value));
    const hasValue = $derived(minutes !== null && minutes > 0);
    const text = $derived(durationLabel(minutes, placeholder ?? '0m'));

    let editing = $state(false);
    let draft = $state('');
    let editor: HTMLInputElement | undefined = $state();

    async function edit() {
        draft = durationDraft(minutes);
        editing = true;
        await tick();
        editor?.focus();
        editor?.select();
    }

    function commit() {
        const total = parseDuration(draft);
        value = total > 0 ? total : null;
        editing = false;
        onchange?.(value);
    }

    const keydown = (event: KeyboardEvent) => {
        if (event.key === 'Enter') { event.preventDefault(); commit(); }
        else if (event.key === 'Escape') { event.preventDefault(); editing = false; }
    };
</script>

<Field {label} for={editorId} {...rest}>
    <div data-sigta="duration-input">
        <input type="hidden" value={minutes ?? ''} />

        <button
            type="button"
            hidden={editing}
            onclick={edit}
            data-empty={hasValue ? undefined : ''}
            data-scope="duration-input"
            data-part="display"
            data-test="duration-display"
        >{text}</button>

        <input
            hidden={!editing}
            bind:this={editor}
            id={editorId}
            type="text"
            bind:value={draft}
            onblur={commit}
            onkeydown={keydown}
            inputmode="numeric"
            placeholder="00:00"
            aria-label={`${label ? `${label} ` : ''}as hours and minutes, HH:MM`}
            data-scope="duration-input"
            data-part="editor"
            data-test="duration-editor"
        />
    </div>
</Field>
