<script lang="ts">
    /* A hairline between groups of menu items. */
    import type { HTMLAttributes } from 'svelte/elements';

    let { ...rest }: HTMLAttributes<HTMLHRElement> = $props();
</script>

<!-- The role is explicit, as in the Livewire runtime's markup. -->
<!-- svelte-ignore a11y_no_redundant_roles -->
<hr data-sigta="dropdown.separator" {...rest} role="separator" />
