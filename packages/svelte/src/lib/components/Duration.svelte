<script lang="ts">
    /* Duration — whole minutes as "1h 15m" (compact) or "01:30" (clock).
       Exact hours drop the minutes; zero and null read 0m. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { formatDuration } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        minutes?: number | null;
        format?: 'compact' | 'clock';
    }

    let { minutes = null, format = 'compact', ...rest }: Props = $props();
</script>

<span data-sigta="duration" {...rest}>{formatDuration(minutes, format)}</span>
