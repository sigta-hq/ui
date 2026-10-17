<script lang="ts">
    /* Command — a palette in a native <dialog>: a query box filtering
       <Command.Item>s, arrow keys to move, Enter to run, Escape to close.
       Opens with ⌘K / Ctrl-K (shortcut), bind:open, or by name like a modal:
       <Modal.Trigger name="command"> or a sigta-modal-show window event. */
    import type { Snippet } from 'svelte';
    import type { HTMLDialogAttributes } from 'svelte/elements';
    import { Combobox } from '../runtime/combobox.svelte.js';
    import { providePalette } from '../runtime/command.js';
    import Icon from './Icon.svelte';

    interface Props extends HTMLDialogAttributes {
        name?: string | null;
        placeholder?: string | null;
        shortcut?: boolean;
        open?: boolean;
        children?: Snippet;
    }

    let { name = 'command', placeholder = 'Type a command…', shortcut = true, open = $bindable(false), children, ...rest }: Props = $props();

    let dialog: HTMLDialogElement | undefined = $state();
    const box = new Combobox();

    const show = () => { open = true; };
    const hide = () => { open = false; };
    providePalette({ hide });

    const matches = (detail: { name?: string | null } | null | undefined) =>
        !detail || detail.name === undefined || detail.name === null || detail.name === name;

    $effect(() => {
        if (!dialog) return;
        if (open && !dialog.open) dialog.showModal();
        else if (!open && dialog.open) dialog.close();
    });

    $effect(() => {
        const onShow = (event: Event) => { if (matches((event as CustomEvent).detail)) show(); };
        const onHide = (event: Event) => { if (matches((event as CustomEvent).detail)) hide(); };
        window.addEventListener('sigta-modal-show', onShow);
        window.addEventListener('sigta-modal-hide', onHide);
        return () => {
            window.removeEventListener('sigta-modal-show', onShow);
            window.removeEventListener('sigta-modal-hide', onHide);
        };
    });

    // ⌘K / Ctrl-K anywhere on the page.
    const windowKeydown = (event: KeyboardEvent) => {
        if (shortcut && (event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            show();
        }
    };

    const backdropClick = (event: MouseEvent) => { if (event.target === dialog) hide(); };
    const keydown = (event: KeyboardEvent) => {
        if (event.key === 'Escape') { event.preventDefault(); hide(); }
    };
    const inputKeydown = (event: KeyboardEvent) => {
        if (event.key === 'ArrowDown') { event.preventDefault(); box.move(1); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); box.move(-1); }
        else if (event.key === 'Enter') {
            event.preventDefault();
            box.options.find((o) => o.id === box.activeId)?.element()?.click();
        }
    };
</script>

<svelte:window onkeydown={windowKeydown} />

<div data-sigta="command">
    <dialog
        bind:this={dialog}
        onclick={backdropClick}
        onclose={() => { open = false; }}
        data-scope="command"
        data-part="dialog"
        {...rest}
    >
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div onkeydown={keydown}>
            <div data-scope="command" data-part="search">
                <Icon name="search" size="sm" data-scope="command" data-part="icon" />
                <input
                    type="text"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-controls="sigta-command-list"
                    aria-expanded="true"
                    aria-activedescendant={box.activeId ?? undefined}
                    bind:value={box.query}
                    oninput={() => box.filter()}
                    onkeydown={inputKeydown}
                    placeholder={placeholder ?? undefined}
                    autocomplete="off"
                    data-scope="command"
                    data-part="input"
                />
                <kbd data-scope="command" data-part="esc">esc</kbd>
            </div>

            <div id="sigta-command-list" role="listbox" data-scope="command" data-part="list">
                {@render children?.()}
                <div hidden={box.visible.length > 0} data-scope="command" data-part="empty">Nothing found — try different ink.</div>
            </div>
        </div>
    </dialog>
</div>
