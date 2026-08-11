{{-- Page navigation. Pass a Laravel paginator (`:paginator="$users"`) or drive it
     manually with `pages`/`current`/`url`. `url` is a href pattern for manual mode:
     "?page=%d" (sprintf) or a prefix the page number is appended to ("?page=").
     Without `url`, manual mode renders plain buttons for the consumer to wire up. --}}
@props([
    'paginator' => null,
    'pages' => null,
    'current' => 1,
    'url' => null,
])

@php
$isSimple = $paginator !== null && ! $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;

if ($paginator) {
    $current = $paginator->currentPage();
    $last = $isSimple ? null : $paginator->lastPage();
    $hrefFor = fn (int $page) => $paginator->url($page);
    $prevHref = $paginator->previousPageUrl();
    $nextHref = $isSimple ? ($paginator->hasMorePages() ? $paginator->url($current + 1) : null) : $paginator->nextPageUrl();
} else {
    $last = max(1, (int) ($pages ?? 1));
    $current = min($last, max(1, (int) $current));
    $hrefFor = $url === null
        ? null
        : fn (int $page) => str_contains($url, '%d') ? sprintf($url, $page) : $url.$page;
    $prevHref = $hrefFor && $current > 1 ? $hrefFor($current - 1) : null;
    $nextHref = $hrefFor && $current < $last ? $hrefFor($current + 1) : null;
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
$atEnd = $last !== null ? $current >= $last : ($nextHref === null);

$pageBase = 'flex h-7 min-w-7 items-center justify-center px-1 rounded-base tabular-nums no-underline transition-colors duration-150 ease-out';
$pageIdle = $pageBase.' text-graphite hover:text-ink hover:bg-ink/5';
$pageCurrent = $pageBase.' bg-ink text-surface';
$arrow = 'flex size-7 items-center justify-center rounded-base text-ink no-underline transition-colors duration-150 ease-out hover:bg-ink/5 disabled:opacity-40 disabled:pointer-events-none aria-disabled:opacity-40 aria-disabled:pointer-events-none';
@endphp

<nav data-sigta="pagination" {{ $attributes->merge(['aria-label' => 'Pagination']) }}>
    <ul class="m-0 flex list-none items-center gap-1 p-0 font-mono text-[0.72rem] tracking-[0.04em]">
        <li>
            @if ($atStart)
                <span class="{{ $arrow }}" aria-disabled="true" aria-hidden="true"><x-sigta::icon name="chevron-left" size="sm" /></span>
            @elseif ($prevHref !== null)
                <a href="{{ $prevHref }}" class="{{ $arrow }}" aria-label="Previous page" rel="prev"><x-sigta::icon name="chevron-left" size="sm" /></a>
            @else
                <button type="button" class="{{ $arrow }}" aria-label="Previous page"><x-sigta::icon name="chevron-left" size="sm" /></button>
            @endif
        </li>

        @foreach ($items as $item)
            <li>
                @if ($item === '…')
                    <span class="flex h-7 min-w-7 items-center justify-center text-graphite" aria-hidden="true">…</span>
                @elseif ($item === $current)
                    <span class="{{ $pageCurrent }}" aria-current="page">{{ $item }}</span>
                @elseif ($hrefFor !== null)
                    <a href="{{ $hrefFor($item) }}" class="{{ $pageIdle }}" aria-label="Page {{ $item }}">{{ $item }}</a>
                @else
                    <button type="button" class="{{ $pageIdle }}" aria-label="Page {{ $item }}">{{ $item }}</button>
                @endif
            </li>
        @endforeach

        <li>
            @if ($atEnd)
                <span class="{{ $arrow }}" aria-disabled="true" aria-hidden="true"><x-sigta::icon name="chevron-right" size="sm" /></span>
            @elseif ($nextHref !== null)
                <a href="{{ $nextHref }}" class="{{ $arrow }}" aria-label="Next page" rel="next"><x-sigta::icon name="chevron-right" size="sm" /></a>
            @else
                <button type="button" class="{{ $arrow }}" aria-label="Next page"><x-sigta::icon name="chevron-right" size="sm" /></button>
            @endif
        </li>
    </ul>
</nav>
