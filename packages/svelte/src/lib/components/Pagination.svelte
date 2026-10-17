<script lang="ts">
    /* Page navigation from a plain paginator object ({ currentPage, lastPage }
       or a Laravel paginator's JSON) or manual pages / current; url is a
       pattern ("?page=%d") or a function of the page. Without a url the pages
       are buttons for you to wire up. The page model is the core's, the same
       one the Blade view computes. */
    import type { HTMLAttributes } from 'svelte/elements';
    import { paginate, type PageState, type PageUrl } from '@sigta-hq/core';
    import Icon from './Icon.svelte';

    interface Props extends HTMLAttributes<HTMLElement> {
        paginator?: PageState | Record<string, unknown> | null;
        pages?: number | null;
        current?: number;
        url?: PageUrl;
    }

    let { paginator = null, pages = null, current = 1, url = null, ...rest }: Props = $props();

    const model = $derived(paginate({ paginator, pages, current, url }));
</script>

<nav data-sigta="pagination" aria-label="Pagination" {...rest}>
    <ul data-scope="pagination" data-part="list">
        <li>
            {#if model.atStart}
                <span aria-disabled="true" aria-hidden="true" data-scope="pagination" data-part="prev"><Icon name="chevron-left" size="sm" /></span>
            {:else if model.prevHref !== null}
                <a href={model.prevHref} aria-label="Previous page" rel="prev" data-scope="pagination" data-part="prev"><Icon name="chevron-left" size="sm" /></a>
            {:else}
                <button type="button" aria-label="Previous page" data-scope="pagination" data-part="prev"><Icon name="chevron-left" size="sm" /></button>
            {/if}
        </li>

        {#each model.items as item, index (index)}
            <li>
                {#if item === '…'}
                    <span aria-hidden="true" data-scope="pagination" data-part="gap">…</span>
                {:else if item === model.current}
                    <span aria-current="page" data-scope="pagination" data-part="page">{item}</span>
                {:else if model.hrefFor}
                    <a href={model.hrefFor(item)} aria-label="Page {item}" data-scope="pagination" data-part="page">{item}</a>
                {:else}
                    <button type="button" aria-label="Page {item}" data-scope="pagination" data-part="page">{item}</button>
                {/if}
            </li>
        {/each}

        <li>
            {#if model.atEnd}
                <span aria-disabled="true" aria-hidden="true" data-scope="pagination" data-part="next"><Icon name="chevron-right" size="sm" /></span>
            {:else if model.nextHref !== null}
                <a href={model.nextHref} aria-label="Next page" rel="next" data-scope="pagination" data-part="next"><Icon name="chevron-right" size="sm" /></a>
            {:else}
                <button type="button" aria-label="Next page" data-scope="pagination" data-part="next"><Icon name="chevron-right" size="sm" /></button>
            {/if}
        </li>
    </ul>
</nav>
