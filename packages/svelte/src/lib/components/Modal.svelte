<script lang="ts">
    /* Modal — a native <dialog> (focus trap and Esc for free). Open it with
       bind:open, or by name from anywhere: <Modal.Trigger name="…">, or a
       `sigta-modal-show` / `sigta-modal-hide` window event with { name }. */
    import type { Snippet } from 'svelte';
    import type { HTMLDialogAttributes } from 'svelte/elements';
    import Heading from './Heading.svelte';
    import Icon from './Icon.svelte';

    interface Props extends HTMLDialogAttributes {
        name?: string | null;
        size?: 'sm' | 'base' | 'lg' | 'xl';
        heading?: string | null;
        dismissible?: boolean;
        fullscreen?: boolean | null;
        open?: boolean;
        children?: Snippet;
    }

    let { name = null, size = 'base', heading = null, dismissible = true, fullscreen = null, open = $bindable(false), children, ...rest }: Props = $props();

    let dialog: HTMLDialogElement | undefined = $state();
    const full = $derived(fullscreen ?? (size === 'lg' || size === 'xl'));

    const matches = (detail: { name?: string | null } | null | undefined) =>
        !detail || detail.name === undefined || detail.name === null || detail.name === name;

    $effect(() => {
        if (!dialog) return;
        if (open && !dialog.open) dialog.showModal();
        else if (!open && dialog.open) dialog.close();
    });

    $effect(() => {
        const onShow = (event: Event) => { if (matches((event as CustomEvent).detail)) open = true; };
        const onHide = (event: Event) => { if (matches((event as CustomEvent).detail)) open = false; };
        window.addEventListener('sigta-modal-show', onShow);
        window.addEventListener('sigta-modal-hide', onHide);
        return () => {
            window.removeEventListener('sigta-modal-show', onShow);
            window.removeEventListener('sigta-modal-hide', onHide);
        };
    });

    const hide = () => { open = false; };
    const backdropClick = (event: MouseEvent) => { if (event.target === dialog) hide(); };
</script>

<div data-sigta="modal">
    <dialog
        bind:this={dialog}
        data-scope="modal"
        data-part="dialog"
        data-size={size}
        data-fullscreen={full ? '' : undefined}
        onclick={dismissible ? backdropClick : undefined}
        onclose={() => { open = false; }}
        {...rest}
    >
        <div data-scope="modal" data-part="body">
            {#if heading}
                <div data-scope="modal" data-part="header">
                    <Heading level={2} size="lg">{heading}</Heading>
                    {#if dismissible}
                        <button type="button" onclick={hide} aria-label="Close" data-scope="modal" data-part="close">
                            <Icon name="x" size="sm" />
                        </button>
                    {/if}
                </div>
            {:else if dismissible}
                <button type="button" onclick={hide} aria-label="Close" data-scope="modal" data-part="close">
                    <Icon name="x" size="sm" />
                </button>
            {/if}

            {@render children?.()}
        </div>
    </dialog>
</div>
