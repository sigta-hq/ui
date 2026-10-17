<script lang="ts">
    /* A hairline between groups of menu items. */
    import type { HTMLAttributes } from 'svelte/elements';

    let { ...rest }: HTMLAttributes<HTMLHRElement> = $props();
</script>

<hr data-sigta="dropdown.separator" {...rest} role="separator" />
