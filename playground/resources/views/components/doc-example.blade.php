{{-- Docs helper: renders a live component cluster beside its escaped source.

     The source string is supplied as an <x-slot:code> and must NOT contain a
     literal "<sigta:" / "</sigta:" — Blade's prefix precompiler rewrites those
     to <x-sigta::…> even inside PHP strings. Author the source with the « sentinel
     for "<" and unwrap it at render time (see the $src helper in docs.blade.php). --}}
@props(['label' => null])

<div {{ $attributes->merge(['class' => 'grid gap-4 md:grid-cols-2 md:items-stretch']) }} data-doc-example>
    <div class="flex flex-col gap-4 rounded-panel border border-line bg-surface p-5">
        @if ($label)
            <span class="font-mono text-[0.62rem] tracking-[0.14em] uppercase text-graphite">{{ $label }}</span>
        @endif
        <div class="flex flex-1 flex-wrap content-start items-center gap-3">{{ $slot }}</div>
    </div>
    <pre class="m-0 self-stretch overflow-x-auto rounded-panel bg-inverse p-4 font-mono text-[0.76rem] leading-relaxed text-inverse-fg"><code>{{ $code ?? '' }}</code></pre>
</div>
