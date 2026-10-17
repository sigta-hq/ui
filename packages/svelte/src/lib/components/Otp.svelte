<script lang="ts">
    /* OTP — a row of single-character boxes over one hidden input that holds
       the code (name lands there; bind:value for two-way binding). Typing
       moves on, pasting spreads across the boxes, Backspace on an empty box
       steps back, the arrow keys move. */
    import type { HTMLInputAttributes } from 'svelte/elements';
    import Field from './Field.svelte';

    interface Props extends Omit<HTMLInputAttributes, 'type'> {
        length?: number;
        label?: string | null;
        description?: string | null;
        error?: string | null;
        autofocus?: boolean;
        value?: string | null;
        class?: string;
    }

    let { length = 6, label = null, description = null, error = null, autofocus = false, value = $bindable(), id, class: klass, disabled, ...rest }: Props = $props();

    const uid = $props.id();
    const count = $derived(Math.max(1, Math.trunc(Number(length)) || 0));
    const baseId = $derived(id ?? `sigta-${uid}`);
    // The field chrome belongs to the first box, so its ids carry -0.
    const describedBy = $derived([description ? `${baseId}-0-description` : '', error ? `${baseId}-0-error` : ''].filter(Boolean).join(' ') || undefined);

    const split = (code: unknown, size: number) => {
        const chars = String(code ?? '').split('').slice(0, size);
        return Array.from({ length: size }, (_, i) => chars[i] ?? '');
    };

    // The code as typed so far; the boxes are written by hand (like the
    // Livewire twin), the state keeps the code and follows an outside value.
    // svelte-ignore state_referenced_locally
    let digits = $state(split(value, count));
    const boxes: HTMLInputElement[] = $state([]);
    let hidden: HTMLInputElement | undefined = $state();

    $effect(() => {
        const next = String(value ?? '');
        if (next === digits.join('') && digits.length === count) return;
        digits = split(next, count);
        boxes.forEach((box, i) => { if (box) box.value = digits[i] ?? ''; });
    });

    const box = (i: number) => boxes[i];

    function sync() {
        value = digits.join('');
        if (hidden) {
            hidden.value = value;
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    function fill(start: number, text: string) {
        const chars = String(text).replace(/\s+/g, '').split('').slice(0, count - start);
        if (!chars.length) return;
        chars.forEach((ch, offset) => {
            digits[start + offset] = ch;
            box(start + offset).value = ch;
        });
        sync();
        box(Math.min(start + chars.length, count - 1)).focus();
    }

    function handleInput(i: number, event: Event) {
        const target = event.target as HTMLInputElement;
        const typed = target.value.replace(/\s+/g, '');
        if (typed === '') {
            digits[i] = '';
            target.value = '';
            sync();
            return;
        }
        target.value = '';
        fill(i, typed);
    }

    function keydown(i: number, event: KeyboardEvent) {
        const target = event.target as HTMLInputElement;
        if (event.key === 'Backspace') {
            if (target.value !== '' || i === 0) return;
            event.preventDefault();
            digits[i - 1] = '';
            box(i - 1).value = '';
            sync();
            box(i - 1).focus();
        } else if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            const next = box(Math.min(count - 1, Math.max(0, i + (event.key === 'ArrowLeft' ? -1 : 1))));
            next.focus();
            next.select();
        }
    }

    function paste(i: number, event: ClipboardEvent) {
        event.preventDefault();
        fill(i, event.clipboardData?.getData('text') ?? '');
    }
</script>

<Field {label} {description} {error} for="{baseId}-0">
    <div data-sigta="otp" class={klass}>
        {#each Array.from({ length: count }) as _, i (i)}
            <!-- svelte-ignore a11y_autofocus -->
            <input
                bind:this={boxes[i]}
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                aria-label="Digit {i + 1} of {count}"
                data-scope="otp"
                data-part="box"
                value={digits[i] ?? ''}
                id={i === 0 ? `${baseId}-0` : undefined}
                autofocus={i === 0 && autofocus ? true : undefined}
                {disabled}
                aria-invalid={error ? 'true' : undefined}
                aria-describedby={describedBy}
                onfocus={(event) => (event.currentTarget as HTMLInputElement).select()}
                oninput={(event) => handleInput(i, event)}
                onkeydown={(event) => keydown(i, event)}
                onpaste={(event) => paste(i, event)}
            />
        {/each}

        <input type="hidden" bind:this={hidden} {id} {disabled} value={value ?? undefined} {...rest} />
    </div>
</Field>
