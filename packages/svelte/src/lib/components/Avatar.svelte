<script lang="ts">
    /* Avatar — a portrait from src, or initials from the first two words of
       the name. Initials are an img labelled with alt ?? name, or hidden when
       there is no name; an aria-hidden you pass leaves both to you. */
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLElement> {
        src?: string | null;
        name?: string | null;
        alt?: string | null;
        shape?: 'circle' | 'square';
        size?: 'xs' | 'sm' | 'base' | 'lg';
        tone?: 'ink' | 'line';
        [key: string]: unknown;
    }

    let { src = null, name = null, alt = null, shape = 'circle', size = 'base', tone = 'ink', ...rest }: Props = $props();

    // PHP truthiness, as the Blade view tests these values ('' and '0' are false).
    const truthy = (value: unknown) => value !== null && value !== undefined && value !== false && value !== '' && value !== '0' && value !== 0;

    // As the Blade view derives them: split on ASCII whitespace, drop empty
    // (and "0") words, the first letter of the first two, upper-cased.
    const initials = $derived(
        (name ?? '').split(/[ \t\n\v\f\r]+/)
            .filter((word) => word !== '' && word !== '0')
            .slice(0, 2)
            .map((word) => (Array.from(word)[0] ?? '').toUpperCase())
            .join(''),
    );
    const label = $derived(alt ?? name);
    const ownHidden = $derived(rest['aria-hidden'] !== undefined && rest['aria-hidden'] !== null);
</script>

{#if truthy(src)}
    <img data-sigta="avatar" src={src} alt={alt ?? name ?? ''} data-size={size} data-shape={shape} data-tone={tone} {...rest} />
{:else}
    <span
        data-sigta="avatar"
        role={!ownHidden && truthy(label) ? 'img' : undefined}
        aria-label={!ownHidden && truthy(label) ? label : undefined}
        aria-hidden={!ownHidden && !truthy(label) ? 'true' : undefined}
        data-size={size}
        data-shape={shape}
        data-tone={tone}
        {...rest}
    >{initials}</span>
{/if}
