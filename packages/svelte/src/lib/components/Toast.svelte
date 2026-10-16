<script lang="ts">
    /* Toast region. Mount ONCE in your root layout. Anything raises a toast:
       sigta.toast('Saved'), window.Sigta.toast({ text, variant }), a
       `sigta-toast` window event, or a server through the Inertia page props
       (@sigta-hq/svelte/inertia). An action adds a button — the "Undo" after a
       completion — that runs onClick or fires its event, then closes the toast. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { onMount } from 'svelte';
    import { dropToast, leaveToast, pushToast, runToastAction, toastDuration, TOAST_EVENT, TOAST_LEAVE_MS, type Toast, type ToastDetail } from '@sigta-hq/core';
    import Icon from './Icon.svelte';
    import { ensureRuntime } from '../runtime/runtime.js';

    interface Props extends HTMLAttributes<HTMLDivElement> {
        position?: 'bottom-end' | 'bottom-start' | 'top-end' | 'top-start';
    }

    let { position = 'bottom-end', ...rest }: Props = $props();

    let toasts: Toast[] = $state([]);
    let counter = 0;

    const icons: Record<string, string> = { success: 'circle-check', danger: 'triangle-alert', warning: 'circle-alert', info: 'info' };

    function add(detail: ToastDetail = {}) {
        const id = ++counter;
        toasts = pushToast(toasts, detail, id);
        const duration = toastDuration(detail);
        if (duration > 0) setTimeout(() => remove(id), duration);
    }

    function remove(id: number) {
        toasts = leaveToast(toasts, id);
        setTimeout(() => { toasts = dropToast(toasts, id); }, TOAST_LEAVE_MS);
    }

    function act(toast: Toast) {
        runToastAction(toast.action);
        remove(toast.id);
    }

    onMount(() => {
        ensureRuntime();
        const listener = (event: Event) => add((event as CustomEvent<ToastDetail>).detail);
        window.addEventListener(TOAST_EVENT, listener);
        return () => window.removeEventListener(TOAST_EVENT, listener);
    });
</script>

<div data-position={position} {...rest} data-sigta="toast">
    {#each toasts as toast (toast.id)}
        <div role={toast.variant === 'danger' ? 'alert' : 'status'} data-variant={toast.variant} data-leaving={toast.leaving ? '' : undefined} data-scope="toast" data-part="item">
            <span data-scope="toast" data-part="icon" aria-hidden="true">
                {#if icons[toast.variant]}<Icon name={icons[toast.variant]} size="sm" />{/if}
            </span>
            <span data-scope="toast" data-part="body">
                <b hidden={!toast.heading} data-scope="toast" data-part="heading">{toast.heading ?? ''}</b>
                <span>{toast.text}</span>
                {#if toast.link?.href}
                    <a
                        href={toast.link.href}
                        target={toast.link.target || undefined}
                        rel={toast.link.rel || undefined}
                        download={toast.link.download || undefined}
                        data-scope="toast"
                        data-part="link"
                    >{toast.link.text || 'View'}</a>
                {/if}
                {#if toast.action?.text}
                    <button type="button" onclick={() => act(toast)} data-scope="toast" data-part="action">{toast.action.text}</button>
                {/if}
            </span>
            <button type="button" onclick={() => remove(toast.id)} aria-label="Dismiss" data-scope="toast" data-part="dismiss">
                <Icon name="x" size="xs" />
            </button>
        </div>
    {/each}
</div>
