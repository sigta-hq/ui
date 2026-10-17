<script lang="ts">
    /* File upload — a dashed dropzone (a <label> around a visually hidden
       native file input) in the field chrome, listing the picked files by
       name and size. The same look and states as the Livewire runtime, but no
       backend protocol: the File objects are yours. bind:files (File[]) or
       onfiles(files); with Inertia put them on a useForm field and post it as
       FormData. A dropped file is assigned to the native input too, so a plain
       <form> posts it. Other attributes land on the input. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import { fileEntries } from '@sigta-hq/core';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'type' | 'files' | 'multiple'> {
        label?: string | null;
        description?: string | null;
        error?: string | null;
        multiple?: boolean;
        helper?: string | null;
        files?: File[];
        onfiles?: (files: File[]) => void;
    }

    let { label = null, description = null, error = null, multiple = false, helper = null, files = $bindable([]), onfiles, onchange, id, disabled, ...rest }: Props = $props();

    const uid = $props.id();
    const inputId = $derived(id ?? `sigta-${uid}`);
    const describedBy = $derived([description ? `${inputId}-description` : '', error ? `${inputId}-error` : ''].filter(Boolean).join(' ') || undefined);
    const prompt = $derived(helper ?? (multiple ? 'Drag files here, or' : 'Drag a file here, or'));
    const entries = $derived(fileEntries(files ?? []));

    let input: HTMLInputElement | undefined = $state();
    let dragging = $state(0);

    const same = (a: ArrayLike<File>, b: ArrayLike<File>) => a.length === b.length && Array.from(a).every((file, i) => file === b[i]);

    // Files set from outside (a reset, a restored draft) reach the native input.
    $effect(() => {
        const next = files ?? [];
        if (!input || same(input.files ?? [], next)) return;
        if (!next.length) {
            input.value = '';
            return;
        }
        const data = new DataTransfer();
        next.forEach((file) => data.items.add(file));
        input.files = data.files;
    });

    const change = (event: Event & { currentTarget: EventTarget & HTMLInputElement }) => {
        files = Array.from(event.currentTarget.files ?? []);
        onfiles?.(files);
        onchange?.(event);
    };

    const drop = (event: DragEvent) => {
        dragging = 0;
        const dropped = event.dataTransfer?.files;
        if (!dropped?.length || !input) return;
        input.files = dropped;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    };
</script>

<Field {label} {description} {error} for={inputId}>
    <div data-sigta="file-upload">
        <label
            data-scope="file-upload"
            data-part="zone"
            data-invalid={error ? '' : undefined}
            data-disabled={disabled ? '' : undefined}
            data-dragging={dragging > 0 ? 'true' : undefined}
            ondragenter={(event) => { event.preventDefault(); dragging++; }}
            ondragleave={(event) => { event.preventDefault(); dragging = Math.max(0, dragging - 1); }}
            ondragover={(event) => event.preventDefault()}
            ondrop={(event) => { event.preventDefault(); drop(event); }}
        >
            <input
                bind:this={input}
                type="file"
                {multiple}
                {disabled}
                id={inputId}
                aria-invalid={error ? 'true' : undefined}
                aria-describedby={describedBy}
                onchange={change}
                data-scope="file-upload"
                data-part="input"
                {...rest}
            />

            <Icon name="upload-cloud" size="lg" data-scope="file-upload" data-part="icon" />

            <span data-scope="file-upload" data-part="helper">
                {prompt}
                <span data-scope="file-upload" data-part="browse">browse</span>
            </span>
        </label>

        <ul style={entries.length ? undefined : 'display: none'} data-scope="file-upload" data-part="list">
            {#each entries as file, index (index)}
                <li data-scope="file-upload" data-part="file">
                    <span data-scope="file-upload" data-part="name">{file.name}</span>
                    <span data-scope="file-upload" data-part="size">{file.size}</span>
                </li>
            {/each}
        </ul>
    </div>
</Field>
