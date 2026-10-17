<script lang="ts">
    /* Money input — an amount typed in major units, bound in MINOR units
       (bind:value), with an inline currency switcher (bind:currency). Hidden
       inputs carry both for plain form posts, as in the Livewire runtime. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { untrack } from 'svelte';
    import { majorFromMinor, minorFromMajor } from '@sigta-hq/core';
    import { Presence } from '../runtime/presence.svelte.js';
    import Field from './Field.svelte';
    import Icon from './Icon.svelte';
    import { translator } from '../runtime/messages.js';

    interface Props extends Omit<HTMLAttributes<HTMLDivElement>, 'onchange'> {
        label?: string | null;
        value?: number | null;
        placeholder?: string | null;
        currency?: string;
        currencies?: Record<string, string> | null;
        currencyName?: string | null;
        onchange?: (value: number | null) => void;
        oncurrency?: (currency: string) => void;
    }

    let { label = null, value = $bindable(null), placeholder = null, currency = $bindable('DKK'), currencies = null, currencyName = null, onchange, oncurrency, ...rest }: Props = $props();
    const t = translator();

    const uid = $props.id();
    const amountId = `sigta-${uid}`;
    const options = $derived(currencies && Object.keys(currencies).length ? currencies : { DKK: 'DKK', EUR: 'EUR', USD: 'USD' });

    // The text field holds major units; value follows it in minor units.
    let major = $state(majorFromMinor(value));
    // A value set from outside (a reset, a loaded record) reaches the field;
    // typing only changes value, so it never rewrites what is being typed.
    $effect.pre(() => {
        const next = value ?? null;
        untrack(() => {
            if (next !== minorFromMajor(major)) major = majorFromMinor(next);
        });
    });
    const typed = () => {
        value = minorFromMajor(major);
        onchange?.(value);
    };

    let open = $state(false);
    const fade = new Presence(() => open);
    let switcher: HTMLDivElement | undefined = $state();

    const selectCurrency = (code: string) => {
        currency = code;
        oncurrency?.(code);
        open = false;
    };
    const outside = (event: MouseEvent) => {
        if (open && !switcher?.contains(event.target as Node | null)) open = false;
    };
    const keydown = (event: KeyboardEvent) => { if (event.key === 'Escape') open = false; };
</script>

<svelte:document onclick={outside} />

<Field {label} for={amountId} {...rest}>
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div data-sigta="money-input" onkeydown={keydown}>
        <input type="hidden" value={value ?? ''} />
        <input type="hidden" name={currencyName ?? 'currency'} value={currency} />

        <input
            type="text"
            id={amountId}
            inputmode="decimal"
            bind:value={major}
            oninput={typed}
            placeholder={placeholder ?? '0'}
            aria-label={`${label ? `${label} ` : ''}${t('amount')}`}
            data-test="money-amount"
            data-scope="money-input"
            data-part="amount"
        />

        <div bind:this={switcher} data-scope="money-input" data-part="switcher">
            <button
                type="button"
                onclick={() => { open = !open; }}
                aria-expanded={open ? 'true' : 'false'}
                aria-haspopup="listbox"
                aria-label={t('Currency')}
                data-test="money-currency"
                data-scope="money-input"
                data-part="currency"
            >
                <span>{currency}</span>
                <Icon name="chevron-down" size="xs" />
            </button>

            <div bind:this={fade.node} hidden={!fade.shown} role="listbox" data-test="money-currency-menu" data-scope="money-input" data-part="menu">
                {#each Object.entries(options) as [code, name] (code)}
                    <button
                        type="button"
                        onclick={() => selectCurrency(code)}
                        role="option"
                        aria-selected={code === currency ? 'true' : 'false'}
                        data-scope="money-input"
                        data-part="option"
                    >
                        <span>{code}</span>
                        {#if String(name) !== code}
                            <span data-scope="money-input" data-part="label">{name}</span>
                        {/if}
                    </button>
                {/each}
            </div>
        </div>
    </div>
</Field>
