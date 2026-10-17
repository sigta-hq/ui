<script lang="ts">
    /* Money — a minor-unit amount (øre / cents) in tabular numerals. Known
       currencies carry symbol, position and separators; override any piece,
       or pass an unknown code (printed as a trailing symbol). */
    import type { HTMLAttributes } from 'svelte/elements';
    import { currencyCode, formatMoney } from '@sigta-hq/core';

    interface Props extends HTMLAttributes<HTMLSpanElement> {
        minor?: number | null;
        currency?: string;
        decimals?: number;
        symbol?: string | null;
        position?: 'before' | 'after' | null;
        thousands?: string | null;
        decimal?: string | null;
        suffix?: string | null;
    }

    let { minor = null, currency = 'DKK', decimals = 2, symbol = null, position = null, thousands = null, decimal = null, suffix = null, ...rest }: Props = $props();

    const amount = $derived(formatMoney(minor, { currency, decimals, symbol, position, thousands, decimal }));
</script>

<span data-sigta="money" data-currency={currencyCode(currency)} {...rest}>{amount}{#if suffix}<span data-scope="money" data-part="suffix">{` ${suffix}`}</span>{/if}</span>
