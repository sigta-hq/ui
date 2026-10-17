<script lang="ts">
    /* Editor — the rich text editor (TipTap, from @sigta-hq/core/editor, the
       same editor the Livewire runtime runs). bind:value carries the content
       as HTML, or markdown with format="markdown"; a value set from outside
       replaces the content. Every other attribute (name, data-*) lands on the
       hidden value input, as in the Livewire runtime. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { onMount, tick, untrack } from 'svelte';
    import {
        bindingId,
        createEditor,
        editorFormat,
        editorRows,
        editorTools,
        followLink,
        imageFiles,
        isLinkShortcut,
        openLink as visit,
        type EditorCommand,
        type EditorController,
    } from '@sigta-hq/core/editor';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'value'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        name?: string | null;
        value?: string | null;
        format?: 'html' | 'markdown';
        variant?: 'default' | 'inline';
        placeholder?: string;
        rows?: number | string | null;
        uploadUrl?: string | null;
        resolveUrl?: string | null;
        uploadToken?: string | null;
    }

    let {
        label = null,
        description = null,
        error = null,
        name = null,
        value = $bindable(''),
        format = 'html',
        variant = 'default',
        placeholder = '',
        rows = null,
        uploadUrl = null,
        resolveUrl = null,
        uploadToken = null,
        id,
        class: klass,
        ...rest
    }: Props = $props();

    const uid = $props.id();
    const editorId = $derived(id ?? (name ? bindingId(name) : `sigta-${uid}`));
    const kind = $derived(editorFormat(format));
    const inline = $derived(variant === 'inline');
    const lines = $derived(editorRows(rows, inline));
    const tools = $derived(editorTools(!!uploadUrl));

    // The TipTap editor lives outside reactive state on purpose: read back
    // through a $state proxy, ProseMirror objects fail their own identity
    // checks ("Applying a mismatched transaction").
    let controller: EditorController | null = null;
    let root: HTMLDivElement | undefined = $state();
    let mount: HTMLDivElement | undefined = $state();
    let hidden: HTMLInputElement | undefined = $state();
    let picker: HTMLInputElement | undefined = $state();
    let linkInput: HTMLInputElement | undefined = $state();

    let ready = $state(false);
    let version = $state(0); // bumped on transactions so the toolbar states re-evaluate
    let focused = $state(false);
    let linkOpen = $state(false);
    let linkUrl = $state('');
    let uploading = $state(0);
    // What the editor last wrote into value: anything else came from outside.
    let published: string | null | undefined = untrack(() => value);

    const publish = (next: string) => {
        published = next;
        value = next;
        if (hidden) {
            hidden.value = next;
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };

    onMount(() => {
        if (!mount || !root) return;
        controller = createEditor({
            element: mount,
            content: value ?? '',
            format: kind,
            placeholder,
            uploadUrl,
            uploadToken,
            uploadError: root.dataset.uploadError || null,
            resolveUrl,
            onTransaction: () => { version++; },
            onFocus: () => { focused = true; },
            onBlur: () => { focused = false; },
            onChange: publish,
            onUploading: (count) => { uploading = count; },
        });
        ready = true;

        // Deliberate replacement from outside the component, as in the Livewire runtime.
        const set = (event: Event) => controller?.setContent((event as CustomEvent).detail ?? '');
        root.addEventListener('sigta-editor-set', set);
        return () => {
            root?.removeEventListener('sigta-editor-set', set);
            controller?.destroy();
            controller = null;
        };
    });

    // A value set from outside (a reset, another record) replaces the content.
    $effect(() => {
        const next = value;
        if (!ready || next === published) return;
        published = next;
        controller?.setContent(next ?? '', false);
    });

    const isActive = (command: EditorCommand, attrs?: Record<string, unknown>): boolean => {
        void version;
        return controller?.isActive(command, attrs) ?? false;
    };
    const pressed = (command: EditorCommand, attrs?: Record<string, unknown>) =>
        ready ? (isActive(command, attrs) ? 'true' : 'false') : undefined;

    async function openLink() {
        if (!controller) return;
        linkUrl = controller.linkHref();
        linkOpen = true;
        await tick();
        linkInput?.focus();
    }
    function closeLink() {
        linkOpen = false;
        linkUrl = '';
        controller?.focus();
    }
    function applyLink() {
        if (!controller) return;
        controller.applyLink(linkUrl);
        closeLink();
    }
    function removeLink() {
        controller?.removeLink();
        closeLink();
    }
    function run(command: EditorCommand, attrs?: Record<string, unknown>) {
        if (command === 'link') return void openLink();
        if (command === 'image') return picker?.click();
        controller?.run(command, attrs);
    }
    function picked(event: Event) {
        const input = event.currentTarget as HTMLInputElement;
        const files = imageFiles(input);
        input.value = '';
        if (files.length > 0) void controller?.uploadImages(files);
    }

    // mousedown is prevented so the editor keeps focus and selection while a tool is clicked.
    const keep = (event: MouseEvent) => event.preventDefault();
    const shortcut = (event: KeyboardEvent) => {
        if (!isLinkShortcut(event)) return;
        event.preventDefault();
        void openLink();
    };
    const linkKeys = (event: KeyboardEvent) => {
        if (event.key === 'Enter') { event.preventDefault(); event.stopPropagation(); applyLink(); }
        else if (event.key === 'Escape') { event.preventDefault(); event.stopPropagation(); closeLink(); }
    };
</script>

<Field {label} {description} {error} for={editorId}>
    <!-- svelte-ignore a11y_no_static_element_interactions, a11y_click_events_have_key_events -->
    <div
        bind:this={root}
        data-sigta="editor"
        data-format={kind}
        data-variant={variant}
        data-placeholder={placeholder}
        data-upload-url={uploadUrl || undefined}
        data-upload-token={uploadUrl ? (uploadToken ?? '') : undefined}
        data-resolve-url={resolveUrl || undefined}
        data-invalid={error ? '' : undefined}
        class={klass}
        onkeydown={shortcut}
        onclick={(event) => followLink(event)}
    >
        <div
            role="toolbar"
            aria-label="Formatting"
            aria-controls={editorId}
            data-scope="editor"
            data-part="toolbar"
            hidden={inline && !(focused || linkOpen)}
        >
            {#each tools as tool, index (tool?.command ?? `separator-${index}`)}
                {#if tool === null}
                    <span data-scope="editor" data-part="separator" aria-hidden="true"></span>
                {:else}
                    <button
                        type="button"
                        data-scope="editor"
                        data-part="tool"
                        aria-label={tool.label}
                        aria-pressed={pressed(tool.command, tool.attrs)}
                        onmousedown={keep}
                        onclick={() => run(tool.command, tool.attrs)}
                    >
                        <Icon name={tool.icon} size="sm" />
                    </button>
                {/if}
            {/each}

            <span data-scope="editor" data-part="end">
                {#if uploadUrl}
                    <span hidden={uploading === 0} data-scope="editor" data-part="uploading" role="status" aria-label="Uploading"><Icon name="loader-circle" size="sm" /></span>
                {/if}
                <button type="button" data-scope="editor" data-part="tool" aria-label="Undo" onmousedown={keep} onclick={() => run('undo')}><Icon name="undo-2" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Redo" onmousedown={keep} onclick={() => run('redo')}><Icon name="redo-2" size="sm" /></button>
            </span>

            <div hidden={!linkOpen} data-sigta="editor-link" data-scope="editor" data-part="link">
                <input
                    bind:this={linkInput}
                    bind:value={linkUrl}
                    type="url"
                    inputmode="url"
                    placeholder="https://"
                    aria-label="Link address"
                    data-scope="editor"
                    data-part="link-input"
                    onkeydown={linkKeys}
                />
                <button type="button" data-scope="editor" data-part="tool" aria-label="Apply link" onmousedown={keep} onclick={applyLink}><Icon name="check" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Open link" onmousedown={keep} onclick={() => visit(linkUrl)} disabled={ready ? linkUrl.trim() === '' : undefined}><Icon name="external-link" size="sm" /></button>
                <button type="button" data-scope="editor" data-part="tool" aria-label="Remove link" onmousedown={keep} onclick={removeLink}><Icon name="unlink" size="sm" /></button>
            </div>
        </div>

        <!-- TipTap mounts here and owns this subtree. -->
        <div bind:this={mount} id={editorId} data-scope="editor" data-part="mount" style="--sigta-editor-rows: {lines}"></div>

        {#if uploadUrl}
            <!-- The picker behind the toolbar's picture tool; drop and paste reach the editor directly. -->
            <input bind:this={picker} type="file" accept="image/*" multiple tabindex="-1" aria-hidden="true" data-scope="editor" data-part="picker" onchange={picked} />
        {/if}

        <input bind:this={hidden} type="hidden" name={name ?? undefined} value={value ?? ''} {...rest} />
    </div>
</Field>
