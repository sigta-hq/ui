{{-- Page navigation. Pass a Laravel paginator (`:paginator="$users"`), the neutral shape
     both runtimes read (`:paginator="['currentPage' => 3, 'lastPage' => 12]"`, or a
     paginator's toArray()), or drive it manually with `pages`/`current`. `url` is a href
     pattern: "?page=%d" (sprintf) or a prefix the page number is appended to ("?page="),
     or a Closure(int $page): string. Without hrefs (no paginator and no `url`), the
     page links are plain buttons for the consumer to wire up. --}}
@props([
    'paginator' => null,
    'pages' => null,
    'current' => 1,
    'url' => null,
])

@php
$hrefFor = match (true) {
    $url === null => null,
    $url instanceof \Closure => $url,
    default => fn (int $page) => str_contains($url, '%d') ? sprintf($url, $page) : $url.$page,
};

if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator) {
    $isSimple = ! $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $current = $paginator->currentPage();
    $last = $isSimple ? null : $paginator->lastPage();
    $hasMore = $paginator->hasMorePages();
    $hrefFor = fn (int $page) => $paginator->url($page);
    $prevHref = $paginator->previousPageUrl();
    $nextHref = $isSimple ? ($hasMore ? $paginator->url($current + 1) : null) : $paginator->nextPageUrl();
} else {
    if (is_array($paginator) && $paginator !== []) {
        // The neutral shape: { currentPage, lastPage?, hasMorePages? }, or a paginator's
        // toArray() (current_page, last_page, next_page_url). Not clamped, like a paginator.
        $current = (int) ($paginator['currentPage'] ?? $paginator['current_page'] ?? 1);
        $last = $paginator['lastPage'] ?? $paginator['last_page'] ?? null;
        $last = $last === null ? null : (int) $last;
        $hasMore = (bool) ($paginator['hasMorePages'] ?? (array_key_exists('next_page_url', $paginator)
            ? $paginator['next_page_url'] !== null
            : $last !== null && $current < $last));
    } else {
        $last = max(1, (int) ($pages ?? 1));
        $current = min($last, max(1, (int) $current));
        $hasMore = $current < $last;
    }
    $prevHref = $hrefFor && $current > 1 ? $hrefFor($current - 1) : null;
    $nextHref = $hrefFor && ($last !== null ? $current < $last : $hasMore) ? $hrefFor($current + 1) : null;
}

// Window of page links: 1 … around-current … last.
$items = [];
if ($last !== null) {
    if ($last <= 7) {
        $items = range(1, $last);
    } else {
        $from = max(2, $current - 1);
        $to = min($last - 1, $current + 1);
        $items = [1];
        if ($from > 2) {
            $items[] = '…';
        }
        array_push($items, ...range($from, $to));
        if ($to < $last - 1) {
            $items[] = '…';
        }
        $items[] = $last;
    }
}

$atStart = $current <= 1;
$atEnd = $last !== null ? $current >= $last : ! $hasMore;
@endphp

<nav data-sigta="pagination" {{ $attributes->merge(['aria-label' => 'Pagination']) }}>
    <ul data-scope="pagination" data-part="list">
        <li>
            @if ($atStart)
                <span aria-disabled="true" aria-hidden="true" data-scope="pagination" data-part="prev"><x-sigta::icon name="chevron-left" size="sm" /></span>
            @elseif ($prevHref !== null)
                <a href="{{ $prevHref }}" aria-label="Previous page" rel="prev" data-scope="pagination" data-part="prev"><x-sigta::icon name="chevron-left" size="sm" /></a>
            @else
                <button type="button" aria-label="Previous page" data-scope="pagination" data-part="prev"><x-sigta::icon name="chevron-left" size="sm" /></button>
            @endif
        </li>

        @foreach ($items as $item)
            <li>
                @if ($item === '…')
                    <span aria-hidden="true" data-scope="pagination" data-part="gap">…</span>
                @elseif ($item === $current)
                    <span aria-current="page" data-scope="pagination" data-part="page">{{ $item }}</span>
                @elseif ($hrefFor !== null)
                    <a href="{{ $hrefFor($item) }}" aria-label="Page {{ $item }}" data-scope="pagination" data-part="page">{{ $item }}</a>
                @else
                    <button type="button" aria-label="Page {{ $item }}" data-scope="pagination" data-part="page">{{ $item }}</button>
                @endif
            </li>
        @endforeach

        <li>
            @if ($atEnd)
                <span aria-disabled="true" aria-hidden="true" data-scope="pagination" data-part="next"><x-sigta::icon name="chevron-right" size="sm" /></span>
            @elseif ($nextHref !== null)
                <a href="{{ $nextHref }}" aria-label="Next page" rel="next" data-scope="pagination" data-part="next"><x-sigta::icon name="chevron-right" size="sm" /></a>
            @else
                <button type="button" aria-label="Next page" data-scope="pagination" data-part="next"><x-sigta::icon name="chevron-right" size="sm" /></button>
            @endif
        </li>
    </ul>
</nav>
