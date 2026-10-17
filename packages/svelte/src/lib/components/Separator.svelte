<script lang="ts">
    /* Separator — a plain hairline <hr> inside content; Rule is the decorative one. */
    import type { HTMLAttributes } from 'svelte/elements';

    interface Props extends HTMLAttributes<HTMLHRElement> {
        variant?: 'solid' | 'dashed';
    }

    let { variant = 'solid', ...rest }: Props = $props();
</script>

<hr data-sigta="separator" data-variant={variant} {...rest} />
