<script lang="ts">
    /* Closes the modal with this name (any modal when unnamed) with the
       same window event the Livewire runtime uses: sigta-modal-hide. */
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        name?: string | null;
        children?: Snippet;
    }

    let { name = null, children, ...rest }: Props = $props();

    const fire = (event: MouseEvent) => {
        (event.currentTarget as HTMLElement).dispatchEvent(new CustomEvent('sigta-modal-hide', { detail: { name }, bubbles: true, composed: true }));
    };
</script>

<!-- svelte-ignore a11y_click_events_have_key_events, a11y_no_static_element_interactions -->
<span onclick={fire} {...rest} data-sigta="modal.close">{@render children?.()}</span>
